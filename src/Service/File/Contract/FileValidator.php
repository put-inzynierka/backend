<?php

namespace App\Service\File\Contract;

use App\Entity\User\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface FileValidator
{
    public function getSupportedTypes(): array;
    public function validate(UploadedFile $file, ?User $actor): ConstraintViolationListInterface;
}
