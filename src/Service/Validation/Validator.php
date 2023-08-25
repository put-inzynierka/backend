<?php

namespace App\Service\Validation;

use Symfony\Component\Validator\ConstraintViolation;

abstract class Validator
{
    protected function createViolation(string $message, string $property = '', ?string $invalidValue = null): ConstraintViolation
    {
        return new ConstraintViolation($message, '', [], null, $property, $invalidValue);
    }
}