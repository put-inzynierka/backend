<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event\Day;
use App\Entity\Location\Location;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class LocationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function indexReservedByDay(Day $day): QueryBuilder
    {
        $query = $this->index();

        $query
            ->innerJoin('e.stands', 's')
            ->innerJoin('s.reservations', 'r')
            ->andWhere('r.day = :day')
            ->andWhere('r.confirmed = true')
            ->setParameter('day', $day)
        ;

        return $query;
    }
}