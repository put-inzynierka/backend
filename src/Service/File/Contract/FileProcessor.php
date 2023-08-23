<?php

namespace App\Service\File\Contract;

use App\Bridge\Symfony\HttpFoundation\RawFile;
use App\Entity\User\User;

interface FileProcessor
{
    public function getSupportedTypes(): array;
    public function process(RawFile $file, ?User $actor): void;
}
