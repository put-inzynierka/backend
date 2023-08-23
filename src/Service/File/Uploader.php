<?php

namespace App\Service\File;

use App\Bridge\Symfony\HttpFoundation\RawFile;
use App\Entity\File\File;
use App\Entity\User\User;
use App\Enum\File\FileExtension;
use App\Enum\File\FileType;
use App\Enum\File\MimeType;
use App\Exception\UnprocessableEntityHttpException;
use App\Service\File\Factory\FileProcessorFactory;
use App\Service\File\Factory\FileValidatorFactory;
use Doctrine\ORM\EntityManagerInterface;

class Uploader
{
    public function __construct(
        protected FileValidatorFactory $validatorFactory,
        protected FileProcessorFactory $fileProcessorFactory,
        protected EntityManagerInterface $entityManager,
        protected string $uploadsDirectory,
        protected string $backendUrl
    ) {}

    public function upload(RawFile $rawFile, FileType $type, ?User $actor): File
    {
        $this->validate($rawFile, $type, $actor);
        $this->process($rawFile, $type, $actor);

        $file = new File(
            $rawFile->getClientFilename(),
            FileExtension::from($rawFile->getClientExtension()),
            MimeType::from($rawFile->getClientMimeType()),
            $type,
            $actor
        );

        $rawFile->move($this->uploadsDirectory, $file->getUuid()->jsonSerialize());

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }

    protected function validate(RawFile $file, FileType $type, ?User $actor): void
    {
        $validator = $this->validatorFactory->getFileValidator($type);

        $violations = $validator->validate($file, $actor);
        if ($violations->count() > 0) {
            throw new UnprocessableEntityHttpException($violations);
        }
    }

    protected function process(RawFile $file, FileType $type, ?User $actor): void
    {
        $processor = $this->fileProcessorFactory->getFileProcessor($type);

        $processor->process($file, $actor);
    }
}