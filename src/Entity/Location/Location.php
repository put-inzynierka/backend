<?php

namespace App\Entity\Location;

use App\Entity\AbstractEntity;
use App\Entity\Movie\Role;
use App\Enum\SerializationGroup\Movie\MovieGroups;
use App\Enum\SerializationGroup\Location\LocationGroups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
#[UniqueEntity(fields: ['name'])]
class Location extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [LocationGroups::CREATE])]
    #[Groups([
        LocationGroups::CREATE,
        LocationGroups::SHOW,
        LocationGroups::INDEX,
        LocationGroups::UPDATE,
    ])]
    #[Property(
        description: 'The name of the location',
        example: 'Pawilon A',
    )]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: Stand::class)]
    #[Groups([
        LocationGroups::SHOW,
    ])]
    private Collection $stands;

    public function __construct()
    {
        $this->stands = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStands(): Collection
    {
        return $this->stands;
    }

    public function addStand(Stand $stand): self
    {
        if (!$this->stands->contains($stand)) {
            $this->stands[] = $stand;
            $stand->setLocation($this);
        }

        return $this;
    }

    public function removeStand(Stand $stand): self
    {
        if ($this->stands->removeElement($stand)) {
            // set the owning side to null (unless already changed)
            if ($stand->getLocation() === $this) {
                $stand->setLocation(null);
            }
        }

        return $this;
    }
}
