<?php

namespace App\Tests;

use PHPUnit\Framework\Constraint\Constraint;

final class ResponseStructureMatch extends Constraint
{
    private array $structure;

    public function __construct(array $structure)
    {
        $this->structure = $structure;
    }

    public function toString(): string
    {
        return 'content matches ' . var_export($this->structure, true);
    }

    protected function matches($response): bool
    {
        $content = json_decode($response->getContent(), true);

        return $this->checkArrayStructure($content, $this->structure);
    }

    protected function failureDescription($response): string
    {
        return 'the Response ' . $this->toString();
    }

    protected function additionalFailureDescription($response): string
    {
        return $response->getContent();
    }

    private function checkArrayStructure(array $array, array $structure): bool
    {
        foreach ($structure as $key => $value) {
            if (gettype($value) === 'string' && !array_key_exists($value, $array)) {
                return false;
            }

            if (gettype($value) === 'array' && !$this->checkArrayStructure($array[$key], $value)) {
                return false;
            }
        }

        return true;
    }
}
