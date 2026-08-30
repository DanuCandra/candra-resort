<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'payment_code', 'reservation_id', 'stay_id', 'folio_id', 'payment_method_id',
    'received_by', 'purpose', 'status', 'source', 'currency', 'amount', 'reference_number',
    'proof_path', 'midtrans_order_id', 'midtrans_transaction_id', 'midtrans_payment_type',
    'midtrans_transaction_status', 'midtrans_fraud_status', 'midtrans_bank',
    'midtrans_va_number', 'midtrans_expiry_time', 'midtrans_response', 'paid_at',
    'refunded_at', 'notes',
])]
class Payment extends Model
{
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function stay(): BelongsTo
    {
        return $this->belongsTo(Stay::class);
    }

    public function folio(): BelongsTo
    {
        return $this->belongsTo(Folio::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'midtrans_expiry_time' => 'datetime',
            'midtrans_response' => 'array',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }
}
