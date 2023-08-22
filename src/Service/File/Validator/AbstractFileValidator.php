<?php

namespace App\Service\File\Validator;

use App\Entity\User\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractFileValidator
{
    protected function createViolation(string $message, string $property = '', ?string $invalidValue = null): ConstraintViolation
    {
        return new ConstraintViolation($message, '', [], null, $property, $invalidValue);
    }
}
