<?php

namespace App\Repository;

use App\Entity\Event\Day;
use App\Entity\Location\Stand;
use App\Entity\Project\Reservation;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findConfirmedByStandAndDay(Stand $stand, Day $day): array
    {
        $builder = $this->index();
        $builder
            ->andWhere('e.stand = :stand')
            ->andWhere('e.day = :day')
            ->andWhere('e.confirmed = true')
            ->setParameters([
                'stand' => $stand,
                'day' => $day
            ])
        ;

        return $builder->getQuery()->getResult();
    }
}
