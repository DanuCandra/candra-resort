<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['section', 'content_key', 'title', 'content', 'image_path', 'metadata', 'sort_order', 'is_active', 'updated_by'])]
// Mewakili konten dinamis pada website publik.
class WebsiteContent extends Model
{
    use SoftDeletes;

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
