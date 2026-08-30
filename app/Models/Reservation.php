<?php

namespace App\Models;

use App\Enums\ReservationPaymentStatus;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'booking_code', 'guest_id', 'created_by', 'room_type_id', 'room_id', 'promotion_id',
    'source', 'guest_name', 'guest_email', 'guest_phone', 'check_in_date', 'check_out_date',
    'total_nights', 'adults', 'children', 'estimated_arrival_time', 'status', 'payment_status',
    'currency', 'subtotal', 'discount_amount', 'service_charge_amount', 'tax_amount',
    'deposit_amount', 'grand_total', 'promo_code_snapshot', 'special_requests', 'internal_notes',
    'payment_due_at', 'confirmed_at', 'cancelled_at', 'cancelled_by', 'cancellation_reason',
])]
class Reservation extends Model
{
    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function nights(): HasMany
    {
        return $this->hasMany(ReservationNight::class)->orderBy('stay_date');
    }

    public function stay(): HasOne
    {
        return $this->hasOne(Stay::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function folio(): HasOne
    {
        return $this->hasOne(Folio::class);
    }

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'status' => ReservationStatus::class,
            'payment_status' => ReservationPaymentStatus::class,
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'service_charge_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'payment_due_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }
}
