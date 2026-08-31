<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['room_type_id', 'name', 'start_date', 'end_date', 'days_of_week', 'price_per_night', 'priority', 'is_active', 'created_by'])]
// Mewakili tarif khusus suatu tipe kamar.
class RoomRate extends Model
{
    use SoftDeletes;

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reservationNights(): HasMany
    {
        return $this->hasMany(ReservationNight::class);
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'days_of_week' => 'array',
            'price_per_night' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
