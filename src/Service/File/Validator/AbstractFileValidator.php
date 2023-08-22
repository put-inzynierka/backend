<?php

namespace App\Service\File\Validator;

use Symfony\Component\Validator\ConstraintViolation;

abstract class AbstractFileValidator
{
    protected function createViolation(string $message, string $property = '', ?string $invalidValue = null): ConstraintViolation
    {
        return new ConstraintViolation($message, '', [], null, $property, $invalidValue);
    }
}
