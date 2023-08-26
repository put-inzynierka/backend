<?php

namespace App\Service\Validation;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ContainmentValidator
{
    public static function validate(iterable $needles, iterable $haystack): ConstraintViolationListInterface;
}