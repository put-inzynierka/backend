<?php

namespace App\Component\Attribute;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use FOS\RestBundle\Controller\Annotations\ParamInterface;
use OpenApi\Attributes as OA;

abstract class BodyParam extends OA\RequestBody implements ParamInterface
{
    public function __construct(
        protected string $name,
        string $description,
        OA\Attachable $schema,
    ) {
        parent::__construct(
            description: $description,
            attachables: [$schema],
        );
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function getDefault(): mixed
    {
        return null;
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

    public function getValue(Request $request, $default): string
    {
        return $request->getContent() !== '' ? $request->getContent() : '{}';
    }
}
