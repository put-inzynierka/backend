<?php

namespace App\Entity\Component\Contract;

use App\Entity\User\User;

interface Token
{
    public function getValue(): string;
    public function getUser(): User;
    public function isUsed(): bool;
    public function setUsed(bool $used): Token;
    public function __construct(string $value, User $user);
}
