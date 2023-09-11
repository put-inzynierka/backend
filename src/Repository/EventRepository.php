<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event\Event;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Request\ParamFetcherInterface;

final class EventRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Event::class);
    }

    public function indexByUpcoming(?string $upcoming): QueryBuilder
    {
        $query = $this->index();

        if ($upcoming !== null) {
            $query
                ->innerJoin('e.days', 'd')
                ->andWhere('d.date > :now')
                ->orderBy('d.date', 'ASC')
                ->setParameter('now', new \DateTime());
        }

        return $query;
    }
}