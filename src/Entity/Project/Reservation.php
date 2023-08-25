<?php

namespace App\Entity\Project;

use App\Entity\AbstractEntity;
use App\Entity\Event\Day;
use App\Entity\Event\Event;
use App\Entity\Location\Location;
use App\Entity\Location\Stand;
use App\Entity\Timeframe;
use App\Enum\SerializationGroup\Event\EventGroups;
use App\Enum\SerializationGroup\Location\StandGroups;
use App\Enum\StandType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
#[UniqueEntity(fields: ['name'])]
class Reservation extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[ORM\ManyToOne(targetEntity: Event::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Event $event;

    #[ORM\ManyToOne(targetEntity: Day::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Day $day;

    #[ORM\ManyToOne(targetEntity: Stand::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Stand $stand;

    #[ORM\OneToOne(targetEntity: Timeframe::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Timeframe $timeframe;

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): Reservation
    {
        $this->project = $project;
        return $this;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): Reservation
    {
        $this->event = $event;

        return $this;
    }

    public function getDay(): Day
    {
        return $this->day;
    }

    public function setDay(Day $day): Reservation
    {
        $this->day = $day;

        return $this;
    }

    public function getStand(): Stand
    {
        return $this->stand;
    }

    public function setStand(Stand $stand): Reservation
    {
        $this->stand = $stand;

        return $this;
    }

    public function getTimeframe(): Timeframe
    {
        return $this->timeframe;
    }

    public function setTimeframe(Timeframe $timeframe): Reservation
    {
        $this->timeframe = $timeframe;

        return $this;
    }
}
