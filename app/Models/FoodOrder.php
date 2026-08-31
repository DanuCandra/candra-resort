<?php

namespace App\Models;

use App\Enums\FoodOrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_code', 'stay_id', 'room_id', 'guest_id', 'handled_by', 'source', 'status',
    'subtotal', 'service_charge_amount', 'total_amount', 'charge_to_room', 'delivery_notes',
    'ordered_at', 'accepted_at', 'processing_at', 'completed_at', 'cancelled_at',
])]
// Mewakili satu transaksi pesanan makanan.
class FoodOrder extends Model
{
    public function stay(): BelongsTo
    {
        return $this->belongsTo(Stay::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FoodOrderItem::class);
    }

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'service_charge_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'charge_to_room' => 'boolean',
            'status' => FoodOrderStatus::class,
            'ordered_at' => 'datetime',
            'accepted_at' => 'datetime',
            'processing_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }
}
