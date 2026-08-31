<?php

namespace App\Enums;

// Menyimpan status pembayaran keseluruhan reservasi.
enum ReservationPaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Partial = 'partial';
    case Paid = 'paid';
    case Refunded = 'refunded';
}
