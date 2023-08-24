<?php declare(strict_types=1);

namespace App\Enum;

enum ProjectType: string
{
    use EnumTrait;

    case PROJECT_STAND = 'project_stand';
    case LECTURE_PANEL = 'lecture_panel';
}