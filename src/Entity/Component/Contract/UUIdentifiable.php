<?php

namespace App\Entity\Component\Contract;

use Symfony\Component\Uid\Uuid;

interface UUIdentifiable
{
    public function getUuid(): Uuid;
}
