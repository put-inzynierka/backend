<?php

namespace App\Repository;

use App\Entity\Location\Location;
use App\Entity\Location\Stand;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class StandRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stand::class);
    }

    public function indexByLocation(Location $location): QueryBuilder
    {
        $query = $this->index();
        $query
            ->andWhere('e.location = :location')
            ->setParameter('location', $location)
        ;

        return $query;
    }

    public function findWithParent(string $id, string $locationId): ?Stand
    {
        $query = $this->index();
        $query
            ->andWhere('e.id = :id')
            ->andWhere('e.location = :locationId')
            ->setParameter('id', $id)
            ->setParameter('locationId', $locationId)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }
}
