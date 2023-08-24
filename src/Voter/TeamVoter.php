<?php

namespace App\Voter;

use App\Entity\Team\Team;
use App\Entity\User\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TeamVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof Team;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            Qualifier::IS_OWNER     => $this->canManage($user, $subject),
            Qualifier::HAS_ACCESS   => $this->isMember($user, $subject),
            default                 => false,
        };
    }

    private function canManage(User $user, Team $subject): bool
    {
        return (bool) $subject->getMember($user)?->getRole()->canManage();
    }

    private function isMember(User $user, Team $subject): bool
    {
        return (bool) $subject->getMember($user);
    }
}