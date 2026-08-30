<?php

namespace App\Services;

use App\Enums\ServiceOrderStatus;
use App\Models\GuestRoomAccess;
use App\Models\HotelService;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ServiceOrderService
{
    public function __construct(private readonly FolioService $folios) {}

    public function place(GuestRoomAccess $access, array $data): ServiceOrder
    {
        return DB::transaction(function () use ($access, $data): ServiceOrder {
            $access = GuestRoomAccess::query()->with('stay')->whereKey($access->id)->lockForUpdate()->firstOrFail();
            if (! $access->isUsable()) {
                throw ValidationException::withMessages(['hotel_service_id' => 'Sesi layanan kamar sudah tidak aktif.']);
            }
            $service = HotelService::query()->whereKey($data['hotel_service_id'])->where('is_active', true)->where('is_available', true)->lockForUpdate()->first();
            if (! $service) {
                throw ValidationException::withMessages(['hotel_service_id' => 'Layanan tidak tersedia lagi.']);
            }
            if ($service->requires_schedule && empty($data['scheduled_at'])) {
                throw ValidationException::withMessages(['scheduled_at' => 'Pilih jadwal untuk layanan ini.']);
            }

            $quantity = round((float) $data['quantity'], 2);

            return ServiceOrder::create([
                'order_code' => 'SVC-'.Str::upper((string) Str::ulid()),
                'stay_id' => $access->stay_id,
                'room_id' => $access->room_id,
                'guest_id' => $access->stay->guest_id,
                'hotel_service_id' => $service->id,
                'source' => 'qr',
                'status' => ServiceOrderStatus::Requested,
                'quantity' => $quantity,
                'unit_price' => $service->price,
                'total_amount' => round((float) $service->price * $quantity, 2),
                'charge_to_room' => true,
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'notes' => $data['notes'] ?? null,
            ])->fresh(['service', 'room']);
        }, 3);
    }

    public function transition(ServiceOrder $order, ServiceOrderStatus $status, User $receptionist, ?string $scheduledAt = null): ServiceOrder
    {
        return DB::transaction(function () use ($order, $status, $receptionist, $scheduledAt): ServiceOrder {
            $order = ServiceOrder::query()->with(['service', 'stay.folio'])->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $allowed = match ($order->status) {
                ServiceOrderStatus::Requested => [ServiceOrderStatus::Accepted, ServiceOrderStatus::Cancelled],
                ServiceOrderStatus::Accepted => [ServiceOrderStatus::Scheduled, ServiceOrderStatus::Processing, ServiceOrderStatus::Cancelled],
                ServiceOrderStatus::Scheduled => [ServiceOrderStatus::Processing, ServiceOrderStatus::Cancelled],
                ServiceOrderStatus::Processing => [ServiceOrderStatus::Completed, ServiceOrderStatus::Cancelled],
                default => [],
            };
            if (! in_array($status, $allowed, true)) {
                throw ValidationException::withMessages(['status' => 'Perubahan status layanan tidak valid.']);
            }
            if ($status === ServiceOrderStatus::Scheduled && ! ($scheduledAt || $order->scheduled_at)) {
                throw ValidationException::withMessages(['scheduled_at' => 'Jadwal layanan wajib diisi.']);
            }

            $updates = ['status' => $status, 'handled_by' => $receptionist->id];
            if ($scheduledAt) {
                $updates['scheduled_at'] = $scheduledAt;
            }
            if ($status === ServiceOrderStatus::Accepted) {
                $updates['accepted_at'] = now();
            } elseif ($status === ServiceOrderStatus::Completed) {
                $updates['completed_at'] = now();
            } elseif ($status === ServiceOrderStatus::Cancelled) {
                $updates['cancelled_at'] = now();
            }
            $order->update($updates);

            if ($status === ServiceOrderStatus::Completed && $order->charge_to_room) {
                if (! $order->stay?->folio) {
                    throw ValidationException::withMessages(['status' => 'Folio active stay tidak ditemukan.']);
                }
                $this->folios->postServiceOrder($order->stay->folio, $order, $receptionist);
            }

            return $order->fresh(['service', 'stay.folio', 'room', 'handledBy']);
        }, 3);
    }
}
