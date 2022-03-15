<?php

namespace App\Voter;

use App\Entity\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class NullVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        if ($attribute !== Qualifier::IS_AUTHENTICATED) {
            return false;
        }

        if ($subject !== null) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return $this->isLoggedIn($user);
    }

    private function isLoggedIn(?UserInterface $user): bool
    {
        return $user instanceof User;
    }
}