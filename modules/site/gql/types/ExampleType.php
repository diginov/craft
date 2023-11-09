<?php

namespace modules\site\gql\types;

use Craft;
use craft\gql\GqlEntityRegistry;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ExampleType extends \craft\gql\base\ObjectType
{
    // Public Methods
    // =========================================================================

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'Example';
    }

    /**
     * @return Type
     */
    public static function getType(): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        return GqlEntityRegistry::createEntity(self::getName(), new ObjectType([
            'name' => static::getName(),
            'fields' => self::class.'::getFieldDefinitions',
            'description' => 'This is the type implemented by example.',
        ]));
    }

    /**
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        $fields = [
            'foo' => [
                'name' => 'foo',
                'type' => Type::string(),
                'description' => 'This is an example field.',
            ],
            'bar' => [
                'name' => 'bar',
                'type' => Type::boolean(),
                'description' => 'This is an example field.',
            ],
        ];

        return Craft::$app->getGql()->prepareFieldDefinitions($fields, self::getName());
    }
}
