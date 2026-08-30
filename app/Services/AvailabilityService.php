<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\CarbonInterface;

class AvailabilityService
{
    public function availableCount(RoomType $roomType, CarbonInterface $checkIn, CarbonInterface $checkOut): int
    {
        $inventory = $roomType->rooms()
            ->where('is_active', true)
            ->whereNotIn('status', [RoomStatus::Maintenance->value, RoomStatus::Unavailable->value])
            ->count();

        if ($inventory === 0) {
            return 0;
        }

        $booked = Reservation::query()
            ->where('room_type_id', $roomType->id)
            ->whereDate('check_in_date', '<', $checkOut->toDateString())
            ->whereDate('check_out_date', '>', $checkIn->toDateString())
            ->where(function ($query): void {
                $query->whereIn('status', [
                    ReservationStatus::Paid->value,
                    ReservationStatus::Confirmed->value,
                    ReservationStatus::CheckedIn->value,
                ])->orWhere(function ($pending): void {
                    $pending->where('status', ReservationStatus::PendingPayment->value)
                        ->where(function ($validHold): void {
                            $validHold->whereNull('payment_due_at')->orWhere('payment_due_at', '>', now());
                        });
                });
            })
            ->count();

        return max(0, $inventory - $booked);
    }

    public function roomIsAvailable(Room $room, CarbonInterface $checkIn, CarbonInterface $checkOut, ?int $ignoreReservationId = null): bool
    {
        if (! $room->is_active || in_array($room->status, [RoomStatus::Maintenance, RoomStatus::Unavailable], true)) {
            return false;
        }

        return ! $room->reservations()
            ->when($ignoreReservationId, fn ($query) => $query->whereKeyNot($ignoreReservationId))
            ->whereDate('check_in_date', '<', $checkOut->toDateString())
            ->whereDate('check_out_date', '>', $checkIn->toDateString())
            ->where(function ($query): void {
                $query->whereIn('status', [
                    ReservationStatus::Paid->value,
                    ReservationStatus::Confirmed->value,
                    ReservationStatus::CheckedIn->value,
                ])->orWhere(function ($pending): void {
                    $pending->where('status', ReservationStatus::PendingPayment->value)
                        ->where(fn ($validHold) => $validHold->whereNull('payment_due_at')->orWhere('payment_due_at', '>', now()));
                });
            })->exists();
    }
}
