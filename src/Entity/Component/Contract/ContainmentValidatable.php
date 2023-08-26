<?php

namespace App\Entity\Component\Contract;

interface ContainmentValidatable
{
    public function getContainmentValidationRules(): array;
}
