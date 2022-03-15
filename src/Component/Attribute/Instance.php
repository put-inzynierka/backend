<?php

namespace App\Component\Attribute;

use Attribute;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\ParamInterface;

#[Attribute(Attribute::TARGET_METHOD)]
class Instance extends Param implements ParamInterface
{
    public function __construct()
    {
        parent::__construct(
            'instance',
            'body',
            'Describes object present in body',
            false,
            true,
            '{}'
        );
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function getValue(Request $request, $default): string
    {
        return $request->getContent() !== '' ? $request->getContent() : $this->default;
    }
}