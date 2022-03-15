<?php

namespace App\Repository;

use App\Entity\User\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityUserRepository extends AbstractRepository implements UserRepositoryInterface
{
    protected UserPasswordHasherInterface $passwordHasher;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, User::class);

        $this->passwordHasher = $passwordHasher;
    }

    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
        $user = $this->findOneBy(['email' => $username]);
        if (!$user) {
            return null;
        }

        $isPasswordValid = $this->passwordHasher->isPasswordValid($user, $password);
        if (!$isPasswordValid) {
            return null;
        }

        return $user;
    }

    public function getUserByIdentifier(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }
}