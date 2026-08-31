<?php

namespace App\Services;

use App\Contracts\MidtransGateway;
use App\Enums\PaymentStatus;
use App\Enums\ReservationPaymentStatus;
use App\Enums\ReservationStatus;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

// Mengelola transaksi dan notifikasi pembayaran Midtrans.
class MidtransPaymentService
{
    public function __construct(private readonly MidtransGateway $gateway) {}

    public function checkout(Reservation $reservation): array
    {
        if ($reservation->payment_status === ReservationPaymentStatus::Paid) {
            throw ValidationException::withMessages(['payment' => 'Reservasi ini sudah lunas.']);
        }

        if ($reservation->status !== ReservationStatus::PendingPayment || ($reservation->payment_due_at && $reservation->payment_due_at->isPast())) {
            throw ValidationException::withMessages(['payment' => 'Reservasi tidak lagi dapat dibayar.']);
        }

        $existing = $reservation->payments()->where('status', PaymentStatus::Pending->value)
            ->whereNotNull('midtrans_response')->latest()->first();
        $existingToken = $existing?->midtrans_response['snap_token'] ?? null;
        if ($existing && $existingToken) {
            return ['payment' => $existing, 'snap_token' => $existingToken];
        }

        $method = PaymentMethod::query()->where('code', 'midtrans')->where('channel', 'midtrans')->where('is_active', true)->firstOrFail();
        $payment = Payment::create([
            'payment_code' => 'PAY-'.Str::upper((string) Str::ulid()),
            'reservation_id' => $reservation->id,
            'payment_method_id' => $method->id,
            'purpose' => 'reservation',
            'status' => PaymentStatus::Pending,
            'source' => 'guest',
            'currency' => 'IDR',
            'amount' => (int) round((float) $reservation->grand_total),
            'midtrans_order_id' => 'MID-'.$reservation->booking_code.'-'.Str::upper(Str::random(5)),
        ]);

        try {
            $snapToken = $this->gateway->createSnapToken($this->parameters($reservation, $payment));
            $payment->update(['midtrans_response' => ['snap_token' => $snapToken]]);
        } catch (\Throwable $exception) {
            $payment->update(['status' => PaymentStatus::Failed, 'notes' => 'Gagal membuat transaksi pembayaran.']);
            report($exception);
            throw ValidationException::withMessages(['payment' => 'Koneksi ke Midtrans gagal. Silakan coba beberapa saat lagi.']);
        }

        return ['payment' => $payment, 'snap_token' => $snapToken];
    }

    public function handleNotification(array $payload): Payment
    {
        foreach (['order_id', 'status_code', 'gross_amount', 'signature_key', 'transaction_status'] as $field) {
            if (! isset($payload[$field])) {
                throw ValidationException::withMessages(['notification' => 'Payload notifikasi Midtrans tidak lengkap.']);
            }
        }

        $serverKey = (string) config('services.midtrans.server_key');
        $signature = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].$serverKey);
        if ($serverKey === '' || ! hash_equals($signature, (string) $payload['signature_key'])) {
            throw ValidationException::withMessages(['notification' => 'Signature notifikasi Midtrans tidak valid.']);
        }

        return DB::transaction(function () use ($payload): Payment {
            $payment = Payment::query()->where('midtrans_order_id', $payload['order_id'])->lockForUpdate()->firstOrFail();
            $expectedAmount = (int) round((float) $payment->amount);
            $receivedAmount = (int) round((float) $payload['gross_amount']);
            if ($expectedAmount !== $receivedAmount) {
                throw ValidationException::withMessages(['notification' => 'Nominal notifikasi tidak sesuai.']);
            }

            $newStatus = $this->mapStatus((string) $payload['transaction_status'], $payload['fraud_status'] ?? null);
            if ($newStatus === PaymentStatus::Paid && (string) $payload['status_code'] !== '200') {
                $newStatus = PaymentStatus::Failed;
            }
            $wasPaid = $payment->status === PaymentStatus::Paid;
            $safePayload = collect($payload)->except('signature_key')->all();
            $storedResponse = $payment->midtrans_response ?? [];
            $payment->update([
                'status' => $newStatus,
                'midtrans_transaction_id' => filled($payload['transaction_id'] ?? null) ? $payload['transaction_id'] : $payment->midtrans_transaction_id,
                'midtrans_payment_type' => $payload['payment_type'] ?? null,
                'midtrans_transaction_status' => $payload['transaction_status'],
                'midtrans_fraud_status' => $payload['fraud_status'] ?? null,
                'midtrans_bank' => $payload['va_numbers'][0]['bank'] ?? ($payload['bank'] ?? null),
                'midtrans_va_number' => $payload['va_numbers'][0]['va_number'] ?? null,
                'midtrans_response' => [...$storedResponse, 'notification' => $safePayload],
                'paid_at' => $newStatus === PaymentStatus::Paid ? ($payment->paid_at ?? now()) : $payment->paid_at,
            ]);

            $reservation = $payment->reservation()->lockForUpdate()->first();
            if ($reservation && $newStatus === PaymentStatus::Paid && ! $wasPaid) {
                $reservation->update([
                    'status' => ReservationStatus::Paid,
                    'payment_status' => ReservationPaymentStatus::Paid,
                    'confirmed_at' => now(),
                ]);
            } elseif ($reservation && in_array($newStatus, [PaymentStatus::Cancelled, PaymentStatus::Expired], true) && $reservation->status === ReservationStatus::PendingPayment) {
                $reservation->update([
                    'status' => ReservationStatus::Cancelled,
                    'cancelled_at' => now(),
                    'cancellation_reason' => 'Pembayaran Midtrans '.$newStatus->value.'.',
                ]);
                if ($reservation->promotion && $reservation->promotion->used_count > 0) {
                    $reservation->promotion->decrement('used_count');
                }
            }

            return $payment->fresh();
        });
    }

    private function parameters(Reservation $reservation, Payment $payment): array
    {
        return [
            'transaction_details' => [
                'order_id' => $payment->midtrans_order_id,
                'gross_amount' => (int) round((float) $payment->amount),
            ],
            'item_details' => [[
                'id' => $reservation->room_type_id,
                'price' => (int) round((float) $payment->amount),
                'quantity' => 1,
                'name' => Str::limit('Reservasi '.$reservation->roomType->name.' '.$reservation->total_nights.' malam', 50, ''),
            ]],
            'customer_details' => [
                'first_name' => $reservation->guest_name,
                'email' => $reservation->guest_email,
                'phone' => $reservation->guest_phone,
            ],
            'expiry' => [
                'duration' => (int) max(1, now()->diffInMinutes($reservation->payment_due_at, false)),
                'unit' => 'minutes',
            ],
        ];
    }

    private function mapStatus(string $transactionStatus, ?string $fraudStatus): PaymentStatus
    {
        return match ($transactionStatus) {
            'settlement' => PaymentStatus::Paid,
            'capture' => $fraudStatus === null || strtolower($fraudStatus) === 'accept' ? PaymentStatus::Paid : PaymentStatus::Failed,
            'pending' => PaymentStatus::Pending,
            'expire' => PaymentStatus::Expired,
            'cancel' => PaymentStatus::Cancelled,
            'refund', 'partial_refund' => PaymentStatus::Refunded,
            default => PaymentStatus::Failed,
        };
    }
}
