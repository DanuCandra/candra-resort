<?php

namespace App\Models;

use App\Enums\StayStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'reservation_id', 'guest_id', 'room_id', 'guest_name', 'guest_phone', 'identity_type',
    'identity_number', 'identity_photo_path', 'checked_in_by', 'checked_out_by', 'check_in_at',
    'check_out_at', 'key_code', 'key_issued_at', 'key_returned_at', 'security_deposit_amount',
    'status', 'notes',
])]
class Stay extends Model
{
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function roomAccesses(): HasMany
    {
        return $this->hasMany(GuestRoomAccess::class);
    }

    public function folio(): HasOne
    {
        return $this->hasOne(Folio::class);
    }

    public function foodOrders(): HasMany
    {
        return $this->hasMany(FoodOrder::class);
    }

    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function guestRequests(): HasMany
    {
        return $this->hasMany(GuestRequest::class);
    }

    protected function casts(): array
    {
        return [
            'identity_number' => 'encrypted',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'key_issued_at' => 'datetime',
            'key_returned_at' => 'datetime',
            'security_deposit_amount' => 'decimal:2',
            'status' => StayStatus::class,
        ];
    }
}
