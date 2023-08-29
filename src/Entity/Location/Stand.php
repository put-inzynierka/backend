<?php

namespace App\Entity\Location;

use App\Entity\AbstractEntity;
use App\Enum\SerializationGroup\Event\EventGroups;
use App\Enum\SerializationGroup\Location\StandGroups;
use App\Enum\SerializationGroup\Project\ReservationGroups;
use App\Enum\StandType;
use App\Repository\StandRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity(repositoryClass: StandRepository::class)]
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
        EventGroups::SHOW,
        ReservationGroups::INDEX,
    ])]
    #[Property(
        description: 'The name of the stand',
        example: 'B43',
    )]
    private string $name;

    #[ORM\Column(type: Types::STRING, enumType: StandType::class)]
    #[Constraints\NotBlank(allowNull: false, groups: [StandGroups::CREATE])]
    #[Constraints\Choice(callback: [StandType::class, 'cases'])]
    #[Groups([
        StandGroups::CREATE,
        StandGroups::SHOW,
        StandGroups::INDEX,
        StandGroups::UPDATE,
    ])]
    #[Property(
        description: 'The type of the stand',
        enum: ['stand', 'stage'],
        example: 'stand',
    )]
    private StandType $type;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'stands')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        StandGroups::SHOW,
        ReservationGroups::INDEX,
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
