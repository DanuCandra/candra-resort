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
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = today();

        return view('receptionist.dashboard', [
            'metrics' => [
                'arrivals' => Reservation::query()->whereDate('check_in_date', $today)
                    ->whereIn('status', [ReservationStatus::Paid->value, ReservationStatus::Confirmed->value])->count(),
                'departures' => Reservation::query()->whereDate('check_out_date', $today)
                    ->where('status', ReservationStatus::CheckedIn->value)->count(),
                'occupied' => Room::query()->where('status', RoomStatus::Occupied->value)->count(),
                'available' => Room::query()->where('status', RoomStatus::Available->value)->where('is_active', true)->count(),
                'cleaning' => Room::query()->where('status', RoomStatus::Cleaning->value)->count(),
                'pending_payments' => Payment::query()->where('status', PaymentStatus::Pending->value)->count(),
                'guest_requests' => GuestRequest::query()->whereNotIn('status', ['completed', 'cancelled'])->count(),
                'food_orders' => FoodOrder::query()->whereNotIn('status', ['completed', 'cancelled'])->count(),
            ],
            'arrivals' => Reservation::query()->with('roomType')->whereDate('check_in_date', $today)
                ->whereIn('status', [ReservationStatus::Paid->value, ReservationStatus::Confirmed->value])->limit(6)->get(),
            'departures' => Reservation::query()->with(['roomType', 'room'])->whereDate('check_out_date', $today)
                ->where('status', ReservationStatus::CheckedIn->value)->limit(6)->get(),
            'recentRequests' => GuestRequest::query()->with('room')->whereNotIn('status', ['completed', 'cancelled'])
                ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
                ->latest('requested_at')->limit(6)->get(),
        ]);
    }
}
