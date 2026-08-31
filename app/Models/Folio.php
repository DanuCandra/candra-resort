<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'folio_number', 'stay_id', 'reservation_id', 'status', 'currency', 'subtotal',
    'discount_amount', 'service_charge_amount', 'tax_amount', 'total_amount',
    'paid_amount', 'balance_amount', 'closed_at', 'closed_by',
])]
// Mewakili tagihan berjalan selama masa inap.
class Folio extends Model
{
    public function stay(): BelongsTo
    {
        return $this->belongsTo(Stay::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FolioItem::class)->orderBy('posted_at');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'service_charge_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
            'closed_at' => 'datetime',
        ];
    }
}
