<?php

namespace App\Enums;

// Menyimpan status masa inap tamu.
enum StayStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
