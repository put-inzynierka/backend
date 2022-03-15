<?php

namespace App\Service\KeyGenerator\Contract;

use App\Entity\Game\Game;

interface GeneratesKeys
{
    public static function getSupportedPlatforms(): array;
    public function generate(Game $game): string;
}