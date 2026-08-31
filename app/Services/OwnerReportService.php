<?php

namespace App\Services;

use App\Enums\FoodOrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Enums\ServiceOrderStatus;
use App\Enums\StayStatus;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\ServiceOrder;
use App\Models\Stay;
use App\Support\ReportPeriod;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

// Menghitung metrik dan laporan bisnis Owner.
class OwnerReportService
{
    /** @return array<string, int|float> */
    public function dashboardMetrics(ReportPeriod $period): array
    {
        $reservations = Reservation::query()->whereBetween('created_at', $period->bounds());
        $paidPayments = Payment::query()
            ->where('status', PaymentStatus::Paid->value)
            ->whereBetween('paid_at', $period->bounds());
        $occupancy = $this->occupancy($period);

        return [
            'reservations' => (clone $reservations)->count(),
            'booking_value' => (float) (clone $reservations)->sum('grand_total'),
            'revenue' => (float) $paidPayments->sum('amount'),
            'cancelled' => (clone $reservations)->where('status', ReservationStatus::Cancelled->value)->count(),
            'average_stay' => round((float) (clone $reservations)
                ->whereIn('status', [ReservationStatus::CheckedIn->value, ReservationStatus::CheckedOut->value])
                ->avg('total_nights'), 1),
            'occupancy_rate' => $occupancy['rate'],
            'occupied_room_nights' => $occupancy['occupied_room_nights'],
            'available_room_nights' => max(0, $occupancy['capacity_room_nights'] - $occupancy['occupied_room_nights']),
            'active_rooms' => Room::query()->where('is_active', true)->count(),
        ];
    }

    /** @return array{occupied_room_nights: int, capacity_room_nights: int, rate: float, daily: array<int, array{date: CarbonImmutable, occupied: int, capacity: int, rate: float}>} */
    public function occupancy(ReportPeriod $period): array
    {
        $reportEnd = $period->end->min(CarbonImmutable::today()->endOfDay());
        $activeRooms = Room::query()->where('is_active', true)->count();

        if ($period->start->greaterThan($reportEnd)) {
            return [
                'occupied_room_nights' => 0,
                'capacity_room_nights' => 0,
                'rate' => 0,
                'daily' => [],
            ];
        }

        $stays = Stay::query()
            ->where('status', '!=', StayStatus::Cancelled->value)
            ->whereNotNull('check_in_at')
            ->where('check_in_at', '<=', $reportEnd)
            ->where(function (Builder $query) use ($period): void {
                $query->whereNull('check_out_at')->orWhere('check_out_at', '>', $period->start);
            })
            ->get(['check_in_at', 'check_out_at']);

        $daily = [];
        $occupiedRoomNights = 0;

        for ($day = $period->start->startOfDay(); $day->lessThanOrEqualTo($reportEnd); $day = $day->addDay()) {
            $nextDay = $day->addDay();
            $occupied = $stays->filter(function (Stay $stay) use ($day, $nextDay): bool {
                return $stay->check_in_at->lessThan($nextDay)
                    && ($stay->check_out_at === null || $stay->check_out_at->greaterThan($day));
            })->count();

            $occupiedRoomNights += $occupied;
            $daily[] = [
                'date' => $day,
                'occupied' => $occupied,
                'capacity' => $activeRooms,
                'rate' => $activeRooms > 0 ? round(($occupied / $activeRooms) * 100, 1) : 0,
            ];
        }

        $capacityRoomNights = count($daily) * $activeRooms;

        return [
            'occupied_room_nights' => $occupiedRoomNights,
            'capacity_room_nights' => $capacityRoomNights,
            'rate' => $capacityRoomNights > 0 ? round(($occupiedRoomNights / $capacityRoomNights) * 100, 1) : 0,
            'daily' => $daily,
        ];
    }

    /** @return array{labels: array<int, string>, revenue: array<int, float>, reservations: array<int, int>} */
    public function trend(ReportPeriod $period): array
    {
        $useMonths = $period->start->diffInDays($period->end) > 45;
        $cursor = $useMonths ? $period->start->startOfMonth() : $period->start->startOfDay();
        $last = $useMonths ? $period->end->startOfMonth() : $period->end->startOfDay();
        $buckets = [];

        while ($cursor->lessThanOrEqualTo($last)) {
            $key = $useMonths ? $cursor->format('Y-m') : $cursor->format('Y-m-d');
            $buckets[$key] = [
                'label' => $useMonths ? $cursor->translatedFormat('M Y') : $cursor->translatedFormat('d M'),
                'revenue' => 0.0,
                'reservations' => 0,
            ];
            $cursor = $useMonths ? $cursor->addMonth() : $cursor->addDay();
        }

        Payment::query()->where('status', PaymentStatus::Paid->value)
            ->whereBetween('paid_at', $period->bounds())->get(['amount', 'paid_at'])
            ->each(function (Payment $payment) use (&$buckets, $useMonths): void {
                $key = $payment->paid_at->format($useMonths ? 'Y-m' : 'Y-m-d');
                if (isset($buckets[$key])) {
                    $buckets[$key]['revenue'] += (float) $payment->amount;
                }
            });

        Reservation::query()->whereBetween('created_at', $period->bounds())->get(['created_at'])
            ->each(function (Reservation $reservation) use (&$buckets, $useMonths): void {
                $key = $reservation->created_at->format($useMonths ? 'Y-m' : 'Y-m-d');
                if (isset($buckets[$key])) {
                    $buckets[$key]['reservations']++;
                }
            });

        return [
            'labels' => array_column($buckets, 'label'),
            'revenue' => array_column($buckets, 'revenue'),
            'reservations' => array_column($buckets, 'reservations'),
        ];
    }

    /** @return Collection<int, array{label: string, count: int, amount: float}> */
    public function serviceSummary(ReportPeriod $period): Collection
    {
        $food = FoodOrder::query()
            ->where('status', FoodOrderStatus::Completed->value)
            ->whereBetween('completed_at', $period->bounds())
            ->selectRaw("'food' as service_key, COUNT(*) as aggregate_count, COALESCE(SUM(total_amount), 0) as aggregate_amount")
            ->first();

        $services = ServiceOrder::query()
            ->with('service:id,name')
            ->where('status', ServiceOrderStatus::Completed->value)
            ->whereBetween('completed_at', $period->bounds())
            ->get()
            ->groupBy(fn (ServiceOrder $order): string => $order->service?->name ?? 'Layanan lainnya')
            ->map(fn (Collection $orders, string $label): array => [
                'label' => $label,
                'count' => $orders->count(),
                'amount' => (float) $orders->sum('total_amount'),
            ])->values();

        return collect([[
            'label' => 'Makanan & Minuman',
            'count' => (int) ($food?->aggregate_count ?? 0),
            'amount' => (float) ($food?->aggregate_amount ?? 0),
        ]])->concat($services);
    }

    /** @return Collection<int, array{month: string, reservations: int, cancelled: int, revenue: float, occupied_room_nights: int, capacity_room_nights: int, occupancy_rate: float}> */
    public function monthlySummary(ReportPeriod $period): Collection
    {
        $rows = collect();
        $cursor = $period->start->startOfMonth();

        while ($cursor->lessThanOrEqualTo($period->end->startOfMonth())) {
            $key = $cursor->format('Y-m');
            $rows->put($key, [
                'month' => $cursor->translatedFormat('F Y'),
                'reservations' => 0,
                'cancelled' => 0,
                'revenue' => 0.0,
                'occupied_room_nights' => 0,
                'capacity_room_nights' => 0,
                'occupancy_rate' => 0.0,
            ]);
            $cursor = $cursor->addMonth();
        }

        Reservation::query()->whereBetween('created_at', $period->bounds())->get(['status', 'created_at'])
            ->each(function (Reservation $reservation) use ($rows): void {
                $key = $reservation->created_at->format('Y-m');
                if (! $rows->has($key)) {
                    return;
                }

                $row = $rows->get($key);
                $row['reservations']++;
                if ($reservation->status === ReservationStatus::Cancelled) {
                    $row['cancelled']++;
                }
                $rows->put($key, $row);
            });

        Payment::query()->where('status', PaymentStatus::Paid->value)
            ->whereBetween('paid_at', $period->bounds())->get(['amount', 'paid_at'])
            ->each(function (Payment $payment) use ($rows): void {
                $key = $payment->paid_at->format('Y-m');
                if (! $rows->has($key)) {
                    return;
                }

                $row = $rows->get($key);
                $row['revenue'] += (float) $payment->amount;
                $rows->put($key, $row);
            });

        collect($this->occupancy($period)['daily'])->groupBy(fn (array $day): string => $day['date']->format('Y-m'))
            ->each(function (Collection $days, string $key) use ($rows): void {
                if (! $rows->has($key)) {
                    return;
                }

                $row = $rows->get($key);
                $row['occupied_room_nights'] = (int) $days->sum('occupied');
                $row['capacity_room_nights'] = (int) $days->sum('capacity');
                $row['occupancy_rate'] = $row['capacity_room_nights'] > 0
                    ? round(($row['occupied_room_nights'] / $row['capacity_room_nights']) * 100, 1)
                    : 0;
                $rows->put($key, $row);
            });

        return $rows->values();
    }
}
