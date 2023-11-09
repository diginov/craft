<?php

namespace modules\site\gql\mutations;

use modules\site\gql\resolvers\mutations\ExampleMutationResolver;
use modules\site\gql\types\ExampleType;
use modules\site\helpers\GqlHelper;

use Craft;

use GraphQL\Type\Definition\Type;

class ExampleMutation extends \craft\gql\base\Mutation
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function getMutations(): array
    {
        if (!GqlHelper::canMutateExample()) {
            return [];
        }

        $mutations = [];
        $resolver = Craft::createObject(ExampleMutationResolver::class);

        if (GqlHelper::canSchema('example', 'save')) {
            $mutations['saveExample'] = [
                'name' => 'saveExample',
                'args' => [
                    'uid' => Type::string(),
                    'foo' => Type::nonNull(Type::string()),
                    'bar' => Type::nonNull(Type::boolean()),
                ],
                'resolve' => [$resolver, 'saveExample'],
                'description' => 'Save an example.',
                'type' => ExampleType::getType(),
            ];
        }

        return $mutations;
    }
}
