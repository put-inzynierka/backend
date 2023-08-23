<?php

namespace App\Enum;

enum UserRole: string
{
    use EnumTrait;

    case ADMIN = 'admin';
    case USER = 'user';
}
