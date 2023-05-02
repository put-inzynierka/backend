<?php

namespace App\Component\Attribute\Param;

use Attribute;
use FOS\RestBundle\Controller\Annotations\ParamInterface;
use FOS\RestBundle\Validator\Constraints\Regex;
use Symfony\Component\HttpFoundation\Request;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Path extends Param implements ParamInterface
{
    public function __construct(string $name, string $description, protected string $pattern = '/^\d+$/')
    {
        parent::__construct(
            $name,
            'path',
            $description,
            required: true
        );
    }

    public function getConstraints(): array
    {
        return [new Regex($this->pattern)];
    }

    public function getValue(Request $request, $default): int
    {
        return (int) $request->query->get($this->name, $default);
    }
}
