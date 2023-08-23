<?php

namespace App\Service\File\Contract;

use App\Bridge\Symfony\HttpFoundation\RawFile;
use App\Entity\User\User;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface FileValidator
{
    public function getSupportedTypes(): array;
    public function validate(RawFile $file, ?User $actor): ConstraintViolationListInterface;
}
