<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['reservation_id', 'room_rate_id', 'stay_date', 'rate_name', 'price_before_discount', 'discount_amount', 'net_price'])]
// Menyimpan snapshot harga setiap malam reservasi.
class ReservationNight extends Model
{
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function roomRate(): BelongsTo
    {
        return $this->belongsTo(RoomRate::class);
    }

    protected function casts(): array
    {
        return [
            'stay_date' => 'date',
            'price_before_discount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'net_price' => 'decimal:2',
        ];
    }
}
