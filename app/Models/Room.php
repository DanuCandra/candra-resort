<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['room_type_id', 'room_number', 'floor', 'status', 'qr_token', 'notes', 'is_active'])]
class Room extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Room $room): void {
            $room->qr_token ??= (string) Str::uuid();
        });
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(RoomStatusHistory::class)->latest('changed_at');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function stays(): HasMany
    {
        return $this->hasMany(Stay::class);
    }

    protected function casts(): array
    {
        return [
            'status' => RoomStatus::class,
            'is_active' => 'boolean',
        ];
    }
}
