<?php

namespace App\Enum\File;

enum ByteSize: int
{
    case HUNDRED_MB = 100_000_000;
    case TEN_MB = 10_000_000;
    case MEGABYTE = 1_000_000;
}