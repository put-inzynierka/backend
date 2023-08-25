<?php

namespace App\Component\Model;

use App\Util\Time;

class ComparableTimeframe
{
    public function __construct(
        protected Time $hourFrom,
        protected Time $hourTo
    ) {}

    public function getHourFrom(): Time
    {
        return $this->hourFrom;
    }

    public function getHourTo(): Time
    {
        return $this->hourTo;
    }
}