<?php

namespace App\Voter;

use App\Entity\Game\Key;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class KeyVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, Qualifier::getAll())) {
            return false;
        }

        if (!$subject instanceof Key) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return match ($attribute) {
            Qualifier::IS_OWNER => $this->isOwner($user, $subject),
            Qualifier::HAS_ACCESS => $this->hasAccess($user, $subject),
            default => true,
        };
    }

    private function isOwner(UserInterface $user, Key $key): bool
    {
        $game = $key->getGame();

        return $game->getOwner()->getUserIdentifier() === $user->getUserIdentifier();
    }

    private function hasAccess(UserInterface $user, Key $key): bool
    {
        return $this->isOwner($user, $key);
    }
}