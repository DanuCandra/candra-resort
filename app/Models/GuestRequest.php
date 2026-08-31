<?php

namespace App\Models;

use App\Enums\GuestRequestStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'request_code', 'stay_id', 'room_id', 'guest_id', 'handled_by', 'type', 'title',
    'description', 'priority', 'status', 'requested_at', 'accepted_at', 'processing_at',
    'completed_at', 'cancelled_at',
])]
// Mewakili permintaan bantuan atau kebutuhan tamu.
class GuestRequest extends Model
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

    protected function casts(): array
    {
        return [
            'status' => GuestRequestStatus::class,
            'requested_at' => 'datetime',
            'accepted_at' => 'datetime',
            'processing_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }
}
