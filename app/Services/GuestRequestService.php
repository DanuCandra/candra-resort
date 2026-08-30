<?php

namespace App\Services;

use App\Enums\GuestRequestStatus;
use App\Models\GuestRequest;
use App\Models\GuestRoomAccess;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GuestRequestService
{
    public function place(GuestRoomAccess $access, array $data): GuestRequest
    {
        return DB::transaction(function () use ($access, $data): GuestRequest {
            $access = GuestRoomAccess::query()->with('stay')->whereKey($access->id)->lockForUpdate()->firstOrFail();
            if (! $access->isUsable()) {
                throw ValidationException::withMessages(['title' => 'Sesi layanan kamar sudah tidak aktif.']);
            }

            return GuestRequest::create([
                'request_code' => 'REQ-'.Str::upper((string) Str::ulid()),
                'stay_id' => $access->stay_id,
                'room_id' => $access->room_id,
                'guest_id' => $access->stay->guest_id,
                'type' => $data['type'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'priority' => $data['priority'],
                'status' => GuestRequestStatus::Requested,
                'requested_at' => now(),
            ])->fresh('room');
        }, 3);
    }

    public function transition(GuestRequest $guestRequest, GuestRequestStatus $status, User $receptionist): GuestRequest
    {
        return DB::transaction(function () use ($guestRequest, $status, $receptionist): GuestRequest {
            $guestRequest = GuestRequest::query()->whereKey($guestRequest->id)->lockForUpdate()->firstOrFail();
            $allowed = match ($guestRequest->status) {
                GuestRequestStatus::Requested => [GuestRequestStatus::Accepted, GuestRequestStatus::Cancelled],
                GuestRequestStatus::Accepted => [GuestRequestStatus::Processing, GuestRequestStatus::Cancelled],
                GuestRequestStatus::Processing => [GuestRequestStatus::Completed, GuestRequestStatus::Cancelled],
                default => [],
            };
            if (! in_array($status, $allowed, true)) {
                throw ValidationException::withMessages(['status' => 'Perubahan status permintaan tidak valid.']);
            }

            $field = match ($status) {
                GuestRequestStatus::Accepted => 'accepted_at', GuestRequestStatus::Processing => 'processing_at',
                GuestRequestStatus::Completed => 'completed_at', GuestRequestStatus::Cancelled => 'cancelled_at', default => null,
            };
            $updates = ['status' => $status, 'handled_by' => $receptionist->id];
            if ($field) {
                $updates[$field] = now();
            }
            $guestRequest->update($updates);

            return $guestRequest->fresh(['room', 'stay', 'handledBy']);
        }, 3);
    }
}
