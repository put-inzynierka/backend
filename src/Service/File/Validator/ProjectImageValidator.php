<?php

namespace App\Service\File\Validator;

use App\Bridge\Symfony\HttpFoundation\RawFile;
use App\Entity\User\User;
use App\Enum\File\ByteSize;
use App\Enum\File\FileType;
use App\Service\File\Contract\FileValidator;
use App\Service\File\Validator\Trait\ValidatesType;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ProjectImageValidator extends AbstractFileValidator implements FileValidator
{
    use ValidatesType;

    public function getSupportedTypes(): array
    {
        return [FileType::PROJECT_IMAGE];
    }

    public function validate(RawFile $file, ?User $actor): ConstraintViolationListInterface
    {
        $violations = new ConstraintViolationList();

        if (!$actor) {
            $violations->add($this->createViolation(
                'Insufficient permissions to upload a project image.'
            ));
        }

        $this->validateImage($file, $violations);

        if ($file->getSize() > ByteSize::HUNDRED_MB) {
            $violations->add($this->createViolation(
                'The file must not be larger than 100 MB.',
                'file'
            ));
        }

        return $violations;
    }
}