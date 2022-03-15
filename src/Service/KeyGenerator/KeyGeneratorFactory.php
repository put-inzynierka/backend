<?php

namespace App\Service\KeyGenerator;

use App\Entity\Game\Platform;
use App\Service\KeyGenerator\Contract\GeneratesKeys;

class KeyGeneratorFactory
{
    public function createGenerator(Platform $platform): GeneratesKeys
    {
        // todo
    }
}