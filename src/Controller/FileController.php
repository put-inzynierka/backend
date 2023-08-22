<?php

namespace App\Controller;

use App\Bridge\Symfony\HttpFoundation\RawFile;
use App\Component\Attribute\Response as Resp;
use App\Component\Model\File;
use App\Enum\File\FileType;
use App\Service\File\Uploader;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Tag;
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
}
