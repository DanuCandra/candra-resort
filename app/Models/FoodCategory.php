<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'description', 'is_active', 'sort_order'])]
// Mewakili kategori makanan dan minuman.
class FoodCategory extends Model
{
    use SoftDeletes;

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
