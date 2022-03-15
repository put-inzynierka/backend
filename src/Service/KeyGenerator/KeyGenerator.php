<?php

namespace App\Service\KeyGenerator;

abstract class KeyGenerator
{
    protected function generateRandomString(int $length): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lastCharacterIndex = strlen($characters) - 1;

        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[rand(0, $lastCharacterIndex)];
        }

        return $result;
    }
}