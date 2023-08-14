<?php declare(strict_types=1);

namespace App\Service\Team;

use App\Entity\Team\Team;
use App\Entity\Team\TeamMember;
use App\Entity\User\User;
use App\Enum\TeamMemberRole;
use Doctrine\ORM\EntityManagerInterface;

class TeamCreator
{
    public function __construct(
      private readonly EntityManagerInterface $manager
    ) {
    }

    public function create(Team $team, User $user): void
    {
        $teamMember = new TeamMember();
        $teamMember->setTeam($team);
        $teamMember->setUser($user);
        $teamMember->setEmail($user->getEmail());
        $teamMember->setAccepted(true);
        $teamMember->setRole(TeamMemberRole::OWNER);

        $team->addTeamMember($teamMember);

        $this->manager->persist($team);
        $this->manager->flush();
    }
}