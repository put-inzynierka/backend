<?php declare(strict_types=1);

namespace App\Service\Team;

use App\Entity\Team\Team;
use App\Entity\Team\TeamMember;
use App\Entity\User\User;
use App\Service\Mail\TeamInvitationMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TeamInvitationService
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly TeamInvitationMailer $mailer
    ) {
    }

    public function invite(Team $team, TeamMember $teamMember): void
    {
        $teamMember->setTeam($team);

        $this->manager->persist($teamMember);
        $this->manager->flush();

        $this->mailer->send($teamMember);
    }

    public function accept(TeamMember $teamMember, User $user): void
    {
        if ($teamMember->getEmail() !== $user->getEmail()) {
            throw new AccessDeniedException('You can only accept your invitation');
        }

        $teamMember->setUser($user);
        $teamMember->setAccepted(true);

        $this->manager->persist($teamMember);
        $this->manager->flush();
    }
}