<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'scope', 'icon', 'description', 'is_active', 'sort_order'])]
class Facility extends Model
{
    use SoftDeletes;

    public function roomTypes(): BelongsToMany
    {
        return $this->belongsToMany(RoomType::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
