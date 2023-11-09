<?php

namespace modules\site\gql\queries;

use modules\site\gql\resolvers\ExampleResolver;
use modules\site\gql\types\ExampleType;
use modules\site\helpers\GqlHelper;

use GraphQL\Type\Definition\Type;

class ExampleQuery extends \craft\gql\base\Query
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function getQueries($checkToken = true): array
    {
        if ($checkToken && !GqlHelper::canQueryExample()) {
            return [];
        }

        return [
            'example' => [
                'type' => ExampleType::getType(),
                'args' => [
                    'uid' => [
                        'name' => 'uid',
                        'type' => Type::string(),
                        'description' => 'Narrows the query results based on the UID.',
                    ],
                ],
                'resolve' => ExampleResolver::class.'::resolve',
                'description' => 'This query is used to get example data.',
            ],
        ];
    }
}
