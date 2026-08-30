<?php

namespace App\Enums;

enum UserRole: string
{
    case Guest = 'guest';
    case Receptionist = 'receptionist';
    case Owner = 'owner';

    public function label(): string
    {
        return match ($this) {
            self::Guest => 'Guest',
            self::Receptionist => 'Receptionist',
            self::Owner => 'Owner',
        };
    }
}
