<?php

namespace App\Service\File\Factory;

use App\Enum\File\FileType;
use App\Service\File\Contract\FileProcessor;
use App\Service\File\Contract\FileValidator;
use InvalidArgumentException;

class FileProcessorFactory
{
    public function __construct(
        protected iterable $processors
    ) {}
    
    public function getFileProcessor(FileType $type): FileProcessor
    {
        /** @var FileProcessor $processor */
        foreach ($this->processors as $processor) {
            if (in_array($type, $processor->getSupportedTypes())) {
                return $processor;
            }
        }

        throw new InvalidArgumentException('Unhandled FileType provided.');
    }
}