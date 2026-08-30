<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\MidtransPaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Request $request, Reservation $reservation, MidtransPaymentService $service): View
    {
        abort_unless($reservation->guest_id === $request->user()->id, 403);
        $reservation->load(['roomType', 'nights', 'promotion']);

        $checkout = null;
        $configurationReady = filled(config('services.midtrans.client_key')) && filled(config('services.midtrans.server_key'));
        if ($configurationReady && $reservation->status->value === 'pending_payment') {
            $checkout = $service->checkout($reservation);
        }

        return view('guest.reservations.payment', [
            'reservation' => $reservation->fresh(['roomType', 'nights', 'promotion', 'payments']),
            'checkout' => $checkout,
            'configurationReady' => $configurationReady,
            'snapUrl' => config('services.midtrans.is_production')
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js',
            'clientKey' => config('services.midtrans.client_key'),
        ]);
    }
}
