<?php

namespace App\Controller;

use App\Bridge\Symfony\HttpFoundation\RawFile;
use App\Component\Attribute\Param as Param;
use App\Component\Attribute\Response as Resp;
use App\Entity\File\File;
use App\Enum\File\FileType;
use App\Service\File\Uploader;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends AbstractController
{
    #[Rest\Post(path: '/files/{type}', name: 'store_file')]
    #[Tag('File')]
    #[Param\Path(
        'type',
        'Type of uploaded file. One of: event-image, project-image',
        '/^(event-image|project-image)$/')
    ]
    #[Resp\ObjectResponse(
        description: 'Creates a new file',
        class: File::class,
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
        File $file,
        string $uploadsDirectory
    ): Response {
        $path = sprintf('%s/%s', $uploadsDirectory, $file->getUuid()->jsonSerialize());

        return $this->binary(
            $path,
            $file->getMimeType()->value,
            $file->getFilename()
        );
    }
}
