<?php

namespace App\Entity;

use App\Enum\SerializationGroup\Event\EventGroups;
use App\Enum\SerializationGroup\Event\VolunteerGroups;
use App\Enum\SerializationGroup\Project\ReservationGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table]
class Timeframe extends AbstractEntity
{
    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    #[Constraints\Time]
    #[Constraints\NotBlank(allowNull: false, groups: [EventGroups::CREATE])]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::INDEX,
        EventGroups::UPDATE,
        VolunteerGroups::CREATE,
        VolunteerGroups::INDEX,
        ReservationGroups::CREATE,
        ReservationGroups::UPDATE,
        ReservationGroups::INDEX,
    ])]
    #[Property(
        description: 'Time of the beginning',
        example: '12:00',
    )]
    private DateTimeImmutable $hourFrom;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    #[Constraints\Time]
    #[Constraints\NotBlank(allowNull: false, groups: [EventGroups::CREATE])]
    #[Groups([
        EventGroups::CREATE,
        EventGroups::SHOW,
        EventGroups::INDEX,
        EventGroups::UPDATE,
        VolunteerGroups::CREATE,
        VolunteerGroups::INDEX,
        ReservationGroups::CREATE,
        ReservationGroups::UPDATE,
        ReservationGroups::INDEX,
    ])]
    #[Property(
        description: 'Time of the end',
        example: '14:30',
    )]
    private DateTimeImmutable $hourTo;

    public function getHourFrom(): DateTimeImmutable
    {
        return $this->hourFrom;
    }

    public function setHourFrom(DateTimeImmutable $hourFrom): Timeframe
    {
        $this->hourFrom = $hourFrom;

        return $this;
    }

    public function getHourTo(): DateTimeImmutable
    {
        return $this->hourTo;
    }

    public function setHourTo(DateTimeImmutable $hourTo): Timeframe
    {
        $this->hourTo = $hourTo;

        return $this;
    }
}
