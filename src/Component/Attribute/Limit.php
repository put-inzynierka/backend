<?php

namespace App\Component\Attribute;

use Attribute;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Validator\Constraints\Regex;
use FOS\RestBundle\Controller\Annotations\ParamInterface;

#[Attribute(Attribute::TARGET_METHOD)]
class Limit extends Param implements ParamInterface
{
    public function __construct()
    {
        parent::__construct(
            'limit',
            'query',
            'Defines list\'s on-page limit',
            false,
            false,
            10
        );
    }

    public function getConstraints(): array
    {
        $pattern = '/^\d+$/';

        return [new Regex($pattern)];
    }

    public function getValue(Request $request, $default): int
    {
        return (int) $request->query->get($this->name, $default);
    }
}