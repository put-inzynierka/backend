<?php

namespace App\Repository;

use App\Entity\Event\Day;
use App\Entity\Location\Stand;
use App\Entity\Project\Reservation;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Request\ParamFetcherInterface;

class ReservationRepository extends AbstractRepository
{
    public function __construct(
        protected ParamFetcherInterface $paramFetcher,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Reservation::class);
    }

    public function index(): QueryBuilder
    {
        $query = parent::index();
        
        if ($this->paramFetcher->get('confirmed')) {
            $confirmed = $this->paramFetcher->get('confirmed');
            if ($confirmed === 'true') {
                $confirmed = true;
            } elseif ($confirmed === 'false') {
                $confirmed = false;
            } else {
                $confirmed = null;
            }
            
            $query
                ->andWhere('e.confirmed = :confirmed')
                ->setParameter('confirmed', $confirmed)
            ;
        }

        return $query;
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
