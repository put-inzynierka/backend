<?php

namespace App\Enum;

enum TeamMemberRole: string
{
    use EnumTrait;

    case OWNER = 'owner';
    case MANAGER = 'manager';
    case MEMBER = 'member';

    public function canManage(): bool
    {
        return in_array($this, [self::MANAGER, self::OWNER]);
    }
}
