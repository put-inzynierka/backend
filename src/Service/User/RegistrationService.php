<?php

namespace App\Service\User;

use App\Entity\Component\Contract\Token;
use App\Entity\User\ActivationToken;
use App\Entity\User\User;
use App\Enum\UserRole;
use App\Service\Mail\RegistrationMailer;
use Doctrine\ORM\EntityManagerInterface;

class RegistrationService
{
    public function __construct(
        protected PasswordHasher $passwordHasher,
        protected TokenFactory $tokenFactory,
        protected EntityManagerInterface $entityManager,
        protected RegistrationMailer $registrationMailer,
    ) {}

    public function register(User $user): void
    {
        $user
            ->setRole(UserRole::USER)
            ->setActive(false)
        ;

        $this->passwordHasher->hashPassword($user);

        $token = $this->generateActivationToken($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($token);

        $this->entityManager->flush();

        $this->registrationMailer->send($user, $token);
    }

    protected function generateActivationToken(User $user): Token
    {
        return $this->tokenFactory->create(
            ActivationToken::class,
            $user
        );
    }
}
