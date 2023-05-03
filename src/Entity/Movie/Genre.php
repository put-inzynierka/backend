<?php

namespace App\Entity\Movie;

use App\Entity\AbstractEntity;
use App\Enum\SerializationGroup\Movie\GenreGroups;
use App\Enum\SerializationGroup\Movie\MovieGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
#[UniqueEntity(fields: ['name'])]
class Genre extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [GenreGroups::CREATE])]
    #[Groups([
        GenreGroups::CREATE,
        GenreGroups::SHOW,
        GenreGroups::INDEX,
        GenreGroups::UPDATE,
        MovieGroups::SHOW,
        MovieGroups::INDEX,
    ])]
    #[Property(
        description: 'The name of the genre',
        example: 'Action',
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
