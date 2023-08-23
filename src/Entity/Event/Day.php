<?php

namespace App\Entity\Event;

use App\Entity\AbstractEntity;
use App\Entity\Timeframe;
use App\Enum\SerializationGroup\Event\EventGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table]
class Day extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: Event::class)]
    #[ORM\JoinColumn]
    private Event $event;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Constraints\Date]
    #[Constraints\NotBlank(allowNull: false, groups: [EventGroups::CREATE])]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::INDEX,
        EventGroups::UPDATE,
    ])]
    #[Property(
        description: 'Date of the day',
        example: '2023-08-18',
    )]
    private DateTimeImmutable $date;

    #[ORM\ManyToOne(targetEntity: Timeframe::class)]
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

    public function setTimeframe(Timeframe $timeframe): Day
    {
        $this->timeframe = $timeframe;

        return $this;
    }
}
