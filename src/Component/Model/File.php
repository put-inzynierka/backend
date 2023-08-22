<?php

namespace App\Component\Model;

use App\Enum\SerializationGroup\BaseGroups;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;

class File
{
    #[Groups([BaseGroups::DEFAULT])]
    #[Property(
        description: 'The UUID of the file',
        example: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
    )]
    protected string $uuid;

    #[Groups([BaseGroups::DEFAULT])]
    #[Property(
        description: 'The URL address of the file',
        example: 'https://inzynierka-api.fortek.dev/cdn/f47ac10b-58cc-4372-a567-0e02b2c3d479',
    )]
    protected string $url;

    public function __construct(string $uuid, string $url)
    {
        $this->uuid = $uuid;
        $this->url = $url;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
