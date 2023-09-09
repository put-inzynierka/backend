<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event\Day;
use Doctrine\Persistence\ManagerRegistry;

final class DayRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Day::class);
    }

    public function findWithParent(string $id, string $eventId): ?Day
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