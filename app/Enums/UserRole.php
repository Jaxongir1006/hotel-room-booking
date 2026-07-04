<?php

namespace App\Enums;

enum UserRole: string
{
    case Guest = 'guest';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Guest => 'Guest',
            self::Admin => 'Administrator',
        };
    }
}
