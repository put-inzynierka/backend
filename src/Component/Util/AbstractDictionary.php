<?php

namespace App\Component\Util;

use App\Component\Util\Interface\Dictionary;
use App\Component\Util\Trait\Dictionary as DictionaryTrait;

abstract class AbstractDictionary implements Dictionary
{
    use DictionaryTrait;
}