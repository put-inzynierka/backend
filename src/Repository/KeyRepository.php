<?php

namespace App\Repository;

use App\Entity\Game\Key;
use App\Entity\Game\Game;
use Doctrine\Persistence\ManagerRegistry;

class KeyRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Key::class);
    }

    public function indexByGame(Game $game)
    {
        $query = $this->index();
        $query
            ->andWhere('e.game = :game')
            ->setParameter('game', $game)
        ;

        return $query;
    }
}