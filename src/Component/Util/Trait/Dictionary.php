<?php

namespace App\Component\Util\Trait;

trait Dictionary
{
    public static function getAll(): array
    {
        $reflectionClass = new \ReflectionClass(new static);

        return $reflectionClass->getConstants(
            \ReflectionClassConstant::IS_PUBLIC | \ReflectionClassConstant::IS_PROTECTED
        );
    }
}