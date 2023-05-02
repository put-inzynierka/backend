<?php

namespace App\Component\Attribute\Response;

use App\Enum\SerializationGroup\BaseGroups;
use Attribute;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;

#[Attribute(Attribute::TARGET_METHOD)]
class ObjectResponse extends Response
{
    public function __construct(string $description, string $class, string $group, int $status = 200)
    {
        parent::__construct(
            response: $status,
            description: $description,
            content: new JsonContent(
                ref: new Model(type: $class, groups: [$group, BaseGroups::DEFAULT])
            )
        );
    }
}
