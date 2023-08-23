<?php

namespace App\Enum\File;

use App\Enum\EnumTrait;

enum FileType: string
{
    use EnumTrait;

    case EVENT_IMAGE = 'event-image';
    case PROJECT_IMAGE = 'project-image';
}
