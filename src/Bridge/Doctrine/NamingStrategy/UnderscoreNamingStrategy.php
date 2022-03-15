<?php

namespace App\Bridge\Doctrine\NamingStrategy;

use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Component\String\Inflector\InflectorInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy as BaseUnderscoreNamingStrategy;

class UnderscoreNamingStrategy extends BaseUnderscoreNamingStrategy
{
    protected const DEFAULT_PATTERN      = '/(?<=[a-z])([A-Z])/';
    protected const NUMBER_AWARE_PATTERN = '/(?<=[a-z0-9])([A-Z])/';

    protected InflectorInterface $inflector;
    protected int $case;
    protected string $pattern;

    public function __construct($case = CASE_LOWER, bool $numberAware = true)
    {
        $this->inflector = new EnglishInflector();

        $this->case    = $case;
        $this->pattern = $numberAware ? self::NUMBER_AWARE_PATTERN : self::DEFAULT_PATTERN;

        parent::__construct($case, $numberAware);
    }

    public function classToTableName($className): string
    {
        if (str_contains($className, '\\')) {
            $qualifiedClassName = explode('\\', $className);
            $className = array_pop($qualifiedClassName);
            $schemaName = array_pop($qualifiedClassName);

            if ($schemaName !== 'Entity') {
                $pluralSchemaName = $this->inflector->pluralize($schemaName)[0];
                $className = $pluralSchemaName . '.' . $className;
            }
        }

        $pluralClassName = $this->inflector->pluralize($className)[0];

        return $this->underscore($pluralClassName);
    }

    protected function underscore(string $string): string
    {
        $string = preg_replace($this->pattern, '_$1', $string);

        if ($this->case === CASE_UPPER) {
            return strtoupper($string);
        }

        return strtolower($string);
    }
}