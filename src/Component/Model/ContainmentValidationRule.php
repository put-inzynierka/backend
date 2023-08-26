<?php

namespace App\Component\Model;

class ContainmentValidationRule
{
    public function __construct(
        protected string $validatorClass,
        protected iterable $needles,
        protected iterable $haystack
    ) {}

    public function getValidatorClass(): string
    {
        return $this->validatorClass;
    }

    public function getNeedles(): iterable
    {
        return $this->needles;
    }

    public function getHaystack(): iterable
    {
        return $this->haystack;
    }
}