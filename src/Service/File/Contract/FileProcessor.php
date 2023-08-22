<?php

namespace App\Service\File\Contract;

use App\Entity\User\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileProcessor
{
    public function getSupportedTypes(): array;
    public function process(UploadedFile $file, ?User $actor): void;
}
