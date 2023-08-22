<?php

namespace App\Bridge\Symfony\HttpFoundation;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

class RawFile extends File
{
    protected $handle;

    public function __construct(
        string $contents,
        protected string $clientMimeType,
        protected string $clientFilename,
        protected string $clientExtension
    ) {
        $this->handle = tmpfile();
        fwrite($this->handle, $contents);

        parent::__construct(stream_get_meta_data($this->handle)['uri']);
    }

    public static function fromBinaryRequest(Request $request): self
    {
        $mimeTypeHeader = $request->headers->get('Content-Type');
        if (!$mimeTypeHeader) {
            throw new \InvalidArgumentException('Content-Type header is not set.');
        }

        $contentDispositionHeader = $request->headers->get('Content-Disposition');
        if (!$contentDispositionHeader) {
            throw new \InvalidArgumentException('Content-Disposition header is not set.');
        }

        $contentDisposition = explode(';', $contentDispositionHeader)[0];
        if ($contentDisposition !== 'attachment') {
            throw new \InvalidArgumentException('Content-Disposition needs to be "attachment".');
        }

        $mimeType = explode(';', $mimeTypeHeader)[0];

        $filename = explode('filename="', $contentDispositionHeader)[1];
        $filename = substr($filename, 0, -1);
        $filename = basename($filename);

        $extension = explode('.', $filename);
        array_shift($extension);
        $extension = implode('.', $extension);

        return new self(
            $request->getContent(),
            $mimeType,
            $filename,
            $extension
        );
    }

    public function getClientMimeType(): string
    {
        return $this->clientMimeType;
    }

    public function getClientFilename(): string
    {
        return $this->clientFilename;
    }

    public function getClientExtension(): string
    {
        return $this->clientExtension;
    }
}