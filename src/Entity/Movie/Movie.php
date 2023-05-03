<?php

namespace App\Entity\Movie;

use App\Entity\AbstractEntity;
use App\Entity\Person\Person;
use App\Enum\SerializationGroup\Movie\MovieGroups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
class Movie extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [MovieGroups::CREATE])]
    #[Groups([
        MovieGroups::CREATE,
        MovieGroups::SHOW,
        MovieGroups::INDEX,
        MovieGroups::UPDATE,
    ])]
    #[Property(
        description: 'The title of the movie',
        example: 'The Rock',
    )]
    private string $title;

    #[ORM\Column(type: Types::INTEGER)]
    #[Constraints\Type(type: Types::INTEGER)]
    #[Constraints\NotBlank(allowNull: false, groups: [MovieGroups::CREATE])]
    #[Groups([
        MovieGroups::CREATE,
        MovieGroups::SHOW,
        MovieGroups::INDEX,
        MovieGroups::UPDATE,
    ])]
    #[Property(
        description: 'The year the movie was released',
        example: 1996,
    )]
    private int $year;

    #[ORM\ManyToOne(targetEntity: Genre::class)]
    #[ORM\JoinColumn]
    #[Constraints\NotBlank(allowNull: false, groups: [MovieGroups::CREATE])]
    #[Groups([
        MovieGroups::CREATE,
        MovieGroups::SHOW,
        MovieGroups::INDEX,
        MovieGroups::UPDATE,
    ])]
    private Genre $genre;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn]
    #[Constraints\NotBlank(allowNull: false, groups: [MovieGroups::CREATE])]
    #[Groups([
        MovieGroups::CREATE,
        MovieGroups::SHOW,
        MovieGroups::INDEX,
        MovieGroups::UPDATE,
    ])]
    private Person $director;

    #[ORM\OneToMany(mappedBy: 'movie', targetEntity: Role::class)]
    #[Groups([
        MovieGroups::SHOW,
    ])]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Movie
    {
        $this->title = $title;

        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): Movie
    {
        $this->year = $year;

        return $this;
    }

    public function getGenre(): Genre
    {
        return $this->genre;
    }

    public function setGenre(Genre $genre): Movie
    {
        $this->genre = $genre;

        return $this;
    }

    public function getDirector(): Person
    {
        return $this->director;
    }

    public function setDirector(Person $director): Movie
    {
        $this->director = $director;

        return $this;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function setRoles(Collection $roles): Movie
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(Role $role): Movie
    {
        $this->roles->add($role);

        return $this;
    }

    public function removeRole(Role $role): Movie
    {
        $this->roles->removeElement($role);

        return $this;
    }
}
