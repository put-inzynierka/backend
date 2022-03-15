<?php

namespace App\Helper;

use Doctrine\ORM\QueryBuilder;
use App\Component\Model\PaginatedList;

class Paginator
{
    public static function paginate(QueryBuilder $builder, ?int $page, ?int $limit): PaginatedList
    {
        if (!$page) {
            return new PaginatedList(
                Paginator::count($builder),
                1,
                1,
                $builder->getQuery()->getResult()
            );
        }

        $totalCount = Paginator::count($builder);
        $builder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
        ;

        return new PaginatedList(
            $totalCount,
            $page,
            ceil($totalCount / $limit),
            $builder->getQuery()->getResult()
        );
    }

    protected static function count(QueryBuilder $builder): int
    {
        $immutableBuilder = clone $builder;
        $alias = $immutableBuilder->getRootAliases()[0];

        return $immutableBuilder
            ->resetDQLPart('orderBy')
            ->select('count(' . $alias . '.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}