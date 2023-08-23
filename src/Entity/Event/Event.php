<?php

namespace App\Entity\Event;

use App\Entity\AbstractEntity;
use App\Entity\File\File;
use App\Entity\Location\Location;
use App\Enum\SerializationGroup\Event\EventGroups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
class Event extends AbstractEntity
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1, max: 255)]
    #[Constraints\NotBlank(allowNull: false, groups: [EventGroups::CREATE])]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::INDEX,
        EventGroups::UPDATE,
    ])]
    #[Property(
        description: 'The name of the event',
        example: 'RoboDay 2023',
    )]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    #[Constraints\Type(type: Types::STRING)]
    #[Constraints\Length(min:1)]
    #[Constraints\NotBlank(allowNull: false, groups: [EventGroups::CREATE])]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::INDEX,
        EventGroups::UPDATE,
    ])]
    #[Property(
        description: 'The description of the event',
        example: 'RoboDay to wydarzenie zrzeszające roboty.',
    )]
    private string $description;

    #[ORM\OneToOne(targetEntity: File::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Constraints\NotBlank(allowNull: false, groups: [EventGroups::CREATE])]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::INDEX,
        EventGroups::UPDATE,
    ])]
    #[Property(
        description: 'The image of the event',
    )]
    private File $image;

    #[ORM\ManyToMany(targetEntity: Location::class)]
    #[ORM\JoinTable(name: 'event.events_locations')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'location_id', referencedColumnName: 'id')]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::UPDATE,
    ])]
    private Collection $locations;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Day::class)]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::UPDATE,
    ])]
    private Collection $days;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
        $this->days = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Event
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Event
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): File
    {
        return $this->image;
    }

    public function setImage(File $image): Event
    {
        $this->image = $image;

        return $this;
    }

    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function setLocations(Collection $locations): Event
    {
        $this->locations = $locations;

        return $this;
    }

    public function addLocation(Location $location): Event
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
        }

        return $this;
    }

    public function removeLocation(Location $location): self
    {
        $this->locations->removeElement($location);

        return $this;
    }

    public function getDays(): Collection
    {
        return $this->days;
    }

    public function setDays(Collection $days): Event
    {
        $this->days = $days;

        return $this;
    }

    public function addDay(Day $day): self
    {
        if (!$this->days->contains($day)) {
            $this->days[] = $day;
            $day->setEvent($this);
        }

        return $this;
    }

    public function removeDay(Day $day): self
    {
        $this->days->removeElement($day);

        return $this;
    }
}
