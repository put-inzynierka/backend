<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event\Announcement;
use App\Entity\Event\Event;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class AnnouncementRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Announcement::class);
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

    public function findWithParent(string $id, string $eventId): ?Announcement
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