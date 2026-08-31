<?php

namespace App\Enums;

// Menyimpan pilihan status operasional kamar.
enum RoomStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Occupied = 'occupied';
    case Cleaning = 'cleaning';
    case Maintenance = 'maintenance';
    case Unavailable = 'unavailable';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
