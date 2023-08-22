<?php

namespace App\Service\File\Validator\Trait;

use App\Bridge\Symfony\HttpFoundation\RawFile;
use App\Enum\File\FileExtension;
use App\Enum\File\MimeType;
use Symfony\Component\Validator\ConstraintViolationList;

trait ValidatesType
{
    protected function validateImage(RawFile $file, ConstraintViolationList $violations): void
    {
        $extension = FileExtension::tryFrom($file->getClientExtension());
        $mimeType = MimeType::tryFrom($file->getClientMimeType());

        if (!$mimeType || !$mimeType->isImage()) {
            $violations->add($this->createViolation(
                'The file needs to be an image.',
                'file'
            ));
        }

        if (!$extension || $extension->mimeType() !== $mimeType) {
            $violations->add($this->createViolation(
                'The extension needs to match the mime type.',
                'file'
            ));
        }
    }
}