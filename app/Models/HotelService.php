<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'code', 'name', 'category', 'description', 'price', 'price_unit', 'duration_minutes',
    'image_path', 'requires_schedule', 'is_available', 'is_active', 'sort_order',
])]
// Mewakili layanan berbayar yang disediakan hotel.
class HotelService extends Model
{
    use SoftDeletes;

    public function orders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'requires_schedule' => 'boolean',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
