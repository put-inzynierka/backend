<?php

namespace App\Entity\Component\Trait;

use App\Enum\SerializationGroup\BaseGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

trait UUIdentifiable
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID)]
    #[Groups([BaseGroups::DEFAULT])]
    #[Property(
        description: 'The UUID of the entity',
        example: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
    )]
    private Uuid|string $uuid;

    #[ORM\PostLoad]
    public function loaded(): void
    {
        if (is_string($this->uuid)) {
            $this->uuid = Uuid::fromString($this->uuid);
        }
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }
}
