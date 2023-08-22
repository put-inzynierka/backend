<?php

namespace App\Service\File\Factory;

use App\Enum\File\FileType;
use App\Service\File\Contract\FileValidator;
use InvalidArgumentException;

class FileValidatorFactory
{
    public function __construct(
        protected iterable $validators
    ) {}
    
    public function getFileValidator(FileType $type): FileValidator
    {
        /** @var FileValidator $validator */
        foreach ($this->validators as $validator) {
            if (in_array($type, $validator->getSupportedTypes())) {
                return $validator;
            }
        }

        throw new InvalidArgumentException('Unhandled FileType provided.');
    }
}