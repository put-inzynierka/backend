<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project\Project;
use App\Entity\Team\Team;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class ProjectRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
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
}