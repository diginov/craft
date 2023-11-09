<?php

namespace modules\site\helpers;

use craft\models\GqlSchema;

class GqlHelper extends \craft\helpers\Gql
{
    // Public Methods
    // =========================================================================

    /**
     * Return true if active schema can query example.
     *
     * @param GqlSchema|null $schema
     * @return bool
     */
    public static function canQueryExample(?GqlSchema $schema = null): bool
    {
        $allowedEntities = self::extractAllowedEntitiesFromSchema('read', $schema);
        return isset($allowedEntities['example']);
    }

    /**
     * Return true if active schema can mutate example.
     *
     * @param GqlSchema|null $schema
     * @return bool
     */
    public static function canMutateExample(?GqlSchema $schema = null): bool
    {
        $allowedEntities = self::extractAllowedEntitiesFromSchema('save', $schema);
        return isset($allowedEntities['example']);
    }
}
