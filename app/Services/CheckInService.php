<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Enums\StayStatus;
use App\Models\Folio;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomStatusHistory;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

// Menjalankan seluruh aturan bisnis check-in.
class CheckInService
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly ManualPaymentService $payments,
    ) {}

    public function checkIn(Reservation $reservation, User $receptionist, array $data, UploadedFile $identityPhoto): Stay
    {
        $identityPath = $identityPhoto->store('guest-identities/'.now()->format('Y/m'), 'local');

        try {
            return DB::transaction(function () use ($reservation, $receptionist, $data, $identityPath): Stay {
                $reservation = Reservation::query()->with(['nights', 'payments'])->whereKey($reservation->id)->lockForUpdate()->firstOrFail();
                if (! in_array($reservation->status, [ReservationStatus::Paid, ReservationStatus::Confirmed], true) || $reservation->stay()->exists()) {
                    throw ValidationException::withMessages(['reservation' => 'Reservasi tidak memenuhi syarat check-in atau sudah pernah check-in.']);
                }

                $room = Room::query()->whereKey($data['room_id'])->lockForUpdate()->firstOrFail();
                if ($room->room_type_id !== $reservation->room_type_id || ! $this->availability->roomIsAvailable($room, $reservation->check_in_date, $reservation->check_out_date, $reservation->id)) {
                    throw ValidationException::withMessages(['room_id' => 'Kamar tidak sesuai atau tidak tersedia untuk reservasi ini.']);
                }

                $stay = Stay::create([
                    'reservation_id' => $reservation->id,
                    'guest_id' => $reservation->guest_id,
                    'room_id' => $room->id,
                    'guest_name' => $reservation->guest_name,
                    'guest_phone' => $data['guest_phone'],
                    'identity_type' => $data['identity_type'],
                    'identity_number' => $data['identity_number'] ?? null,
                    'identity_photo_path' => $identityPath,
                    'checked_in_by' => $receptionist->id,
                    'check_in_at' => now(),
                    'key_code' => $data['key_code'],
                    'key_issued_at' => now(),
                    'security_deposit_amount' => $data['security_deposit_amount'] ?? 0,
                    'status' => StayStatus::Active,
                    'notes' => $data['notes'] ?? null,
                ]);

                $paidAmount = (int) round((float) $reservation->payments()->where('status', 'paid')->sum('amount'));
                $folio = Folio::create([
                    'folio_number' => 'FOL-'.Str::upper((string) Str::ulid()),
                    'stay_id' => $stay->id,
                    'reservation_id' => $reservation->id,
                    'status' => 'open',
                    'currency' => 'IDR',
                    'subtotal' => $reservation->subtotal,
                    'discount_amount' => $reservation->discount_amount,
                    'service_charge_amount' => $reservation->service_charge_amount,
                    'tax_amount' => $reservation->tax_amount,
                    'total_amount' => $reservation->grand_total,
                    'paid_amount' => $paidAmount,
                    'balance_amount' => max(0, (int) round((float) $reservation->grand_total) - $paidAmount),
                ]);
                foreach ($reservation->nights as $night) {
                    $folio->items()->create([
                        'item_type' => 'room',
                        'description' => 'Kamar '.$room->room_number.' · '.$night->stay_date->translatedFormat('d M Y').' · '.$night->rate_name,
                        'quantity' => 1,
                        'unit_price' => $night->net_price,
                        'amount' => $night->net_price,
                        'source_type' => $night->getMorphClass(),
                        'source_id' => $night->id,
                        'posted_by' => $receptionist->id,
                        'posted_at' => now(),
                    ]);
                }
                $reservation->payments()->update(['stay_id' => $stay->id, 'folio_id' => $folio->id]);

                if (($data['payment_amount'] ?? 0) > 0) {
                    $method = PaymentMethod::query()->whereKey($data['payment_method_id'])->lockForUpdate()->firstOrFail();
                    $this->payments->record($reservation, $method, (int) round((float) $data['payment_amount']), $receptionist, $stay, $folio, 'deposit', $data['reference_number'] ?? null, 'Pembayaran/deposit saat check-in.');
                }

                $reservation->update(['room_id' => $room->id, 'status' => ReservationStatus::CheckedIn]);
                $oldStatus = $room->status;
                $room->update(['status' => RoomStatus::Occupied]);
                RoomStatusHistory::create([
                    'room_id' => $room->id,
                    'old_status' => $oldStatus,
                    'new_status' => RoomStatus::Occupied,
                    'changed_by' => $receptionist->id,
                    'reason' => 'Check-in '.$reservation->booking_code,
                    'changed_at' => now(),
                ]);

                return $stay->fresh(['room', 'reservation', 'folio']);
            }, 3);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($identityPath);
            throw $exception;
        }
    }
}
