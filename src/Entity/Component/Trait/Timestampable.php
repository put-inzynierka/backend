<?php

namespace App\Entity\Component\Trait;

use App\Enum\SerializationGroup\BaseGroups;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;

trait Timestampable
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups([BaseGroups::TIMESTAMPS])]
    #[Property(
        description: 'The date and time the entity was created',
        example: '2021-01-01T00:00:00+00:00',
    )]
    private readonly DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups([BaseGroups::TIMESTAMPS])]
    #[Property(
        description: 'The date and time the entity was last modified',
        example: '2021-01-01T00:00:00+00:00',
    )]
    private DateTimeImmutable $modifiedAt;

    #[ORM\PrePersist]
    public function created(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function modified(): void
    {
        $this->modifiedAt = new DateTimeImmutable();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getModifiedAt(): DateTimeImmutable
    {
        return $this->modifiedAt;
    }
}
