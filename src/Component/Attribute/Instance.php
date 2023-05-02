<?php

namespace App\Component\Attribute;

use Attribute;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\ParamInterface;

#[Attribute(Attribute::TARGET_METHOD)]
class Instance extends BodyParam implements ParamInterface
{
    public function __construct(string $class, string $group)
    {
        parent::__construct(
            $class,
            'Describes object present in body',
            new Model(
                type: $class,
                groups: [$group]
            )
        );
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function getValue(Request $request, $default): string
    {
        return $request->getContent() !== '' ? $request->getContent() : '';
    }
}
