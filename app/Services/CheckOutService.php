<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Enums\StayStatus;
use App\Models\GuestRoomAccess;
use App\Models\PaymentMethod;
use App\Models\RoomStatusHistory;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

// Menjalankan seluruh aturan bisnis check-out.
class CheckOutService
{
    public function __construct(private readonly ManualPaymentService $payments) {}

    public function checkOut(Stay $stay, User $receptionist, array $data): Stay
    {
        return DB::transaction(function () use ($stay, $receptionist, $data): Stay {
            $stay = Stay::query()->with(['reservation', 'room', 'folio.items'])->whereKey($stay->id)->lockForUpdate()->firstOrFail();
            if ($stay->status !== StayStatus::Active || ! $stay->folio) {
                throw ValidationException::withMessages(['stay' => 'Stay tidak aktif atau folio tidak tersedia.']);
            }

            $hasPendingOperations = $stay->foodOrders()->whereNotIn('status', ['completed', 'cancelled'])->exists()
                || $stay->serviceOrders()->whereNotIn('status', ['completed', 'cancelled'])->exists()
                || $stay->guestRequests()->whereNotIn('status', ['completed', 'cancelled'])->exists();
            if ($hasPendingOperations) {
                throw ValidationException::withMessages(['stay' => 'Masih ada pesanan atau permintaan tamu yang belum selesai.']);
            }

            $folio = $stay->folio()->lockForUpdate()->first();
            $total = (int) round((float) $folio->items()->where('is_void', false)->sum('amount'));
            $paid = (int) round((float) $folio->payments()->where('status', 'paid')->sum('amount'));
            $outstanding = max(0, $total - $paid);
            $paymentAmount = (int) round((float) ($data['payment_amount'] ?? 0));
            if ($outstanding > 0 && $paymentAmount !== $outstanding) {
                throw ValidationException::withMessages(['payment_amount' => 'Pembayaran akhir harus sama dengan saldo outstanding Rp'.number_format($outstanding, 0, ',', '.').'.']);
            }
            if ($outstanding > 0) {
                $method = PaymentMethod::query()->whereKey($data['payment_method_id'])->lockForUpdate()->firstOrFail();
                $this->payments->record($stay->reservation, $method, $paymentAmount, $receptionist, $stay, $folio, 'folio', $data['reference_number'] ?? null, 'Pelunasan saat check-out.');
            }

            $folio->update([
                'subtotal' => $total,
                'total_amount' => $total,
                'paid_amount' => $total,
                'balance_amount' => 0,
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => $receptionist->id,
            ]);
            $stay->update([
                'checked_out_by' => $receptionist->id,
                'check_out_at' => now(),
                'key_returned_at' => now(),
                'status' => StayStatus::Completed,
                'notes' => collect([$stay->notes, $data['notes'] ?? null])->filter()->join("\n"),
            ]);
            $stay->reservation->update(['status' => ReservationStatus::CheckedOut]);
            GuestRoomAccess::query()->where('stay_id', $stay->id)->whereNull('revoked_at')->update(['revoked_at' => now()]);

            $oldStatus = $stay->room->status;
            $stay->room->update(['status' => RoomStatus::Cleaning]);
            RoomStatusHistory::create([
                'room_id' => $stay->room_id,
                'old_status' => $oldStatus,
                'new_status' => RoomStatus::Cleaning,
                'changed_by' => $receptionist->id,
                'reason' => 'Check-out '.$stay->reservation->booking_code.'; menunggu housekeeping.',
                'changed_at' => now(),
            ]);

            return $stay->fresh(['reservation', 'room', 'folio']);
        }, 3);
    }
}
