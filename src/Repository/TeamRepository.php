<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Team\Team;
use App\Entity\User\User;
use App\Enum\UserRole;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class TeamRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function indexByUser(User $user): QueryBuilder
    {
        $query = $this->index();

        if ($user->getRole() !== UserRole::ADMIN) {
            $query
                ->leftJoin('e.members', 'm')
                ->andWhere('m.user = :user')
                ->andWhere('m.accepted = true')
                ->setParameter('user', $user)
            ;
        }

        return $query;
    }
}