<?php

namespace App\Voter;

use App\Entity\Game\Game;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class GameVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, Qualifier::getAll())) {
            return false;
        }

        if (!$subject instanceof Game) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return match ($attribute) {
            Qualifier::IS_OWNER => $this->isOwner($user, $subject),
            default => true,
        };
    }

    private function isOwner(?UserInterface $user, Game $game): bool
    {
        return $user && $game->getOwner()->getUserIdentifier() === $user->getUserIdentifier();
    }
}