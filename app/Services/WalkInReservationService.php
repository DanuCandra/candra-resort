<?php

namespace App\Services;

use App\Enums\ReservationPaymentStatus;
use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomStatusHistory;
use App\Models\RoomType;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WalkInReservationService
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly PricingService $pricing,
        private readonly ManualPaymentService $payments,
    ) {}

    public function create(User $receptionist, array $data): Reservation
    {
        return DB::transaction(function () use ($receptionist, $data): Reservation {
            $room = Room::query()->with('roomType')->whereKey($data['room_id'])->lockForUpdate()->firstOrFail();
            $roomType = RoomType::query()->whereKey($room->room_type_id)->lockForUpdate()->firstOrFail();
            $checkIn = CarbonImmutable::parse($data['check_in']);
            $checkOut = CarbonImmutable::parse($data['check_out']);

            if (! $this->availability->roomIsAvailable($room, $checkIn, $checkOut)) {
                throw ValidationException::withMessages(['room_id' => 'Kamar fisik tersebut tidak tersedia pada tanggal yang dipilih.']);
            }
            if ($data['adults'] > $roomType->max_adults || $data['children'] > $roomType->max_children) {
                throw ValidationException::withMessages(['adults' => 'Jumlah tamu melebihi kapasitas tipe kamar.']);
            }

            $quote = $this->pricing->quote($roomType, $checkIn, $checkOut, $data['promo_code'] ?? null, null, true);
            $reservation = Reservation::create([
                'booking_code' => 'WI-'.now()->format('ymd').'-'.Str::upper(Str::random(7)),
                'created_by' => $receptionist->id,
                'room_type_id' => $roomType->id,
                'room_id' => $room->id,
                'promotion_id' => $quote['promotion']?->id,
                'source' => 'walk_in',
                'guest_name' => $data['guest_name'],
                'guest_email' => $data['guest_email'] ?? null,
                'guest_phone' => $data['guest_phone'],
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'total_nights' => $quote['total_nights'],
                'adults' => $data['adults'],
                'children' => $data['children'],
                'status' => ReservationStatus::Confirmed,
                'payment_status' => ReservationPaymentStatus::Unpaid,
                'currency' => 'IDR',
                'subtotal' => $quote['subtotal'],
                'discount_amount' => $quote['discount_amount'],
                'grand_total' => $quote['grand_total'],
                'promo_code_snapshot' => $quote['promotion']?->code,
                'special_requests' => $data['special_requests'] ?? null,
                'confirmed_at' => now(),
            ]);
            $reservation->nights()->createMany($quote['nights']);
            $quote['promotion']?->increment('used_count');

            if ($checkIn->isToday() && $room->status === RoomStatus::Available) {
                $room->update(['status' => RoomStatus::Reserved]);
                RoomStatusHistory::create([
                    'room_id' => $room->id,
                    'old_status' => RoomStatus::Available,
                    'new_status' => RoomStatus::Reserved,
                    'changed_by' => $receptionist->id,
                    'reason' => 'Reservasi walk-in '.$reservation->booking_code,
                    'changed_at' => now(),
                ]);
            }

            if (($data['payment_amount'] ?? 0) > 0) {
                $method = PaymentMethod::query()->whereKey($data['payment_method_id'])->lockForUpdate()->firstOrFail();
                $this->payments->record(
                    $reservation,
                    $method,
                    (int) round((float) $data['payment_amount']),
                    $receptionist,
                    purpose: 'reservation',
                    reference: $data['reference_number'] ?? null,
                    notes: 'Pembayaran saat pembuatan reservasi walk-in.',
                );
            }

            return $reservation->fresh(['roomType', 'room', 'nights', 'payments']);
        }, 3);
    }
}
