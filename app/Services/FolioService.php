<?php

namespace App\Services;

use App\Models\Folio;
use App\Models\FoodOrder;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class FolioService
{
    public function postFoodOrder(Folio $folio, FoodOrder $order, User $receptionist): void
    {
        if ($folio->status !== 'open') {
            throw ValidationException::withMessages(['status' => 'Folio tamu sudah ditutup.']);
        }

        $alreadyPosted = $folio->items()
            ->where('source_type', $order->getMorphClass())
            ->where('source_id', $order->id)
            ->exists();

        if (! $alreadyPosted) {
            $folio->items()->create([
                'item_type' => 'food',
                'description' => 'Pesanan F&B '.$order->order_code,
                'quantity' => 1,
                'unit_price' => $order->total_amount,
                'amount' => $order->total_amount,
                'source_type' => $order->getMorphClass(),
                'source_id' => $order->id,
                'posted_by' => $receptionist->id,
                'posted_at' => now(),
            ]);
        }

        $this->recalculate($folio);
    }

    public function recalculate(Folio $folio): void
    {
        $total = (float) $folio->items()->where('is_void', false)->sum('amount');
        $paid = (float) $folio->payments()->where('status', 'paid')->sum('amount');

        $folio->update([
            'subtotal' => $total,
            'total_amount' => $total,
            'paid_amount' => $paid,
            'balance_amount' => max(0, $total - $paid),
        ]);
    }

    public function postServiceOrder(Folio $folio, ServiceOrder $order, User $receptionist): void
    {
        if ($folio->status !== 'open') {
            throw ValidationException::withMessages(['status' => 'Folio tamu sudah ditutup.']);
        }

        if (! $folio->items()->where('source_type', $order->getMorphClass())->where('source_id', $order->id)->exists()) {
            $folio->items()->create([
                'item_type' => 'service',
                'description' => $order->service->name.' · '.$order->order_code,
                'quantity' => $order->quantity,
                'unit_price' => $order->unit_price,
                'amount' => $order->total_amount,
                'source_type' => $order->getMorphClass(),
                'source_id' => $order->id,
                'posted_by' => $receptionist->id,
                'posted_at' => now(),
            ]);
        }

        $this->recalculate($folio);
    }
}
