<?php

namespace App\Enum\File;

use App\Enum\EnumTrait;
use JetBrains\PhpStorm\Deprecated;

enum MimeType: string
{
    use EnumTrait;

    protected const DEPRECATION_MESSAGE = 'Should still be supported, but no longer assigned.';

    case IMAGE_BMP = 'image/bmp';
    case IMAGE_GIF = 'image/gif';
    case IMAGE_HEIC = 'image/heic';
    case IMAGE_JPEG = 'image/jpeg';
    case IMAGE_PNG = 'image/png';
    case IMAGE_TIFF = 'image/tiff';
    case IMAGE_WEBP = 'image/webp';

    #[Deprecated(self::DEPRECATION_MESSAGE, '%class%::IMAGE_JPEG')]
    case IMAGE_PJPEG = 'image/pjpeg';
    #[Deprecated(self::DEPRECATION_MESSAGE, '%class%::IMAGE_PNG')]
    case IMAGE_VND_MOZILLA_APNG = 'image/vnd.mozilla.apng';

    public function isImage(): bool
    {
        return explode('/', $this->value)[0] === 'image';
    }
}
