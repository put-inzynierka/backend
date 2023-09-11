<?php

namespace App\Helper;

use Doctrine\ORM\QueryBuilder;
use App\Component\Model\PaginatedList;

class Paginator
{
    public static function paginate(QueryBuilder $builder, ?int $page, ?int $limit): PaginatedList
    {
        $totalCount = Paginator::count($builder);

        if (!$page) {
            return new PaginatedList(
                $totalCount,
                1,
                $totalCount,
                1,
                $builder->getQuery()->getResult()
            );
        }

        $builder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
        ;

        return new PaginatedList(
            $totalCount,
            $page,
            $limit,
            ceil($totalCount / $limit),
            $builder->getQuery()->getResult()
        );
    }

    public static function wrap(QueryBuilder $builder): PaginatedList
    {
        $totalCount = Paginator::count($builder);

        return new PaginatedList(
            $totalCount,
            1,
            $totalCount,
            1,
            $builder->getQuery()->getResult()
        );
    }

    public static function wrapArray(array $array): PaginatedList
    {
        $totalCount = count($array);

        return new PaginatedList(
            $totalCount,
            1,
            $totalCount,
            1,
            $array
        );
    }

    protected static function count(QueryBuilder $builder): int
    {
        $immutableBuilder = clone $builder;
        $alias = $immutableBuilder->getRootAliases()[0];

        $counts = $immutableBuilder
            ->resetDQLPart('orderBy')
            ->select('count(' . $alias . '.id)')
            ->getQuery()
            ->getScalarResult()
        ;
        
        return count($counts) === 1 ? (int) $counts[0][1] : count($counts);
    }
}
