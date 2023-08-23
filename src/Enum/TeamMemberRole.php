<?php

namespace App\Enum;

enum TeamMemberRole: string
{
    use EnumTrait;

    case OWNER = 'owner';
    case MANAGER = 'manager';
    case MEMBER = 'member';
}
