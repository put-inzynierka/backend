<?php

namespace App\Voter;

use App\Entity\User\User;
use App\Enum\UserRole;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class NullVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [Qualifier::IS_AUTHENTICATED, Qualifier::IS_ADMIN])) {
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

        return match ($attribute) {
            Qualifier::IS_AUTHENTICATED => $this->isLoggedIn($user),
            Qualifier::IS_ADMIN         => $this->isAdmin($user),
            default                     => false,
        };
    }

    private function isLoggedIn(?UserInterface $user): bool
    {
        return $user instanceof User;
    }

    private function isAdmin(?UserInterface $user): bool
    {
        return $user instanceof User && $user->getRole() === UserRole::ADMIN;
    }
}