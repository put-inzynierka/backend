<?php

namespace App\Service\Validation;

use App\Component\Model\ComparableTimeframe;
use App\Entity\Component\Contract\Timeframeable;
use App\Util\Time;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class TimeframeValidator extends Validator
{
    public function __construct(
        protected array $possibleDays
    ) {}

    /**
     * @param Timeframeable[] $needles
     * @param Timeframeable[] $haystack
     * @return ConstraintViolationListInterface
     */
    public function validate(iterable $needles, iterable $haystack): ConstraintViolationListInterface
    {
        $this->preparePossibleDays($haystack);

        $violations = new ConstraintViolationList();
        foreach ($needles as $needle) {
            if ($this->checkIfIsContained($needle)) {
                continue;
            }

            $violations->add($this->createViolation(
                'The timeframes need to be contained within possible timeframes.',
                'day',
                $needle->getDate()->format('D-m-y')
            ));
        }

        return $violations;
    }

    protected function preparePossibleDays(array $haystack): void
    {
        $possibleDays = [];

        foreach ($haystack as $day) {
            $formattedDate = $day->getDate()->format('Y-m-d');

            if (!array_key_exists($formattedDate, $possibleDays)) {
                $possibleDays[$formattedDate] = [];
            }

            foreach ($day->getTimeframes() as $timeframe) {
                $comparableTimeframe = new ComparableTimeframe(
                    Time::createFromDateTime($timeframe->getHourFrom()),
                    Time::createFromDateTime($timeframe->getHourTo())
                );

                foreach ($possibleDays[$formattedDate] as &$possibleTimeframe) {
                    $mergedTimeframe = $this->attemptToMerge($comparableTimeframe, $possibleTimeframe);

                    if ($mergedTimeframe) {
                        $possibleTimeframe = $mergedTimeframe;
                        continue 2;
                    }
                }

                $possibleDays[$formattedDate][] = $comparableTimeframe;
            }
        }

        $this->possibleDays = $possibleDays;
    }

    protected function checkIfIsContained(Timeframeable $day): bool
    {
        $formattedDate = $day->getDate()->format('Y-m-d');

        if (!array_key_exists($formattedDate, $this->possibleDays)) {
            // That day is straight up missing in our possible
            // days, so it fails the check.
            return false;
        }

        foreach ($day->getTimeframes() as $timeframe) {
            $comparableTimeframe = new ComparableTimeframe(
                Time::createFromDateTime($timeframe->getHourFrom()),
                Time::createFromDateTime($timeframe->getHourTo())
            );

            /** @var ComparableTimeframe $possibleTimeframe */
            foreach ($this->possibleDays[$formattedDate] as $possibleTimeframe) {
                if (
                    $comparableTimeframe->getHourFrom()->between(
                        $possibleTimeframe->getHourFrom(),
                        $possibleTimeframe->getHourTo()
                    ) &&
                    $comparableTimeframe->getHourTo()->between(
                        $possibleTimeframe->getHourFrom(),
                        $possibleTimeframe->getHourTo()
                    )
                ) {
                    // That timeframe is contained within a possible timeframe, so
                    // we're going on to the next timeframe to check within that day.
                    continue 2;
                }
            }

            // That timeframe is not contained within any of the possible
            // timeframes, so it fails the check.
            return false;
        }

        // No timeframes have failed the check, all's good.
        return true;
    }

    protected function attemptToMerge(ComparableTimeframe $first, ComparableTimeframe $second): ?ComparableTimeframe
    {
        if ($first->getHourFrom()->between($second->getHourFrom(), $second->getHourTo())) {
            if ($first->getHourTo()->between($second->getHourFrom(), $second->getHourTo())) {
                // If the first timeframe contains entirely in the second timeframe, return second timeframe
                return $second;
            }

            // If the first timeframe begins within the second timeframe, but
            // doesn't end within it, make a timeframe starting with the beginning
            // of the second timeframe and ending with the end of the first.
            return new ComparableTimeframe($second->getHourFrom(), $first->getHourTo());
        }

        if ($first->getHourTo()->between($second->getHourFrom(), $second->getHourTo())) {
            // If the first timeframe ends within the second timeframe, but
            // doesn't begin within it, make a timeframe starting with the beginning
            // of the first timeframe and ending with the end of the second.
            return new ComparableTimeframe($first->getHourFrom(), $second->getHourTo());
        }

        // If the first timeframe neither starts nor ends within the second
        // timeframe, the two are not mergeable.
        return null;
    }
}