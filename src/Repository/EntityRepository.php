<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;

class EntityRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry, string $class)
    {
        parent::__construct($registry, $class);
    }
}
