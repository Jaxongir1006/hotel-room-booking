<?php

namespace App\Enums;

enum RoomType: string
{
    case Single = 'single';
    case Double = 'double';
    case Suite = 'suite';
    case Deluxe = 'deluxe';

    public function label(): string
    {
        return match ($this) {
            self::Single => 'Single',
            self::Double => 'Double',
            self::Suite => 'Suite',
            self::Deluxe => 'Deluxe',
        };
    }

    /** @return array<int, array{value: string, label: string}> */
    public static function options(): array
    {
        return array_map(
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases(),
        );
    }
}
