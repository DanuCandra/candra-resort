<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name', 'code', 'type', 'channel', 'gateway_method_code', 'instructions',
    'is_online', 'is_active', 'sort_order', 'created_by',
])]
// Mewakili metode pembayaran yang tersedia.
class PaymentMethod extends Model
{
    use SoftDeletes;

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function casts(): array
    {
        return [
            'is_online' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
