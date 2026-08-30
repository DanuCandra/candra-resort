<?php

namespace App\Http\Controllers\Guest;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $reservations = $request->user()->reservations()
            ->with(['roomType.images', 'stay.room'])
            ->latest()
            ->limit(5)
            ->get();

        return view('guest.dashboard', [
            'reservations' => $reservations,
            'activeReservationCount' => $request->user()->reservations()
                ->whereIn('status', [
                    ReservationStatus::PendingPayment->value,
                    ReservationStatus::Paid->value,
                    ReservationStatus::Confirmed->value,
                    ReservationStatus::CheckedIn->value,
                ])->count(),
        ]);
    }
}
