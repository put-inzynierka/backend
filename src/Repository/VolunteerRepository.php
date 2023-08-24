<?php

namespace App\Repository;

use App\Entity\Event\Event;
use App\Entity\Event\Volunteer;
use Doctrine\Persistence\ManagerRegistry;

class VolunteerRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Volunteer::class);
    }

    public function indexByEvent(Event $event)
    {
        $query = $this->index();
        $query
            ->andWhere('e.event = :event')
            ->setParameter('event', $event)
        ;

        return $query;
    }
}
