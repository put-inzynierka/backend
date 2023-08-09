<?php

namespace App\Service\User;

use App\Entity\Component\Contract\Token;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use ReflectionClass;

class TokenFactory
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {}

    public function create(string $className, User $user): Token
    {
        $class = new ReflectionClass($className);
        if (!$class->implementsInterface(Token::class)) {
            throw new InvalidArgumentException($className . ' must implement ' . Token::class . '.');
        }

        $repository = $this->entityManager->getRepository($className);
        do {
            $value = bin2hex(random_bytes(32));
        } while ($repository->findOneBy(['value' => $value]));

        return new $className($value, $user);
    }
}
