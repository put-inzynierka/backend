<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Team\Team;
use App\Entity\Team\TeamMember;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class TeamMemberRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamMember::class);
    }

    public function indexByTeam(Team $team): QueryBuilder
    {
        $query = $this->index();
        $query
            ->andWhere('e.team = :team')
            ->setParameter('team', $team)
        ;

        return $query;
    }

    public function invitationsByEmail(string $email): QueryBuilder
    {
        $query = $this->index();
        $query
            ->andWhere('e.email = :email')
            ->andWhere('e.accepted = false')
            ->setParameter('email', $email)
        ;

        return $query;
    }
}
