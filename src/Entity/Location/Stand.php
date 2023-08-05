<?php

namespace App\Entity\Location;

use App\Entity\AbstractEntity;
use App\Enum\SerializationGroup\Location\StandGroups;
use App\Enum\StandType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
#[UniqueEntity(fields: ['name'])]
class Stand extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [StandGroups::CREATE])]
    #[Groups([
        StandGroups::CREATE,
        StandGroups::SHOW,
        StandGroups::INDEX,
        StandGroups::UPDATE,
    ])]
    #[Property(
        description: 'The name of the stand',
        example: 'B43',
    )]
    private string $name;

    #[ORM\Column(type: Types::STRING, enumType: StandType::class)]
    #[Groups([
        StandGroups::CREATE,
        StandGroups::SHOW,
        StandGroups::INDEX,
        StandGroups::UPDATE,
    ])]
    private StandType $type;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'stands')]
    #[ORM\JoinColumn()]
    #[Groups([
        StandGroups::SHOW,
    ])]
    private Location $location;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): StandType
    {
        return $this->type;
    }

    public function setType(StandType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }
}