<?php

namespace modules\site\gql\resolvers\mutations;

use modules\site\helpers\GqlHelper;
use GraphQL\Type\Definition\ResolveInfo;

class ExampleMutationResolver extends \craft\gql\base\MutationResolver
{
    // Public Methods
    // =========================================================================

    /**
     * Save an example.
     *
     * @param $source
     * @param array $arguments
     * @param $context
     * @param ResolveInfo $resolveInfo
     * @return array
     */
    public function saveExample($source, array $arguments, $context, ResolveInfo $resolveInfo): array
    {
        $this->requireSchemaAction('example', 'save');

        $uid = $arguments['uid'] ?? null;

        $value = [
            'foo' => $arguments['foo'],
            'bar' => $arguments['bar'],
        ];

        return GqlHelper::applyDirectives($source, $resolveInfo, $value);
    }
}
