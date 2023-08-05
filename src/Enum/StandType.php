<?php

namespace App\Enum;

enum StandType: string
{
    case STAND = 'stand';
    case STAGE = 'stage';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
