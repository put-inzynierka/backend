<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Location\Location;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class LocationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function indexReservedByDay(string $dayId): QueryBuilder
    {
        $query = $this->index();

        $query
            ->innerJoin('e.stands', 's')
            ->innerJoin('s.reservations', 'r', Join::WITH, 'r.confirmed = true')
            ->andWhere('r.day = :day')
            ->setParameter('day', $dayId)
        ;

        return $query;
    }

    public function findWithParentByDay(string $id, string $eventId, string $dayId): ?Location
    {
        $query = $this->indexReservedByDay($dayId);
        $query
            ->innerJoin('e.events', 'events', Join::WITH, 'events.id = :eventId')
            ->andWhere('e.id = :id')
            ->setParameter('id', $id)
            ->setParameter('eventId', $eventId)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }
}