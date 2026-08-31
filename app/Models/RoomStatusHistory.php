<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['room_id', 'old_status', 'new_status', 'changed_by', 'reason', 'changed_at'])]
// Mewakili riwayat perubahan status kamar.
class RoomStatusHistory extends Model
{
    public $timestamps = false;

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    protected function casts(): array
    {
        return [
            'old_status' => RoomStatus::class,
            'new_status' => RoomStatus::class,
            'changed_at' => 'datetime',
        ];
    }
}
