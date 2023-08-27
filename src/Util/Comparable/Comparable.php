<?php

namespace App\Util\Comparable;

trait Comparable
{
    abstract public function getValue(): int;

    public function equals(ComparableInterface $that): bool
    {
        return $this->getValue() === $that->getValue();
    }

    public function greaterThan(ComparableInterface $that): bool
    {
        return $this->getValue() > $that->getValue();
    }

    public function greaterThanOrEqual(ComparableInterface $that): bool
    {
        return $this->getValue() >= $that->getValue();
    }

    public function lesserThan(ComparableInterface $that): bool
    {
        return $this->getValue() < $that->getValue();
    }

    public function lesserThanOrEqual(ComparableInterface $that): bool
    {
        return $this->getValue() <= $that->getValue();
    }

    public function compare(ComparableInterface $that): int
    {
        return $this->getValue() <=> $that->getValue();
    }

    public function between(ComparableInterface $beginning, ComparableInterface $end): bool
    {
        return $this->getValue() >= $beginning && $this->getValue() <= $end;
    }
}
