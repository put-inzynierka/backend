<?php

namespace App\Repository;

use App\Entity\Location\Location;
use App\Entity\Location\Stand;
use Doctrine\Persistence\ManagerRegistry;

class StandRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stand::class);
    }

    public function indexByLocation(Location $location)
    {
        $query = $this->index();
        $query
            ->andWhere('e.location = :location')
            ->setParameter('location', $location)
        ;

        return $query;
    }
}
