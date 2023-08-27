<?php

namespace App\Util\Comparable;

use App\Component\Model\ComparableTimeframe;

interface ComparableInterface
{
    public function getValue(): int;

    public function equals(ComparableTimeframe $that): bool;

    public function greaterThan(ComparableInterface $that): bool;

    public function greaterThanOrEqual(ComparableInterface $that): bool;

    public function lesserThan(ComparableInterface $that): bool;

    public function lesserThanOrEqual(ComparableInterface $that): bool;

    public function compare(ComparableInterface $that): int;

    public function between(ComparableInterface $beginning, ComparableInterface $end): bool;
}