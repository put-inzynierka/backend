<?php

namespace App\Repository;

use App\Entity\Movie\Movie;
use App\Entity\Movie\Role;
use Doctrine\Persistence\ManagerRegistry;

class RoleRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function indexByMovie(Movie $movie)
    {
        $query = $this->index();
        $query
            ->andWhere('e.movie = :movie')
            ->setParameter('movie', $movie)
        ;

        return $query;
    }
}
