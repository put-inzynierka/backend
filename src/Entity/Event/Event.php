<?php

namespace App\Entity\Event;

use App\Entity\AbstractEntity;
use App\Entity\File\File;
use App\Entity\Location\Location;
use App\Enum\SerializationGroup\Event\EventGroups;
use App\Enum\SerializationGroup\Event\VolunteerGroups;
use App\Enum\SerializationGroup\Project\ReservationGroups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints;
use DateTimeImmutable;
use DateTimeInterface;

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
        ReservationGroups::INDEX,
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

    #[ORM\ManyToOne(targetEntity: File::class)]
    #[ORM\JoinColumn(referencedColumnName: 'uuid', nullable: false)]
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

    #[ORM\ManyToMany(targetEntity: Location::class, inversedBy: 'events')]
    #[ORM\JoinTable(name: 'events.events_locations')]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'location_id', referencedColumnName: 'id', nullable: false)]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::UPDATE,
    ])]
    #[Property(
        description: 'The locations the event is taking place at',
    )]
    private Collection $locations;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Day::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['date' => 'asc'])]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::UPDATE,
    ])]
    #[Property(
        description: 'The days the event is taking place at',
    )]
    private Collection $days;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Constraints\Type(DateTimeInterface::class)]
    #[Constraints\NotBlank(allowNull: false, groups: [EventGroups::CREATE])]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::INDEX,
        EventGroups::UPDATE,
    ])]
    #[Property(
        description: 'Time the teams need to register by',
        example: '2023-08-18 23:59',
    )]
    private DateTimeImmutable $teamRegistrationEndsAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Constraints\Type(DateTimeInterface::class)]
    #[Constraints\NotBlank(allowNull: false, groups: [EventGroups::CREATE])]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::INDEX,
        EventGroups::UPDATE,
    ])]
    #[Property(
        description: 'Time the volunteers need to register by',
        example: '2023-08-18 23:59',
    )]
    private DateTimeImmutable $volunteerRegistrationEndsAt;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
        $this->days = new ArrayCollection();
    }

    #[SerializedName('first_day')]
    #[Groups([
        EventGroups::INDEX,
        EventGroups::SHOW,
    ])]
    public function getFirstDay(): ?Day
    {
        return $this->days->first() ?: null;
    }

    #[SerializedName('last_day')]
    #[Groups([
        EventGroups::INDEX,
        EventGroups::SHOW,
    ])]
    public function getLastDay(): ?Day
    {
        return $this->days->last() ?: null;
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

    public function getTeamRegistrationEndsAt(): DateTimeImmutable
    {
        return $this->teamRegistrationEndsAt;
    }

    public function setTeamRegistrationEndsAt(DateTimeImmutable $datetime): Event
    {
        $this->teamRegistrationEndsAt = $datetime;

        return $this;
    }

    public function getVolunteerRegistrationEndsAt(): DateTimeImmutable
    {
        return $this->volunteerRegistrationEndsAt;
    }

    public function setVolunteerRegistrationEndsAt(DateTimeImmutable $datetime): Event
    {
        $this->volunteerRegistrationEndsAt = $datetime;

        return $this;
    }
}
