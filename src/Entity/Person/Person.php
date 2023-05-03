<?php

namespace App\Entity\Person;

use App\Entity\AbstractEntity;
use App\Entity\Movie\Role;
use App\Enum\SerializationGroup\Movie\MovieGroups;
use App\Enum\SerializationGroup\Person\PersonGroups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
class Person extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [PersonGroups::CREATE])]
    #[Groups([
        PersonGroups::CREATE,
        PersonGroups::SHOW,
        PersonGroups::INDEX,
        PersonGroups::UPDATE,
        MovieGroups::INDEX,
        MovieGroups::SHOW,
    ])]
    #[Property(
        description: 'The first name of the person',
        example: 'Nicolas',
    )]
    private string $firstName;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [PersonGroups::CREATE])]
    #[Groups([
        PersonGroups::CREATE,
        PersonGroups::SHOW,
        PersonGroups::INDEX,
        PersonGroups::UPDATE,
        MovieGroups::INDEX,
        MovieGroups::SHOW,
    ])]
    #[Property(
        description: 'The last name of the person',
        example: 'Cage',
    )]
    private string $lastName;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: Role::class)]
    #[Groups([
        PersonGroups::SHOW,
    ])]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $name): self
    {
        $this->firstName = $name;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $name): self
    {
        $this->lastName = $name;

        return $this;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }
}
