<?php

namespace App\Component\Model\AvailableStand;

use App\Entity\Event\Event as EventEntity;
use App\Enum\SerializationGroup\BaseGroups;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;

class Event
{
    public function __construct(
        #[Groups([BaseGroups::DEFAULT])]
        protected int $id,

        #[Groups([BaseGroups::DEFAULT])]
        protected string $name,

        /** @var Day[] $days */
        #[Groups([BaseGroups::DEFAULT])]
        protected array $days
    ) {}

    public static function fromEntity(EventEntity $entity): Event
    {
        return new self(
            $entity->getId(),
            $entity->getName(),
            []
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

    public function getDays(): array
    {
        return $this->days;
    }

    public function setDays(array $days): Event
    {
        $this->days = $days;

        return $this;
    }
}