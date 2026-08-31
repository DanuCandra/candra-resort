<?php

namespace App\Services;

use App\Enums\FoodOrderStatus;
use App\Models\FoodOrder;
use App\Models\GuestRoomAccess;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

// Membuat dan memproses status pesanan makanan.
class FoodOrderService
{
    public function __construct(private readonly FolioService $folios) {}

    public function place(GuestRoomAccess $access, array $data): FoodOrder
    {
        return DB::transaction(function () use ($access, $data): FoodOrder {
            $access = GuestRoomAccess::query()->with('stay')->whereKey($access->id)->lockForUpdate()->firstOrFail();
            if (! $access->isUsable()) {
                throw ValidationException::withMessages(['items' => 'Sesi layanan kamar sudah tidak aktif.']);
            }

            $selected = collect($data['items'])
                ->filter(fn (array $item): bool => (int) ($item['quantity'] ?? 0) > 0);
            $menuItems = MenuItem::query()->whereIn('id', $selected->keys()->all())
                ->where('is_active', true)->where('is_available', true)->lockForUpdate()->get()->keyBy('id');

            if ($menuItems->count() !== $selected->count()) {
                throw ValidationException::withMessages(['items' => 'Salah satu menu tidak tersedia lagi. Silakan muat ulang halaman.']);
            }

            $subtotal = $selected->sum(fn (array $item, int|string $id): int => (int) round((float) $menuItems->get((int) $id)->price * (int) $item['quantity'])
            );

            $order = FoodOrder::create([
                'order_code' => 'FNB-'.Str::upper((string) Str::ulid()),
                'stay_id' => $access->stay_id,
                'room_id' => $access->room_id,
                'guest_id' => $access->stay->guest_id,
                'source' => 'qr',
                'status' => FoodOrderStatus::Requested,
                'subtotal' => $subtotal,
                'service_charge_amount' => 0,
                'total_amount' => $subtotal,
                'charge_to_room' => true,
                'delivery_notes' => $data['delivery_notes'] ?? null,
                'ordered_at' => now(),
            ]);

            foreach ($selected as $id => $item) {
                $menuItem = $menuItems->get((int) $id);
                $quantity = (int) $item['quantity'];
                $order->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'item_name' => $menuItem->name,
                    'quantity' => $quantity,
                    'unit_price' => $menuItem->price,
                    'subtotal' => (int) round((float) $menuItem->price * $quantity),
                    'special_notes' => $item['special_notes'] ?? null,
                ]);
            }

            return $order->fresh(['items', 'room']);
        }, 3);
    }

    public function transition(FoodOrder $order, FoodOrderStatus $status, User $receptionist): FoodOrder
    {
        return DB::transaction(function () use ($order, $status, $receptionist): FoodOrder {
            $order = FoodOrder::query()->with('stay.folio')->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $from = $order->status;
            $allowed = match ($from) {
                FoodOrderStatus::Requested => [FoodOrderStatus::Accepted, FoodOrderStatus::Cancelled],
                FoodOrderStatus::Accepted => [FoodOrderStatus::Processing, FoodOrderStatus::Cancelled],
                FoodOrderStatus::Processing => [FoodOrderStatus::Completed, FoodOrderStatus::Cancelled],
                default => [],
            };

            if (! in_array($status, $allowed, true)) {
                throw ValidationException::withMessages(['status' => 'Perubahan status pesanan tidak valid.']);
            }

            $timestamp = match ($status) {
                FoodOrderStatus::Accepted => 'accepted_at',
                FoodOrderStatus::Processing => 'processing_at',
                FoodOrderStatus::Completed => 'completed_at',
                FoodOrderStatus::Cancelled => 'cancelled_at',
                default => null,
            };
            $updates = ['status' => $status, 'handled_by' => $receptionist->id];
            if ($timestamp) {
                $updates[$timestamp] = now();
            }
            $order->update($updates);

            if ($status === FoodOrderStatus::Completed && $order->charge_to_room) {
                $folio = $order->stay?->folio;
                if (! $folio) {
                    throw ValidationException::withMessages(['status' => 'Folio active stay tidak ditemukan.']);
                }
                $this->folios->postFoodOrder($folio, $order, $receptionist);
            }

            return $order->fresh(['items', 'stay.folio', 'room', 'handledBy']);
        }, 3);
    }
}
