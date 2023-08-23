<?php

namespace App\Voter;

use App\Entity\User\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $actor = $token->getUser();

        return match ($attribute) {
            Qualifier::IS_OWNER => $this->isLoggedUser($actor, $subject),
            default             => false,
        };
    }

    private function isLoggedUser(?User $actor, User $subject): bool
    {
        return $actor instanceof User && $actor->getId() === $subject->getId();
    }
}