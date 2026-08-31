<?php

namespace App\Enums;

// Menyimpan pilihan status resmi untuk transaksi pembayaran.
enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu', self::Paid => 'Lunas', self::Failed => 'Gagal',
            self::Refunded => 'Dikembalikan', self::Cancelled => 'Dibatalkan', self::Expired => 'Kedaluwarsa',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'warning', self::Paid => 'success', self::Failed, self::Cancelled, self::Expired => 'danger',
            self::Refunded => 'info',
        };
    }
}
