<?php

namespace App\Entity\Event;

use App\Entity\AbstractEntity;
use App\Entity\Component\Contract\Timeframeable;
use App\Entity\Timeframe;
use App\Enum\SerializationGroup\Event\EventGroups;
use App\Enum\SerializationGroup\Event\VolunteerGroups;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity]
#[ORM\Table]
class VolunteerAvailability extends AbstractEntity implements Timeframeable
{
    #[ORM\ManyToOne(targetEntity: Volunteer::class)]
    #[ORM\JoinColumn]
    private Volunteer $volunteer;

    #[ORM\ManyToOne(targetEntity: Day::class)]
    #[ORM\JoinColumn]
    #[Groups([
        VolunteerGroups::CREATE,
        VolunteerGroups::INDEX,
    ])]
    private Day $day;

    #[ORM\ManyToMany(targetEntity: Timeframe::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinTable(name: 'event.volunteer_availabilities_timeframes')]
    #[ORM\JoinColumn(name: 'volunteer_availability_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'timeframe_id', referencedColumnName: 'id')]
    #[Constraints\NotBlank(allowNull: false, groups: [EventGroups::CREATE])]
    #[Groups([
        VolunteerGroups::CREATE,
        VolunteerGroups::INDEX,
    ])]
    #[Property(
        description: 'Timeframe of the day',
    )]
    private Collection $timeframes;

    public function __construct()
    {
        $this->timeframes = new ArrayCollection();
    }

    public function getVolunteer(): Volunteer
    {
        return $this->volunteer;
    }

    public function setVolunteer(Volunteer $volunteer): VolunteerAvailability
    {
        $this->volunteer = $volunteer;

        return $this;
    }

    public function getDay(): Day
    {
        return $this->day;
    }

    public function setDay(Day $day): VolunteerAvailability
    {
        $this->day = $day;

        return $this;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->day->getDate();
    }

    public function getTimeframes(): Collection
    {
        return $this->timeframes;
    }

    public function setTimeframes(Collection $timeframes): VolunteerAvailability
    {
        $this->timeframes = $timeframes;

        return $this;
    }

    public function addTimeframe(Timeframe $timeframe): VolunteerAvailability
    {
        if (!$this->timeframes->contains($timeframe)) {
            $this->timeframes[] = $timeframe;
        }

        return $this;
    }

    public function removeTimeframe(Timeframe $timeframe): self
    {
        $this->timeframes->removeElement($timeframe);

        return $this;
    }
}
