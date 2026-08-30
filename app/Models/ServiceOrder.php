<?php

namespace App\Models;

use App\Enums\ServiceOrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_code', 'stay_id', 'room_id', 'guest_id', 'hotel_service_id', 'handled_by',
    'source', 'status', 'quantity', 'unit_price', 'total_amount', 'charge_to_room',
    'scheduled_at', 'notes', 'accepted_at', 'completed_at', 'cancelled_at',
])]
class ServiceOrder extends Model
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

    public function service(): BelongsTo
    {
        return $this->belongsTo(HotelService::class, 'hotel_service_id');
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'charge_to_room' => 'boolean',
            'status' => ServiceOrderStatus::class,
            'scheduled_at' => 'datetime',
            'accepted_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }
}
