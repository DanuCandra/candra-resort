<?php

namespace App\Enums;

// Menyimpan tahapan status reservasi kamar.
enum ReservationStatus: string
{
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case Confirmed = 'confirmed';
    case CheckedIn = 'checked_in';
    case CheckedOut = 'checked_out';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';
}
