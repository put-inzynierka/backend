<?php

namespace App\Entity\Component\Contract;

use DateTimeImmutable;

interface Timestampable
{
    public function getCreatedAt(): DateTimeImmutable;
    public function getModifiedAt(): DateTimeImmutable;
}
