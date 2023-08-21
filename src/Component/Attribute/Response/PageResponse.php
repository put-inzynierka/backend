<?php

namespace App\Component\Attribute\Response;

use App\Enum\SerializationGroup\BaseGroups;
use Attribute;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;

#[Attribute(Attribute::TARGET_METHOD)]
class PageResponse extends Response
{
    public function __construct(string $description, string $class, string $group)
    {
        parent::__construct(
            response: 200,
            description: $description,
            content: new JsonContent(
                properties: [
                    'total_count' => new Property(
                        property: 'total_count',
                        description: 'Total count of items across all pages',
                        type: 'integer',
                        example: 69,
                    ),
                    'page' => new Property(
                        property: 'page',
                        description: 'Current page',
                        type: 'integer',
                        example: 1,
                    ),
                    'per_page' => new Property(
                        property: 'per_page',
                        description: 'Count of items per page',
                        type: 'integer',
                        example: 10,
                    ),
                    'page_count' => new Property(
                        property: 'page_count',
                        description: 'Total count of pages',
                        type: 'integer',
                        example: 7,
                    ),
                    'items' => new Property(
                        property: 'items',
                        description: 'List of items',
                        type: 'array',
                        items: new Items(
                            ref: new Model(type: $class, groups: [$group, BaseGroups::DEFAULT])
                        )
                    ),
                ]
            )
        );
    }
}
