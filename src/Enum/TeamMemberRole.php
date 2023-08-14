<?php

namespace App\Enum;

enum TeamMemberRole: string
{
    case OWNER = 'owner';
    case MANAGER = 'manager';
    case MEMBER = 'member';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
