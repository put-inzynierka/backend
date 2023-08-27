<?php

namespace App\Component\Attribute\Param;

use Attribute;
use FOS\RestBundle\Controller\Annotations\ParamInterface;
use Symfony\Component\HttpFoundation\Request;

#[Attribute(Attribute::TARGET_METHOD)]
class QueryParam extends Param implements ParamInterface
{
    public function __construct(string $name)
    {
        parent::__construct(
            $name,
            'query',
            sprintf('Defines the %s.', $name),
            default: null
        );
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function getValue(Request $request, $default): ?string
    {
        return $request->query->get($this->name, $default);
    }
}
