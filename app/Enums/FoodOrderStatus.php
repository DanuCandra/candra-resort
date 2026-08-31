<?php

namespace App\Enums;

// Menyimpan pilihan status resmi untuk pesanan makanan.
enum FoodOrderStatus: string
{
    case Requested = 'requested';
    case Accepted = 'accepted';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Menunggu',
            self::Accepted => 'Diterima',
            self::Processing => 'Diproses',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Requested => 'warning',
            self::Accepted => 'info',
            self::Processing => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }
}
