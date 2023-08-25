<?php

namespace App\Entity\Component\Contract;

use DateTimeInterface;
use Doctrine\Common\Collections\Collection;

interface Timeframeable
{
    public function getDate(): DateTimeInterface;
    public function getTimeframes(): Collection;
}