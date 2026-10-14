<?php

namespace App\Enums;

enum AppointmentRequestStatus: string
{
    case Requested = 'requested';
    case Confirmed = 'confirmed';
    case Declined = 'declined';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Zatraženo',
            self::Confirmed => 'Potvrđeno',
            self::Declined => 'Odbijeno',
            self::Cancelled => 'Otkazano',
        };
    }
}
