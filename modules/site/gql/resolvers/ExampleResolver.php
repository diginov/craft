<?php

namespace modules\site\gql\resolvers;

use modules\site\helpers\GqlHelper;
use GraphQL\Type\Definition\ResolveInfo;

class ExampleResolver extends \craft\gql\base\Resolver
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function resolve(mixed $source, array $arguments, mixed $context, ResolveInfo $resolveInfo): mixed
    {
        $value = [
            'foo' => 'test',
            'bar' => true,
        ];

        return GqlHelper::applyDirectives($source, $resolveInfo, $value);
    }
}
