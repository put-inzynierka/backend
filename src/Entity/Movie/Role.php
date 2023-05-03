<?php

namespace App\Entity\Movie;

use App\Entity\AbstractEntity;
use App\Entity\Person\Person;
use App\Enum\SerializationGroup\Movie\RoleGroups;
use App\Enum\SerializationGroup\Movie\MovieGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
class Role extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [RoleGroups::CREATE])]
    #[Groups([
        RoleGroups::CREATE,
        RoleGroups::SHOW,
        RoleGroups::INDEX,
        RoleGroups::UPDATE,
        MovieGroups::SHOW,
    ])]
    #[Property(
        description: 'The role portrayed by the cast member',
        example: 'Stanley Goodspeed',
    )]
    private string $role;

    #[ORM\ManyToOne(targetEntity: Movie::class, inversedBy: 'roles')]
    #[ORM\JoinColumn]
    #[Constraints\NotBlank(allowNull: false, groups: [RoleGroups::CREATE])]
    #[Groups([
        RoleGroups::CREATE,
        RoleGroups::SHOW,
        RoleGroups::INDEX,
        RoleGroups::UPDATE,
    ])]
    private Movie $movie;

    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'roles')]
    #[ORM\JoinColumn]
    #[Constraints\NotBlank(allowNull: false, groups: [RoleGroups::CREATE])]
    #[Groups([
        RoleGroups::CREATE,
        RoleGroups::SHOW,
        RoleGroups::INDEX,
        RoleGroups::UPDATE,
        MovieGroups::SHOW,
    ])]
    private Person $person;

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getMovie(): Movie
    {
        return $this->movie;
    }

    public function setMovie(Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    public function getPerson(): Person
    {
        return $this->person;
    }

    public function setPerson(Person $person): self
    {
        $this->person = $person;

        return $this;
    }
}
