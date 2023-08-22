<?php

namespace App\Controller;

use App\Bridge\Symfony\HttpFoundation\RawFile;
use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Component\Model\File as FileModel;
use App\Entity\File\File;
use App\Enum\File\FileType;
use App\Enum\SerializationGroup\Movie\GenreGroups;
use App\Service\File\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends AbstractController
{
    #[Rest\Post(path: '/files/{type}', name: 'store_file')]
    #[Tag('File')]
    #[OA\RequestBody(
        new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                new OA\Property(
                    property: 'file',
                    description: 'File to upload',
                    type: 'string',
                    format: 'binary',
                )
            )
        )
    )]
    #[Resp\ObjectResponse(
        description: 'Creates a new file',
        class: FileModel::class,
        status: 201,
    )]
    public function store(
        Uploader $uploader,
        Request $request,
        string $type
    ): Response {
        $file = $uploader->upload(
            RawFile::fromBinaryRequest($request),
            FileType::from($type),
            $this->getUser()
        );

        return $this->object($file, 201);
    }

    #[Rest\Get(
        path: '/files/{uuid}',
        name: 'show_file'
    )]
    #[Tag('File')]
    #[Param\Path('uuid', description: 'The UUID of the file')]
    public function show(
        EntityManagerInterface $entityManager,
        string $uuid,
        string $uploadsDirectory
    ): Response {
        $file = $entityManager->getRepository(File::class)->findBy(['uuid' => $uuid]);
        $path = sprintf('%s/%s', $uploadsDirectory, $file->getUuid()->jsonSerialize());

        return $this->binary(
            $path,
            $file->getMimeType()->value,
            $file->getFilename()
        );
    }
}
