<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'folio_id', 'item_type', 'description', 'quantity', 'unit_price', 'amount',
    'source_type', 'source_id', 'posted_by', 'posted_at', 'is_void', 'voided_by',
    'voided_at', 'void_reason',
])]
class FolioItem extends Model
{
    public function folio(): BelongsTo
    {
        return $this->belongsTo(Folio::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'amount' => 'decimal:2',
            'posted_at' => 'datetime',
            'is_void' => 'boolean',
            'voided_at' => 'datetime',
        ];
    }
}
