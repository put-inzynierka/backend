<?php

namespace App\Service\File\Processor;

use App\Bridge\Symfony\HttpFoundation\RawFile;
use App\Entity\User\User;
use App\Enum\File\FileType;
use App\Service\File\Contract\FileProcessor;

class PassthroughProcessor implements FileProcessor
{
    public function getSupportedTypes(): array
    {
        return [FileType::EVENT_IMAGE, FileType::PROJECT_IMAGE];
    }

    public function process(RawFile $file, ?User $actor): void
    {
        // do stuff like crop, resize, etc
    }
}