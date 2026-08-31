<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guest\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use App\Services\ReservationService;
use App\Support\AuditLogger;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

// Mengelola reservasi milik Guest.
class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $reservations = $request->user()->reservations()->with(['roomType.images', 'payments'])
            ->latest()->paginate(10);

        return view('guest.reservations.index', compact('reservations'));
    }

    public function create(Request $request, RoomType $roomType, AvailabilityService $availability, PricingService $pricing): View|RedirectResponse
    {
        abort_unless($roomType->is_active, 404);
        $validated = Validator::make($request->query(), [
            'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'adults' => ['required', 'integer', 'min:1', 'max:'.$roomType->max_adults],
            'children' => ['nullable', 'integer', 'min:0', 'max:'.$roomType->max_children],
            'promo_code' => ['nullable', 'string', 'max:100'],
        ])->validate();

        $checkIn = CarbonImmutable::parse($validated['check_in']);
        $checkOut = CarbonImmutable::parse($validated['check_out']);
        if ($availability->availableCount($roomType, $checkIn, $checkOut) < 1) {
            return redirect()->route('public.rooms.index', $request->query())->with('error', 'Tipe kamar tersebut sudah tidak tersedia pada tanggal yang dipilih.');
        }

        $quote = $pricing->quote($roomType, $checkIn, $checkOut, $validated['promo_code'] ?? null, $request->user());
        $roomType->load(['images', 'facilities']);

        return view('guest.reservations.create', compact('roomType', 'validated', 'quote'));
    }

    public function store(StoreReservationRequest $request, ReservationService $service): RedirectResponse
    {
        $reservation = $service->createOnline($request->user(), $request->validated());
        AuditLogger::record($request, 'create', 'reservations', $reservation, 'Tamu membuat reservasi '.$reservation->booking_code.'.', null, $reservation->toArray());

        return redirect()->route('guest.reservations.payment', $reservation)->with('success', 'Reservasi berhasil dibuat. Selesaikan pembayaran sebelum batas waktu.');
    }

    public function show(Request $request, Reservation $reservation): View
    {
        $this->ensureOwnership($request, $reservation);
        $reservation->load(['roomType.images', 'nights', 'promotion', 'payments.method', 'stay.room']);

        return view('guest.reservations.show', compact('reservation'));
    }

    public function cancel(Request $request, Reservation $reservation, ReservationService $service): RedirectResponse
    {
        $this->ensureOwnership($request, $reservation);
        $service->cancelPending($reservation, $request->user());
        AuditLogger::record($request, 'cancel', 'reservations', $reservation, 'Tamu membatalkan reservasi '.$reservation->booking_code.'.');

        return redirect()->route('guest.reservations.show', $reservation)->with('success', 'Reservasi berhasil dibatalkan.');
    }

    private function ensureOwnership(Request $request, Reservation $reservation): void
    {
        abort_unless($reservation->guest_id === $request->user()->id, 403);
    }
}
