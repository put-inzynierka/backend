<?php

namespace App\Entity\Game;

use App\Entity\AbstractEntity;
use App\Enum\SerializationGroup\Game\GameGroups;
use App\Enum\SerializationGroup\Game\KeyGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
#[UniqueEntity(fields: 'name')]
class Platform extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [GameGroups::CREATE])]
    #[Groups([
        GameGroups::CREATE,
        GameGroups::SHOW,
        GameGroups::INDEX,
        GameGroups::UPDATE,
        KeyGroups::INDEX,
        KeyGroups::SHOW,
    ])]
    #[Property(
        description: 'The name of the platform',
        example: 'Steam',
    )]
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
