<?php

namespace App\Service\User;

use App\Entity\Component\Contract\Token;
use App\Entity\User\PasswordResetToken;
use App\Entity\User\User;
use App\Repository\SecurityUserRepository;
use App\Service\Mail\PasswordResetMailer;
use Doctrine\ORM\EntityManagerInterface;

class PasswordResetService
{
    public function __construct(
        protected TokenFactory $tokenFactory,
        protected EntityManagerInterface $entityManager,
        protected SecurityUserRepository $userRepository,
        protected PasswordResetMailer $passwordResetMailer
    ) {}

    public function sendPasswordResetLink(string $email): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return;
        }

        $token = $this->generatePasswordResetToken($user);

        $this->entityManager->persist($token);
        $this->entityManager->flush();

        $this->passwordResetMailer->send($user, $token);
    }

    protected function generatePasswordResetToken(User $user): Token
    {
        return $this->tokenFactory->create(
            PasswordResetToken::class,
            $user
        );
    }
}
