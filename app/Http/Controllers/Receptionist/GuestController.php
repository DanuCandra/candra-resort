<?php

namespace App\Http\Controllers\Receptionist;

use App\Enums\StayStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class GuestController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:registered_only,has_stayed,active'],
            'source' => ['nullable', 'in:account,walk_in'],
        ]);

        $walkInStayGroups = Stay::query()
            ->whereNull('guest_id')
            ->with(['reservation:id,guest_email,source', 'room:id,room_number'])
            ->latest('check_in_at')
            ->get()
            ->groupBy('guest_phone');

        $registeredGuests = User::query()->withTrashed()
            ->where('role', UserRole::Guest->value)
            ->withCount('stays')
            ->withMax('stays', 'check_in_at')
            ->withExists(['stays as has_active_stay' => fn ($query) => $query->where('status', StayStatus::Active->value)])
            ->get()
            ->map(function (User $guest) use ($walkInStayGroups): array {
                $matchingWalkIns = filled($guest->phone) ? $walkInStayGroups->get($guest->phone, collect()) : collect();
                $linkedLastCheckIn = $guest->stays_max_check_in_at ? now()->parse($guest->stays_max_check_in_at) : null;
                $walkInLastCheckIn = $matchingWalkIns->first()?->check_in_at;

                return [
                    'type' => 'account',
                    'id' => $guest->id,
                    'name' => $guest->name,
                    'phone' => $guest->phone,
                    'email' => $guest->email,
                    'checkin_count' => (int) $guest->stays_count + $matchingWalkIns->count(),
                    'last_check_in_at' => collect([$linkedLastCheckIn, $walkInLastCheckIn])->filter()->max(),
                    'has_active_stay' => (bool) $guest->has_active_stay
                        || $matchingWalkIns->contains(fn (Stay $stay): bool => $stay->status === StayStatus::Active),
                    'registered_at' => $guest->created_at,
                    'is_active_account' => $guest->deleted_at === null && $guest->is_active,
                ];
            });

        $registeredPhones = $registeredGuests->pluck('phone')->filter()->flip();
        $walkInGuests = $walkInStayGroups
            ->reject(fn (Collection $stays, string $phone): bool => $registeredPhones->has($phone))
            ->map(function (Collection $stays): array {
                /** @var Stay $latest */
                $latest = $stays->first();
                $latestWithEmail = $stays->first(fn (Stay $stay): bool => filled($stay->reservation?->guest_email));

                return [
                    'type' => 'walk_in',
                    'id' => $latest->id,
                    'name' => $latest->guest_name,
                    'phone' => $latest->guest_phone,
                    'email' => $latestWithEmail?->reservation?->guest_email,
                    'checkin_count' => $stays->count(),
                    'last_check_in_at' => $latest->check_in_at,
                    'has_active_stay' => $stays->contains(fn (Stay $stay): bool => $stay->status === StayStatus::Active),
                    'registered_at' => null,
                    'is_active_account' => false,
                ];
            })->values();

        $allGuests = $registeredGuests->concat($walkInGuests);
        $metrics = [
            'total' => $allGuests->count(),
            'accounts' => $registeredGuests->count(),
            'has_stayed' => $allGuests->where('checkin_count', '>', 0)->count(),
            'active' => $allGuests->where('has_active_stay', true)->count(),
            'walk_in' => $walkInGuests->count(),
        ];

        $guests = $allGuests
            ->when(filled($filters['search'] ?? null), function (Collection $items) use ($filters): Collection {
                $needle = str($filters['search'])->lower()->toString();

                return $items->filter(fn (array $guest): bool => str($guest['name'])->lower()->contains($needle)
                    || str($guest['phone'] ?? '')->lower()->contains($needle)
                    || str($guest['email'] ?? '')->lower()->contains($needle));
            })
            ->when(($filters['source'] ?? null) === 'account', fn (Collection $items): Collection => $items->where('type', 'account'))
            ->when(($filters['source'] ?? null) === 'walk_in', fn (Collection $items): Collection => $items->where('type', 'walk_in'))
            ->when(($filters['status'] ?? null) === 'registered_only', fn (Collection $items): Collection => $items->where('type', 'account')->where('checkin_count', 0))
            ->when(($filters['status'] ?? null) === 'has_stayed', fn (Collection $items): Collection => $items->where('checkin_count', '>', 0))
            ->when(($filters['status'] ?? null) === 'active', fn (Collection $items): Collection => $items->where('has_active_stay', true))
            ->sortByDesc(fn (array $guest): string => ($guest['has_active_stay'] ? '2-' : '1-').($guest['last_check_in_at'] ?? $guest['registered_at'])?->format('Y-m-d H:i:s'))
            ->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginatedGuests = new LengthAwarePaginator(
            $guests->forPage($page, 15)->values(),
            $guests->count(),
            15,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('receptionist.guests.index', [
            'guests' => $paginatedGuests,
            'metrics' => $metrics,
        ]);
    }

    public function showAccount(int $guest): View
    {
        $guestAccount = User::query()->withTrashed()
            ->where('role', UserRole::Guest->value)
            ->findOrFail($guest);

        $stayQuery = Stay::query()
            ->where(function ($query) use ($guestAccount): void {
                $query->where('guest_id', $guestAccount->id);
                if (filled($guestAccount->phone)) {
                    $query->orWhere(function ($walkInQuery) use ($guestAccount): void {
                        $walkInQuery->whereNull('guest_id')->where('guest_phone', $guestAccount->phone);
                    });
                }
            });
        $reservationQuery = Reservation::query()
            ->where(function ($query) use ($guestAccount, $stayQuery): void {
                $query->where('guest_id', $guestAccount->id)
                    ->orWhereIn('id', (clone $stayQuery)->select('reservation_id'));
            });
        $guestMetrics = [
            'reservations' => (clone $reservationQuery)->count(),
            'stays' => (clone $stayQuery)->count(),
            'nights' => (int) (clone $reservationQuery)->sum('total_nights'),
            'paid' => (float) Payment::query()
                ->where('status', 'paid')
                ->whereIn('reservation_id', (clone $reservationQuery)->select('id'))
                ->sum('amount'),
        ];
        $activeStay = (clone $stayQuery)
            ->where('status', StayStatus::Active->value)
            ->with('room:id,room_number')
            ->latest('check_in_at')
            ->first();
        $stays = $stayQuery
            ->with(['reservation:id,booking_code,guest_email,check_in_date,check_out_date,total_nights', 'room:id,room_number'])
            ->latest('check_in_at')
            ->paginate(10, ['*'], 'stays_page')
            ->withQueryString();
        $reservations = $reservationQuery
            ->with(['roomType:id,name', 'room:id,room_number', 'stay:id,reservation_id,status,check_in_at,check_out_at'])
            ->withSum(['payments as paid_total' => fn ($query) => $query->where('status', 'paid')], 'amount')
            ->latest()
            ->paginate(10, ['*'], 'reservations_page')
            ->withQueryString();

        return view('receptionist.guests.show', [
            'guestType' => 'account',
            'guest' => [
                'name' => $guestAccount->name,
                'phone' => $guestAccount->phone,
                'email' => $guestAccount->email,
                'registered_at' => $guestAccount->created_at,
                'is_active_account' => $guestAccount->deleted_at === null && $guestAccount->is_active,
            ],
            'reservations' => $reservations,
            'stays' => $stays,
            'guestMetrics' => $guestMetrics,
            'activeStay' => $activeStay,
        ]);
    }

    public function showWalkIn(Stay $stay): View
    {
        abort_if($stay->guest_id !== null, 404);

        $stayQuery = Stay::query()
            ->whereNull('guest_id')
            ->where('guest_phone', $stay->guest_phone);
        $reservationQuery = Reservation::query()
            ->whereIn('id', (clone $stayQuery)->select('reservation_id'));
        $guestMetrics = [
            'reservations' => (clone $reservationQuery)->count(),
            'stays' => (clone $stayQuery)->count(),
            'nights' => (int) (clone $reservationQuery)->sum('total_nights'),
            'paid' => (float) Payment::query()
                ->where('status', 'paid')
                ->whereIn('reservation_id', (clone $reservationQuery)->select('id'))
                ->sum('amount'),
        ];
        $activeStay = (clone $stayQuery)
            ->where('status', StayStatus::Active->value)
            ->with('room:id,room_number')
            ->latest('check_in_at')
            ->first();
        $latestWithEmail = Reservation::query()
            ->whereIn('id', (clone $stayQuery)->select('reservation_id'))
            ->whereNotNull('guest_email')
            ->where('guest_email', '!=', '')
            ->latest()
            ->value('guest_email');
        $stays = $stayQuery
            ->with(['reservation:id,booking_code,guest_email,check_in_date,check_out_date,total_nights', 'room:id,room_number'])
            ->latest('check_in_at')
            ->paginate(10, ['*'], 'stays_page')
            ->withQueryString();
        $reservations = $reservationQuery
            ->with(['roomType:id,name', 'room:id,room_number', 'stay:id,reservation_id,status,check_in_at,check_out_at'])
            ->withSum(['payments as paid_total' => fn ($query) => $query->where('status', 'paid')], 'amount')
            ->latest()
            ->paginate(10, ['*'], 'reservations_page')
            ->withQueryString();

        return view('receptionist.guests.show', [
            'guestType' => 'walk_in',
            'guest' => [
                'name' => $stay->guest_name,
                'phone' => $stay->guest_phone,
                'email' => $latestWithEmail,
                'registered_at' => null,
                'is_active_account' => false,
            ],
            'reservations' => $reservations,
            'stays' => $stays,
            'guestMetrics' => $guestMetrics,
            'activeStay' => $activeStay,
        ]);
    }
}
