<?php

namespace App\Component\Attribute\Param;

use Attribute;
use FOS\RestBundle\Controller\Annotations\ParamInterface;
use FOS\RestBundle\Validator\Constraints\Regex;
use Symfony\Component\HttpFoundation\Request;

#[Attribute(Attribute::TARGET_METHOD)]
class Page extends Param implements ParamInterface
{
    public function __construct()
    {
        parent::__construct(
            'page',
            'query',
            'Defines list\'s page index',
            default: 1
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
