<?php

namespace App\Service\KeyGenerator\Implementation;

use App\Entity\Game\Game;
use App\Service\KeyGenerator\Contract\GeneratesKeys;
use App\Service\KeyGenerator\KeyGenerator;

class UplayKeyGenerator extends KeyGenerator implements GeneratesKeys
{
    public static function getSupportedPlatforms(): array
    {
        return ['UPLAY'];
    }

    public function generate(Game $game): string
    {
        $chunks = [];
        for ($chunk = 0; $chunk < 4; $chunk++) {
            $chunk = $this->generateRandomString(4);
        }

        return implode('-', $chunks);
    }
}