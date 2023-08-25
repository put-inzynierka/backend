<?php

namespace App\Service\Validation;

use App\Entity\Location\Location;
use App\Entity\Location\Stand;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class StandValidator extends Validator
{
    /**
     * @param Stand[] $needles
     * @param Location[] $haystack
     * @return ConstraintViolationListInterface
     */
    public function validate(iterable $needles, iterable $haystack): ConstraintViolationListInterface
    {
        $violations = new ConstraintViolationList();
        foreach ($needles as $needle) {
            if ($this->checkIfIsContained($needle, $haystack)) {
                continue;
            }

            $violations->add($this->createViolation(
                'The stand need to be contained within possible locations.',
                'stand',
                $needle->getId()
            ));
        }

        return $violations;
    }

    protected function checkIfIsContained(Stand $stand, iterable $locations): bool
    {
        foreach ($locations as $location) {
            if ($stand->getLocation()->getId() === $location->getId()) {
                return true;
            }
        }

        return false;
    }
}