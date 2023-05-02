<?php

namespace App\Component\Attribute\Response;

use Attribute;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;

#[Attribute(Attribute::TARGET_METHOD)]
class EmptyResponse extends Response
{
    public function __construct(string $description, int $status = 204)
    {
        parent::__construct(
            response: $status,
            description: $description
        );
    }
}
