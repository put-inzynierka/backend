<?php

namespace App\Service\KeyGenerator\Implementation;

use App\Entity\Game\Game;
use App\Service\KeyGenerator\Contract\GeneratesKeys;
use App\Service\KeyGenerator\KeyGenerator;

class SteamKeyGenerator extends KeyGenerator implements GeneratesKeys
{
    public static function getSupportedPlatforms(): array
    {
        return ['STEAM'];
    }

    public function generate(Game $game): string
    {
        $chunkCount = rand(3, 5);
        $chunks = [];
        for ($chunk = 0; $chunk < $chunkCount; $chunk++) {
            $chunk = $this->generateRandomString(5);
        }

        return implode('-', $chunks);
    }
}