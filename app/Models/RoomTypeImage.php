<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['room_type_id', 'image_path', 'alt_text', 'caption', 'is_primary', 'sort_order'])]
class RoomTypeImage extends Model
{
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }
}
