<?php

namespace App\Component\Attribute\Param;

use FOS\RestBundle\Controller\Annotations\ParamInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

abstract class Param extends OA\Parameter implements ParamInterface
{
    public function __construct(
        string $name,
        string $in,
        string $description,
        bool $required = false,
        bool $allowEmptyValue = false,
        protected $default = null
    ) {
        parent::__construct(
            name: $name,
            description: $description,
            in: $in,
            required: $required,
            allowEmptyValue: $allowEmptyValue,
        );
    }
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
