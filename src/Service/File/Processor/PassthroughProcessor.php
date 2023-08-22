<?php

namespace App\Service\File\Processor;

use App\Entity\User\User;
use App\Enum\File\FileType;
use App\Service\File\Contract\FileProcessor;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PassthroughProcessor implements FileProcessor
{
    public function getSupportedTypes(): array
    {
        return [FileType::EVENT_IMAGE, FileType::PROJECT_IMAGE];
    }

    public function process(UploadedFile $file, ?User $actor): void
    {
        // do stuff like crop, resize, etc
    }
}