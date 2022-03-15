<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RepositoryFactory
{
    public function __construct(
        protected ContainerInterface $container,
        protected ManagerRegistry $registry
    ) {}

    public function create(string $className): EntityRepository
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException($className . ' not found.');
        }

        $repositoryName = $className . 'Repository';
        if (!$this->container->has($repositoryName)) {
            $this->container->set($repositoryName, new EntityRepository($this->registry, $className));
        }

        return $this->container->get($repositoryName);
    }
}
