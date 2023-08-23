<?php

namespace App\Enum;

enum StandType: string
{
    use EnumTrait;

    case STAND = 'stand';
    case STAGE = 'stage';
}
