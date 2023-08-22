<?php

namespace App\Service\File;

use App\Entity\File\File;
use App\Entity\User\User;
use App\Enum\File\FileExtension;
use App\Enum\File\FileType;
use App\Enum\File\MimeType;
use App\Exception\UnprocessableEntityHttpException;
use App\Service\File\Factory\FileProcessorFactory;
use App\Service\File\Factory\FileValidatorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Component\Model\File as FileModel;

class Uploader
{
    public function __construct(
        protected FileValidatorFactory $validatorFactory,
        protected FileProcessorFactory $fileProcessorFactory,
        protected EntityManagerInterface $entityManager,
        protected string $uploadsDirectory,
        protected string $backendUrl
    ) {}

    public function upload(UploadedFile $uploadedFile, FileType $type, ?User $actor): FileModel
    {
        $this->validate($uploadedFile, $type, $actor);
        $this->process($uploadedFile, $type, $actor);

        $file = new File(
            pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
            FileExtension::from($uploadedFile->getClientOriginalExtension()),
            MimeType::from($uploadedFile->getClientMimeType()),
            $type,
            $actor
        );

        $uploadedFile->move($this->uploadsDirectory, $file->getUuid()->jsonSerialize());

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $this->prepareModel($file);
    }

    public function prepareModel(File $file): FileModel
    {
        $uuid = $file->getUuid()->jsonSerialize();

        return new FileModel(
            $uuid,
            sprintf('%s/files/%s', $this->backendUrl, $uuid)
        );
    }

    protected function validate(UploadedFile $file, FileType $type, ?User $actor): void
    {
        $validator = $this->validatorFactory->getFileValidator($type);

        $violations = $validator->validate($file, $actor);
        if ($violations->count() > 0) {
            throw new UnprocessableEntityHttpException($violations);
        }
    }

    protected function process(UploadedFile $file, FileType $type, ?User $actor): void
    {
        $processor = $this->fileProcessorFactory->getFileProcessor($type);

        $processor->process($file, $actor);
    }
}