<?php

namespace App\Entity\Project;

use App\Component\Model\ContainmentValidationRule;
use App\Entity\AbstractEntity;
use App\Entity\Component\Contract\ContainmentValidatable;
use App\Entity\Component\Contract\Timeframeable;
use App\Entity\Event\Day;
use App\Entity\Event\Event;
use App\Entity\Location\Stand;
use App\Entity\Timeframe;
use App\Enum\SerializationGroup\Project\ReservationGroups;
use App\Service\Validation\StandValidator;
use App\Service\Validation\TimeframeValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table]
class Reservation extends AbstractEntity implements Timeframeable, ContainmentValidatable
{
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        ReservationGroups::INDEX,
    ])]
    private Project $project;

    #[ORM\ManyToOne(targetEntity: Event::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Constraints\NotBlank(allowNull: false, groups: [ReservationGroups::CREATE])]
    #[Constraints\Valid(groups: [
        ReservationGroups::CREATE,
        ReservationGroups::UPDATE,
    ])]
    #[Groups([
        ReservationGroups::CREATE,
        ReservationGroups::UPDATE,
        ReservationGroups::INDEX,
    ])]
    private Event $event;

    #[ORM\ManyToOne(targetEntity: Day::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Constraints\NotBlank(allowNull: false, groups: [ReservationGroups::CREATE])]
    #[Groups([
        ReservationGroups::CREATE,
        ReservationGroups::UPDATE,
        ReservationGroups::INDEX,
    ])]
    private Day $day;

    #[ORM\ManyToOne(targetEntity: Stand::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Constraints\NotBlank(allowNull: false, groups: [ReservationGroups::CREATE])]
    #[Groups([
        ReservationGroups::CREATE,
        ReservationGroups::UPDATE,
        ReservationGroups::INDEX,
    ])]
    private Stand $stand;

    #[ORM\OneToOne(targetEntity: Timeframe::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Constraints\NotBlank(allowNull: false, groups: [ReservationGroups::CREATE])]
    #[Groups([
        ReservationGroups::CREATE,
        ReservationGroups::UPDATE,
        ReservationGroups::INDEX,
    ])]
    private Timeframe $timeframe;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    #[Constraints\Type(Types::BOOLEAN)]
    #[Groups([
        ReservationGroups::ADMIN_UPDATE,
        ReservationGroups::INDEX,
    ])]
    private ?bool $confirmed;

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

    public function getDate(): DateTimeImmutable
    {
        return $this->day->getDate();
    }

    public function getTimeframes(): Collection
    {
        return new ArrayCollection([$this->timeframe]);
    }

    public function isConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(?bool $confirmed): Reservation
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getContainmentValidationRules(): array
    {
        return [
            new ContainmentValidationRule(
                TimeframeValidator::class,
                [$this],
                $this->getEvent()->getDays()
            ),
            new ContainmentValidationRule(
                StandValidator::class,
                [$this->getStand()],
                $this->getEvent()->getLocations()
            )
        ];
    }
}
