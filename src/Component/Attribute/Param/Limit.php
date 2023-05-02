<?php

namespace App\Component\Attribute\Param;

use Attribute;
use FOS\RestBundle\Controller\Annotations\ParamInterface;
use FOS\RestBundle\Validator\Constraints\Regex;
use Symfony\Component\HttpFoundation\Request;

#[Attribute(Attribute::TARGET_METHOD)]
class Limit extends Param implements ParamInterface
{
    public function __construct()
    {
        parent::__construct(
            name: 'limit',
            in: 'query',
            description: 'Defines list\'s on-page limit',
            default: 10
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
