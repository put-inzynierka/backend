<?php

namespace App\Voter;

use App\Component\Util\AbstractDictionary;

class Qualifier extends AbstractDictionary
{
    public const IS_AUTHENTICATED = 'is-authenticated';
    public const IS_OWNER = 'is-owner';
    public const HAS_ACCESS = 'has-access';
}