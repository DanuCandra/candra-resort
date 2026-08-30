<?php

namespace App\Models;

use App\Enums\StayStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'stay_id', 'room_id', 'session_token', 'phone_verified_at', 'last_accessed_at',
    'expires_at', 'revoked_at', 'ip_address', 'user_agent',
])]
class GuestRoomAccess extends Model
{
    public function stay(): BelongsTo
    {
        return $this->belongsTo(Stay::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function isUsable(): bool
    {
        return $this->revoked_at === null
            && ($this->expires_at === null || $this->expires_at->isFuture())
            && $this->stay?->status === StayStatus::Active;
    }

    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
            'last_accessed_at' => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }
}
