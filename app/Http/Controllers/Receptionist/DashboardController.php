<?php

namespace App\Http\Controllers\Receptionist;

use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\FoodOrder;
use App\Models\GuestRequest;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\ServiceOrder;
use App\Models\Stay;
use Illuminate\View\View;

// Menampilkan pusat operasional Receptionist.
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = today();
        $roomStatusCounts = Room::query()
            ->where('is_active', true)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $activeRoomCount = (int) $roomStatusCounts->sum();
        $occupiedRoomCount = (int) $roomStatusCounts->get(RoomStatus::Occupied->value, 0);
        $pendingPaymentQuery = Payment::query()->where('status', PaymentStatus::Pending->value);
        $activeGuestRequestQuery = GuestRequest::query()->whereNotIn('status', ['completed', 'cancelled']);
        $activeFoodOrderQuery = FoodOrder::query()->whereNotIn('status', ['completed', 'cancelled']);
        $activeServiceOrderQuery = ServiceOrder::query()->whereNotIn('status', ['completed', 'cancelled']);
        $arrivalCount = Reservation::query()->whereDate('check_in_date', $today)
            ->whereIn('status', [ReservationStatus::Paid->value, ReservationStatus::Confirmed->value])->count();
        $departureCount = Reservation::query()->whereDate('check_out_date', $today)
            ->where('status', ReservationStatus::CheckedIn->value)->count();
        $guestRequestCount = (clone $activeGuestRequestQuery)->count();
        $foodOrderCount = (clone $activeFoodOrderQuery)->count();
        $serviceOrderCount = (clone $activeServiceOrderQuery)->count();
        $pendingPaymentCount = (clone $pendingPaymentQuery)->count();

        return view('receptionist.dashboard', [
            'metrics' => [
                'arrivals' => $arrivalCount,
                'departures' => $departureCount,
                'occupied' => $occupiedRoomCount,
                'available' => (int) $roomStatusCounts->get(RoomStatus::Available->value, 0),
                'reserved' => (int) $roomStatusCounts->get(RoomStatus::Reserved->value, 0),
                'cleaning' => (int) $roomStatusCounts->get(RoomStatus::Cleaning->value, 0),
                'maintenance' => (int) $roomStatusCounts->get(RoomStatus::Maintenance->value, 0),
                'unavailable' => (int) $roomStatusCounts->get(RoomStatus::Unavailable->value, 0),
                'active_rooms' => $activeRoomCount,
                'occupancy_rate' => $activeRoomCount > 0 ? round(($occupiedRoomCount / $activeRoomCount) * 100) : 0,
                'pending_payments' => $pendingPaymentCount,
                'pending_payment_amount' => (float) (clone $pendingPaymentQuery)->sum('amount'),
                'guest_requests' => $guestRequestCount,
                'urgent_requests' => (clone $activeGuestRequestQuery)->where('priority', 'urgent')->count(),
                'food_orders' => $foodOrderCount,
                'service_orders' => $serviceOrderCount,
                'checked_in_today' => Stay::query()->whereDate('check_in_at', $today)->count(),
                'checked_out_today' => Stay::query()->whereDate('check_out_at', $today)->count(),
                'paid_today' => (float) Payment::query()->where('status', PaymentStatus::Paid->value)
                    ->whereDate('paid_at', $today)->sum('amount'),
            ],
            'arrivals' => Reservation::query()->with(['roomType:id,name', 'room:id,room_number'])->whereDate('check_in_date', $today)
                ->whereIn('status', [ReservationStatus::Paid->value, ReservationStatus::Confirmed->value])
                ->orderBy('estimated_arrival_time')->limit(6)->get(),
            'departures' => Reservation::query()->with(['roomType', 'room'])->whereDate('check_out_date', $today)
                ->where('status', ReservationStatus::CheckedIn->value)->limit(6)->get(),
            'recentRequests' => GuestRequest::query()->with(['room:id,room_number', 'stay:id,guest_name'])->whereNotIn('status', ['completed', 'cancelled'])
                ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
                ->latest('requested_at')->limit(6)->get(),
            'recentFoodOrders' => FoodOrder::query()->with(['room:id,room_number', 'stay:id,guest_name'])
                ->withCount('items')->whereNotIn('status', ['completed', 'cancelled'])
                ->orderByRaw("CASE status WHEN 'requested' THEN 1 WHEN 'accepted' THEN 2 WHEN 'processing' THEN 3 ELSE 4 END")
                ->latest('ordered_at')->limit(5)->get(),
            'recentServiceOrders' => ServiceOrder::query()->with(['room:id,room_number', 'stay:id,guest_name', 'service:id,name'])
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->orderByRaw("CASE status WHEN 'requested' THEN 1 WHEN 'accepted' THEN 2 WHEN 'scheduled' THEN 3 WHEN 'processing' THEN 4 ELSE 5 END")
                ->latest()->limit(5)->get(),
            'pendingPayments' => (clone $pendingPaymentQuery)->with(['reservation:id,booking_code,guest_name', 'method:id,name'])
                ->latest()->limit(5)->get(),
        ]);
    }
}
