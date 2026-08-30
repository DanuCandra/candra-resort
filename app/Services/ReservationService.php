<?php

namespace App\Services;

use App\Enums\ReservationPaymentStatus;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\RoomType;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly PricingService $pricing,
    ) {}

    public function createOnline(User $guest, array $data): Reservation
    {
        return DB::transaction(function () use ($guest, $data): Reservation {
            $roomType = RoomType::query()->whereKey($data['room_type_id'])->where('is_active', true)->lockForUpdate()->firstOrFail();
            $checkIn = CarbonImmutable::parse($data['check_in']);
            $checkOut = CarbonImmutable::parse($data['check_out']);

            $this->releaseExpiredHolds($roomType);

            if ($data['adults'] > $roomType->max_adults || $data['children'] > $roomType->max_children) {
                throw ValidationException::withMessages(['adults' => 'Jumlah tamu melebihi kapasitas tipe kamar.']);
            }

            if ($this->availability->availableCount($roomType, $checkIn, $checkOut) < 1) {
                throw ValidationException::withMessages(['room_type_id' => 'Kamar tidak lagi tersedia untuk tanggal tersebut. Silakan pilih tanggal atau tipe kamar lain.']);
            }

            $quote = $this->pricing->quote($roomType, $checkIn, $checkOut, $data['promo_code'] ?? null, $guest, true);
            $reservation = Reservation::create([
                'booking_code' => $this->bookingCode(),
                'guest_id' => $guest->id,
                'room_type_id' => $roomType->id,
                'promotion_id' => $quote['promotion']?->id,
                'source' => 'online',
                'guest_name' => $guest->name,
                'guest_email' => $guest->email,
                'guest_phone' => $guest->phone,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'total_nights' => $quote['total_nights'],
                'adults' => $data['adults'],
                'children' => $data['children'],
                'status' => ReservationStatus::PendingPayment,
                'payment_status' => ReservationPaymentStatus::Unpaid,
                'currency' => 'IDR',
                'subtotal' => $quote['subtotal'],
                'discount_amount' => $quote['discount_amount'],
                'grand_total' => $quote['grand_total'],
                'promo_code_snapshot' => $quote['promotion']?->code,
                'special_requests' => $data['special_requests'] ?? null,
                'payment_due_at' => now()->addMinutes((int) config('services.midtrans.payment_expiry_minutes', 30)),
            ]);

            $reservation->nights()->createMany($quote['nights']);
            $quote['promotion']?->increment('used_count');

            return $reservation->load(['roomType', 'nights', 'promotion']);
        }, 3);
    }

    public function cancelPending(Reservation $reservation, ?User $actor = null, string $reason = 'Dibatalkan oleh tamu.'): void
    {
        DB::transaction(function () use ($reservation, $actor, $reason): void {
            $locked = Reservation::query()->whereKey($reservation->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== ReservationStatus::PendingPayment) {
                throw ValidationException::withMessages(['reservation' => 'Hanya reservasi yang menunggu pembayaran yang dapat dibatalkan dari halaman ini.']);
            }

            $locked->update([
                'status' => ReservationStatus::Cancelled,
                'cancelled_at' => now(),
                'cancelled_by' => $actor?->id,
                'cancellation_reason' => $reason,
            ]);
            if ($locked->promotion && $locked->promotion->used_count > 0) {
                $locked->promotion->decrement('used_count');
            }
            $locked->payments()->where('status', 'pending')->update(['status' => 'cancelled']);
        });
    }

    public function expirePendingReservations(): int
    {
        return DB::transaction(function (): int {
            $expired = Reservation::query()->with('promotion')
                ->where('status', ReservationStatus::PendingPayment->value)
                ->whereNotNull('payment_due_at')->where('payment_due_at', '<=', now())->lockForUpdate()->get();

            foreach ($expired as $reservation) {
                $this->expire($reservation);
            }

            return $expired->count();
        });
    }

    private function releaseExpiredHolds(RoomType $roomType): void
    {
        $expired = Reservation::query()->with('promotion')->where('room_type_id', $roomType->id)
            ->where('status', ReservationStatus::PendingPayment->value)
            ->whereNotNull('payment_due_at')->where('payment_due_at', '<=', now())->lockForUpdate()->get();

        foreach ($expired as $reservation) {
            $this->expire($reservation);
        }
    }

    private function expire(Reservation $reservation): void
    {
        $reservation->update([
            'status' => ReservationStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => 'Batas waktu pembayaran berakhir.',
        ]);
        if ($reservation->promotion && $reservation->promotion->used_count > 0) {
            $reservation->promotion->decrement('used_count');
        }
        $reservation->payments()->where('status', 'pending')->update(['status' => 'expired']);
    }

    private function bookingCode(): string
    {
        do {
            $code = 'CR-'.now()->format('ymd').'-'.Str::upper(Str::random(7));
        } while (Reservation::query()->where('booking_code', $code)->exists());

        return $code;
    }
}
