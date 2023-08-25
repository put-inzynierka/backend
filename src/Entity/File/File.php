<?php

namespace App\Entity\File;

use App\Entity\Component\Contract\Timestampable;
use App\Entity\Component\Contract\UUIdentifiable;
use App\Entity\Component\Trait\Timestampable as TimestampableTrait;
use App\Entity\Component\Trait\UUIdentifiable as UUIdentifiableTrait;
use App\Entity\User\User;
use App\Enum\File\FileExtension;
use App\Enum\File\FileType;
use App\Enum\File\MimeType;
use App\Enum\SerializationGroup\BaseGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Deprecated;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table]
#[ORM\HasLifecycleCallbacks]
class File implements UUIdentifiable, Timestampable
{
    use UUIdentifiableTrait;
    use TimestampableTrait;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $filename;

    #[ORM\Column(type: Types::STRING, enumType: FileExtension::class)]
    private FileExtension $extension;

    #[ORM\Column(type: Types::STRING, enumType: MimeType::class)]
    private MimeType $mimeType;

    #[ORM\Column(type: Types::STRING, enumType: FileType::class)]
    private FileType $type;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn]
    private ?User $uploadedBy;

    #[Groups([BaseGroups::DEFAULT])]
    private ?string $url;

    public function __construct(string $filename, FileExtension $extension, MimeType $mimeType, FileType $type, ?User $uploadedBy)
    {
        $this->uuid = Uuid::v4();
        $this->filename = $filename;
        $this->extension = $extension;
        $this->mimeType = $mimeType;
        $this->type = $type;
        $this->uploadedBy = $uploadedBy;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): File
    {
        $this->filename = $filename;

        return $this;
    }

    public function getExtension(): FileExtension
    {
        return $this->extension;
    }

    public function setExtension(FileExtension $extension): File
    {
        $this->extension = $extension;

        return $this;
    }

    public function getMimeType(): MimeType
    {
        return $this->mimeType;
    }

    public function setMimeType(MimeType $mimeType): File
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getType(): FileType
    {
        return $this->type;
    }

    public function setType(FileType $type): File
    {
        $this->type = $type;

        return $this;
    }

    public function getUploadedBy(): ?User
    {
        return $this->uploadedBy;
    }

    public function setUploadedBy(?User $uploadedBy): File
    {
        $this->uploadedBy = $uploadedBy;

        return $this;
    }

    #[Deprecated('The following should only be used by normalizer\'s property accessors. Do not use the `url` property as it should not be set during File\'s lifecycle.')]
    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): File
    {
        $this->url = $url;

        return $this;
    }
}
