<?php declare(strict_types=1);

namespace App\Service\Team;

use App\Entity\Team\Team;
use App\Entity\Team\TeamMember;
use App\Entity\User\User;
use App\Enum\TeamMemberRole;
use App\Enum\UserRole;

final class TeamRoleService
{
    public function setEditable(Team $team, User $user): void
    {
        if ($this->getTeamRole($team, $user) === TeamMemberRole::OWNER) {
            $team->setEditable(true);
        }
    }

    public function setMyRole(Team $team, User $user): void
    {
        $team->setMyRole($this->getTeamRole($team, $user));
    }

    private function getTeamRole(Team $team, User $user): ?TeamMemberRole
    {
        if ($this->isAdmin($user)) {
            return TeamMemberRole::OWNER;
        }

        return $team->getMember($user)?->getRole();
    }

    private function isAdmin(User $user): bool
    {
        return $user->getRole() === UserRole::ADMIN;
    }
}