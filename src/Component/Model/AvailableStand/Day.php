<?php

namespace App\Component\Model\AvailableStand;

use App\Entity\Event\Day as DayEntity;
use App\Enum\SerializationGroup\BaseGroups;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Component\Model\AvailableStand\Timeframe as TimeframeModel;

class Day
{
    public function __construct(
        #[Groups([BaseGroups::DEFAULT])]
        protected int $id,

        #[Groups([BaseGroups::DEFAULT])]
        protected string $date,

        /** @var TimeframeModel[] $timeframes */
        #[Groups([BaseGroups::DEFAULT])]
        protected array $timeframes
    ) {}

    public static function fromEntity(DayEntity $entity): Day
    {
        return new self(
            $entity->getId(),
            $entity->getDate()->format('Y-m-d'),
            []
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getTimeframes(): array
    {
        return $this->timeframes;
    }

    public function setTimeframes(array $timeframes): Day
    {
        $this->timeframes = $timeframes;

        return $this;
    }
}