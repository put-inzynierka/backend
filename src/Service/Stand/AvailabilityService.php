<?php

namespace App\Service\Stand;

use App\Component\Model\AvailableStand\Day as DayModel;
use App\Component\Model\AvailableStand\Timeframe as TimeframeModel;
use App\Component\Model\AvailableStand\Stand as StandModel;
use App\Component\Model\ComparableTimeframe;
use App\Entity\Event\Event;
use App\Entity\Location\Location;
use App\Entity\Project\Reservation;
use App\Entity\Timeframe;
use App\Repository\ReservationRepository;
use App\Util\Time;
use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

class AvailabilityService
{
    public function __construct(
        protected CacheInterface $appCache,
        protected ReservationRepository $reservationRepository
    ) {}

    public function rebuild(Event $event): void
    {
        $this->buildReservationAutocomplete($event, true);
    }

    public function buildReservationAutocomplete(Event $event, bool $force = false): array
    {
        /** @var CacheItemInterface $cachedStands */
        $cachedStands = $this->appCache->getItem(sprintf('available_stands_%d', $event->getId()));
        if (!$force && $cachedStands->isHit()) {
            return $cachedStands->get();
        }

        $result = [];
        $stands = [];

        /** @var Location $location */
        foreach ($event->getLocations() as $location) {
            $stands = [...$stands, ...$location->getStands()];
        }

        foreach ($event->getDays() as $day) {
            $dayModel = DayModel::fromEntity($day);
            $timeframes = array_map(
                fn (Timeframe $timeframe) => new ComparableTimeframe(
                    Time::createFromDateTime($timeframe->getHourFrom()),
                    Time::createFromDateTime($timeframe->getHourTo())
                ),
                $day->getTimeframes()->toArray()
            );
            $standsTimeframes = [];

            foreach ($stands as $stand) {
                $standTimeframes = $timeframes;

                $reservationsToSubtract = array_map(
                    fn (Reservation $reservation) => new ComparableTimeframe(
                        Time::createFromDateTime($reservation->getTimeframe()->getHourFrom()),
                        Time::createFromDateTime($reservation->getTimeframe()->getHourTo())
                    ),
                    $this->reservationRepository->findConfirmedByStandAndDay($stand, $day)
                );
                $this->subtractReservations($standTimeframes, $reservationsToSubtract);

                $standsTimeframes[] = [
                    StandModel::fromEntity($stand),
                    array_map(
                        fn (ComparableTimeframe $timeframe) => TimeframeModel::fromComparableTimeframe($timeframe),
                        $standTimeframes
                    )
                ];
            }

            $timeframes = [];
            /**
             * @var StandModel $stand
             * @var TimeframeModel[] $rawTimeframes
             */
            foreach ($standsTimeframes as [$stand, $rawTimeframes]) {
                foreach ($rawTimeframes as $timeframe) {
                    $timeframe->setStand($stand);
                    $timeframes[] = $timeframe;
                }
            }

            $dayModel->setTimeframes($timeframes);
            $result[] = $dayModel;
        }

        $cachedStands->set($result);
        $this->appCache->save($cachedStands);

        return $result;
    }

    protected function subtractReservations(array &$standTimeframes, array &$reservationsToSubtract): void
    {
        /** @var ComparableTimeframe $reservationTimeframe */
        foreach ($reservationsToSubtract as $reservationTimeframe) {
            $started = false;
            $ended = false;

            /** @var ComparableTimeframe $standTimeframe */
            foreach ($standTimeframes as $index => $standTimeframe) {
                $startsInTimeframe = $reservationTimeframe->getHourFrom()->between(
                    $standTimeframe->getHourFrom(),
                    $standTimeframe->getHourTo()
                );
                $endsInTimeframe = $reservationTimeframe->getHourTo()->between(
                    $standTimeframe->getHourFrom(),
                    $standTimeframe->getHourTo()
                );

                if ($startsInTimeframe) {
                    if ($endsInTimeframe) {
                        $standTimeframes[] = new ComparableTimeframe(
                            $standTimeframe->getHourFrom(),
                            $reservationTimeframe->getHourFrom()
                        );
                        $standTimeframes[] = new ComparableTimeframe(
                            $reservationTimeframe->getHourTo(),
                            $standTimeframe->getHourTo()
                        );
                        unset($standTimeframes[$index]);
                        $standTimeframes = array_values($standTimeframes);

                        continue 2;
                    }

                    $standTimeframes[] = new ComparableTimeframe(
                        $standTimeframe->getHourFrom(),
                        $reservationTimeframe->getHourFrom()
                    );
                    unset($standTimeframes[$index]);
                    $standTimeframes = array_values($standTimeframes);
                    $started = true;

                    if ($ended) {
                        continue 2;
                    }

                    continue;
                }

                if ($endsInTimeframe) {
                    $standTimeframes[] = new ComparableTimeframe(
                        $reservationTimeframe->getHourTo(),
                        $standTimeframe->getHourTo()
                    );
                    unset($standTimeframes[$index]);
                    $standTimeframes = array_values($standTimeframes);
                    $ended = true;

                    if ($started) {
                        continue 2;
                    }
                }
            }
        }

        /** @var ComparableTimeframe $timeframe */
        foreach ($standTimeframes as $index => $timeframe) {
            if ($timeframe->getHourTo()->equals($timeframe->getHourFrom())) {
                unset($standTimeframes[$index]);
            }
        }
        $standTimeframes = array_values($standTimeframes);
    }
}
