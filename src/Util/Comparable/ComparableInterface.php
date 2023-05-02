<?php

namespace App\Util\Comparable;

interface ComparableInterface
{
    public function getValue(): int;
    public function greaterThan(ComparableInterface $that): bool;
    public function greaterThanOrEqual(ComparableInterface $that): bool;
    public function lesserThan(ComparableInterface $that): bool;
    public function lesserThanOrEqual(ComparableInterface $that): bool;
    public function compare(ComparableInterface $that): int;
}
