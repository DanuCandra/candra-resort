<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'code', 'name', 'slug', 'description', 'capacity', 'max_adults', 'max_children',
    'bed_type', 'bed_count', 'room_size_sqm', 'base_price', 'extra_bed_price',
    'breakfast_included', 'is_active', 'sort_order',
])]
// Mewakili jenis kamar beserta fasilitasnya.
class RoomType extends Model
{
    use SoftDeletes;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomTypeImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function rates(): HasMany
    {
        return $this->hasMany(RoomRate::class);
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    protected function casts(): array
    {
        return [
            'room_size_sqm' => 'decimal:2',
            'base_price' => 'decimal:2',
            'extra_bed_price' => 'decimal:2',
            'breakfast_included' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
