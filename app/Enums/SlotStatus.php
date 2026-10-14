<?php

namespace App\Enums;

enum SlotStatus: string
{
    case Free = 'free';
    case Held = 'held';
    case Booked = 'booked';

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Slobodno',
            self::Held => 'Rezervirano (privremeno)',
            self::Booked => 'Zauzeto',
        };
    }
}
