<?php

namespace App\Entity\Event;

use App\Entity\AbstractEntity;
use App\Entity\User\User;
use App\Enum\SerializationGroup\Event\VolunteerGroups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table]
class Volunteer extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: Event::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Event $event;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        VolunteerGroups::INDEX,
    ])]
    private User $user;

    #[ORM\OneToMany(mappedBy: 'volunteer', targetEntity: VolunteerAvailability::class, cascade: ['persist', 'remove'])]
    #[Groups([
        VolunteerGroups::CREATE,
        VolunteerGroups::INDEX,
    ])]
    private Collection $availabilities;

    public function __construct()
    {
        $this->availabilities = new ArrayCollection();
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): Volunteer
    {
        $this->event = $event;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Volunteer
    {
        $this->user = $user;

        return $this;
    }

    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function setAvailabilities(Collection $availabilities): Volunteer
    {
        $this->availabilities = $availabilities;

        return $this;
    }

    public function addAvailability(VolunteerAvailability $availability): self
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities[] = $availability;
            $availability->setVolunteer($this);
        }

        return $this;
    }

    public function removeAvailability(VolunteerAvailability $availability): self
    {
        $this->availabilities->removeElement($availability);

        return $this;
    }
}
