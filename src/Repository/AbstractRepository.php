<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function index(): QueryBuilder
    {
        $builder = $this->createQueryBuilder('e');

        $builder->orderBy('e.id', 'desc');

        return $builder;
    }
}
