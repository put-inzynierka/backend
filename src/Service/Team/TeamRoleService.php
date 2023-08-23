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
        if ($this->isAdmin($user) || $this->getTeamRole($team, $user) === TeamMemberRole::OWNER) {
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

        /** @var TeamMember|null $teamMember */
        $teamMember = $team->getTeamMembers()->findFirst(function (string|int $key, TeamMember $teamMember) use ($user) {
            return $teamMember->isAccepted() && $teamMember->getUser() === $user;
        });

        return $teamMember?->getRole();
    }

    private function isAdmin(User $user): bool
    {
        return $user->getRole() === UserRole::ADMIN;
    }
}