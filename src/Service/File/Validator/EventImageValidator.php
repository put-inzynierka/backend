<?php

namespace App\Service\File\Validator;

use App\Bridge\Symfony\HttpFoundation\RawFile;
use App\Entity\User\User;
use App\Enum\File\ByteSize;
use App\Enum\File\FileType;
use App\Enum\UserRole;
use App\Service\File\Contract\FileValidator;
use App\Service\File\Validator\Trait\ValidatesType;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class EventImageValidator extends AbstractFileValidator implements FileValidator
{
    use ValidatesType;

    public function getSupportedTypes(): array
    {
        return [FileType::EVENT_IMAGE];
    }

    public function validate(RawFile $file, ?User $actor): ConstraintViolationListInterface
    {
        $violations = new ConstraintViolationList();

        if ($actor->getRole() !== UserRole::ADMIN) {
            $violations->add($this::createViolation(
                'Insufficient permissions to upload an event image.',
                'type',
                FileType::EVENT_IMAGE->value
            ));
        }

        $this->validateImage($file, $violations);

        if ($file->getSize() > ByteSize::HUNDRED_MB) {
            $violations->add($this::createViolation(
                'The file must not be larger than 100 MB.',
                'file'
            ));
        }

        return $violations;
    }
}