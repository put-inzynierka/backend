<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Project\Project;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class ProjectRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function indexByTeamId(string $teamId): QueryBuilder
    {
        $query = $this->index();

        if ($teamId !== '') {
            $query
                ->andWhere('e.team = :teamId')
                ->setParameter('teamId', $teamId)
            ;
        }

        return $query;
    }
}