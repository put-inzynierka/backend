<?php

namespace App\Enum\File;

use App\Enum\EnumTrait;

enum FileExtension: string
{
    use EnumTrait;

    case JPG = 'jpg';
    case JPEG = 'jpeg';
    case JPE = 'jpe';
    case JIF = 'jif';
    case JFIF = 'jfif';
    case JFI = 'jfi';
    case PNG = 'png';
    case GIF = 'gif';
    case WEBP = 'webp';
    case TIFF = 'tiff';
    case TIF = 'tif';
    case BMP = 'bmp';
    case DIB = 'dib';
    case HEIF = 'heif';
    case HEIC = 'heic';

    public function mimeType(): MimeType
    {
        return match ($this) {
            self::JPG, self::JPEG, self::JPE, self::JIF, self::JFIF, self::JFI => MimeType::IMAGE_JPEG,
            self::PNG => MimeType::IMAGE_PNG,
            self::GIF => MimeType::IMAGE_GIF,
            self::WEBP => MimeType::IMAGE_WEBP,
            self::TIFF, self::TIF => MimeType::IMAGE_TIFF,
            self::BMP, self::DIB => MimeType::IMAGE_BMP,
            self::HEIF, self::HEIC => MimeType::IMAGE_HEIC,
        };
    }
}
