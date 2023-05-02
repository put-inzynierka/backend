<?php

namespace App\Entity\Component\Trait;

use App\Enum\SerializationGroup\BaseGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;

trait Identifiable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups([BaseGroups::DEFAULT])]
    #[Property(
        description: 'The ID of the entity',
        example: 1,
    )]
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }
}
