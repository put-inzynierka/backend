<?php

namespace App\Service\User;

use App\Entity\User\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordHasher
{
    public function __construct(
        protected UserPasswordHasherInterface $baseHasher
    ) {}

    public function hashPassword(User $user): void
    {
        $password = $user->getPassword();
        $password = $this->baseHasher->hashPassword($user, $password);
        $user->setPassword($password);
    }
}
