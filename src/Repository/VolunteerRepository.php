<?php

namespace App\Repository;

use App\Entity\Event\Event;
use App\Entity\Event\Volunteer;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class VolunteerRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Volunteer::class);
    }

    public function indexByEvent(Event $event): QueryBuilder
    {
        $query = $this->index();
        $query
            ->andWhere('e.event = :event')
            ->setParameter('event', $event)
        ;

        return $query;
    }

    public function findWithParent(string $id, string $eventId): ?Volunteer
    {
        $query = $this->index();
        $query
            ->andWhere('e.id = :id')
            ->andWhere('e.event = :eventId')
            ->setParameter('id', $id)
            ->setParameter('eventId', $eventId)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }
}
