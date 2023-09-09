<?php

namespace App\Entity\Event;

use App\Entity\AbstractEntity;
use App\Entity\Component\Contract\Timeframeable;
use App\Entity\Timeframe;
use App\Enum\SerializationGroup\Event\EventGroups;
use App\Enum\SerializationGroup\Event\VolunteerGroups;
use App\Enum\SerializationGroup\Project\ReservationGroups;
use App\Repository\DayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: DayRepository::class)]
#[ORM\Table]
class Day extends AbstractEntity implements Timeframeable
{
    #[ORM\ManyToOne(targetEntity: Event::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Event $event;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Constraints\Date]
    #[Constraints\NotBlank(allowNull: false, groups: [EventGroups::CREATE])]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::INDEX,
        EventGroups::UPDATE,
        VolunteerGroups::INDEX,
        ReservationGroups::INDEX,
    ])]
    #[Property(
        description: 'Date of the day',
        example: '2023-08-18',
    )]
    private DateTimeImmutable $date;

    #[ORM\OneToOne(targetEntity: Timeframe::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Constraints\NotBlank(allowNull: false, groups: [EventGroups::CREATE])]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::INDEX,
        EventGroups::UPDATE,
    ])]
    #[Property(
        description: 'Timeframe of the day',
    )]
    private Timeframe $timeframe;

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): Day
    {
        $this->event = $event;

        return $this;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): Day
    {
        $this->date = $date;

        return $this;
    }

    public function getTimeframe(): Timeframe
    {
        return $this->timeframe;
    }

    public function getTimeframes(): ArrayCollection
    {
        return new ArrayCollection([$this->timeframe]);
    }

    public function setTimeframe(Timeframe $timeframe): Day
    {
        $this->timeframe = $timeframe;

        return $this;
    }
}
