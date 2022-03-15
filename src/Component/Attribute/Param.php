<?php

namespace App\Component\Attribute;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use FOS\RestBundle\Controller\Annotations\ParamInterface;

abstract class Param implements ParamInterface
{
    public function __construct(
        protected string $name,
        protected string $in,
        protected string $description,
        protected bool $allowEmptyValue,
        protected bool $required,
        protected $default
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getIncompatibilities(): array
    {
        return [];
    }

    public function getConstraints(): array
    {
        $constraints = [];

        if ($this->required) {
            $constraints[] = new NotNull();
        }

        if (!$this->allowEmptyValue) {
            $constraints[] = new NotBlank(allowNull: true);
        }

        return $constraints;
    }

    public function isStrict(): bool
    {
        return false;
    }

    public function getValue(Request $request, $default): mixed
    {
        return $request->query->get($this->name, $default);
    }
}