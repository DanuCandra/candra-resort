<?php

namespace App\Enums;

enum ServiceOrderStatus: string
{
    case Requested = 'requested';
    case Accepted = 'accepted';
    case Scheduled = 'scheduled';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Menunggu', self::Accepted => 'Diterima', self::Scheduled => 'Terjadwal',
            self::Processing => 'Diproses', self::Completed => 'Selesai', self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Requested => 'warning', self::Accepted, self::Scheduled => 'info',
            self::Processing => 'primary', self::Completed => 'success', self::Cancelled => 'danger',
        };
    }
}
