<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\ReservationPaymentStatus;
use App\Models\Folio;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ManualPaymentService
{
    public function record(
        Reservation $reservation,
        PaymentMethod $method,
        int $amount,
        User $receptionist,
        ?Stay $stay = null,
        ?Folio $folio = null,
        string $purpose = 'reservation',
        ?string $reference = null,
        ?string $notes = null,
    ): Payment {
        if ($amount <= 0 || ! $method->is_active || $method->channel !== 'manual') {
            throw ValidationException::withMessages(['payment_amount' => 'Metode atau nominal pembayaran manual tidak valid.']);
        }

        $payment = Payment::create([
            'payment_code' => 'PAY-'.Str::upper((string) Str::ulid()),
            'reservation_id' => $reservation->id,
            'stay_id' => $stay?->id,
            'folio_id' => $folio?->id,
            'payment_method_id' => $method->id,
            'received_by' => $receptionist->id,
            'purpose' => $purpose,
            'status' => PaymentStatus::Paid,
            'source' => 'receptionist',
            'currency' => 'IDR',
            'amount' => $amount,
            'reference_number' => $reference,
            'paid_at' => now(),
            'notes' => $notes,
        ]);

        $paidAmount = (int) round((float) $reservation->payments()->where('status', PaymentStatus::Paid->value)->sum('amount'));
        $reservation->update([
            'payment_status' => $paidAmount >= (int) round((float) $reservation->grand_total)
                ? ReservationPaymentStatus::Paid
                : ReservationPaymentStatus::Partial,
        ]);

        if ($folio) {
            $folioPaid = (int) round((float) $folio->payments()->where('status', PaymentStatus::Paid->value)->sum('amount'));
            $folio->update([
                'paid_amount' => $folioPaid,
                'balance_amount' => max(0, (int) round((float) $folio->total_amount) - $folioPaid),
            ]);
        }

        return $payment;
    }
}
