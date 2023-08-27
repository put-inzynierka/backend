<?php

namespace App\Component\Model\AvailableStand;

use App\Component\Model\ComparableTimeframe;
use App\Entity\Timeframe as TimeframeEntity;
use App\Enum\SerializationGroup\BaseGroups;
use Symfony\Component\Serializer\Annotation\Groups;

class Timeframe
{
    public function __construct(
        #[Groups([BaseGroups::DEFAULT])]
        protected string $hourFrom,

        #[Groups([BaseGroups::DEFAULT])]
        protected string $hourTo,

        #[Groups([BaseGroups::DEFAULT])]
        protected ?Stand $stand
    ) {}

    public static function fromEntity(TimeframeEntity $entity): Timeframe
    {
        return new self(
            $entity->getHourFrom()->format('H:i'),
            $entity->getHourTo()->format('H:i'),
            null
        );
    }

    public static function fromComparableTimeframe(ComparableTimeframe $timeframe): Timeframe
    {
        return new self(
            $timeframe->getHourFrom()->format('H:i'),
            $timeframe->getHourTo()->format('H:i'),
            null
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHourFrom(): string
    {
        return $this->hourFrom;
    }

    public function getHourTo(): string
    {
        return $this->hourTo;
    }

    public function getStand(): ?Stand
    {
        return $this->stand;
    }

    public function setStand(Stand $stand): Timeframe
    {
        $this->stand = $stand;

        return $this;
    }
}