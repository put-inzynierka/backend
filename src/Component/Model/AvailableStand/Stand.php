<?php

namespace App\Component\Model\AvailableStand;

use App\Entity\Location\Stand as StandEntity;
use App\Enum\SerializationGroup\BaseGroups;
use Symfony\Component\Serializer\Annotation\Groups;

class Stand
{
    public function __construct(
        #[Groups([BaseGroups::DEFAULT])]
        protected int $id,

        #[Groups([BaseGroups::DEFAULT])]
        protected string $name,
    ) {}

    public static function fromEntity(StandEntity $entity): Stand
    {
        return new self(
            $entity->getId(),
            $entity->getName()
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}