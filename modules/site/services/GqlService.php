<?php

namespace modules\site\services;

use modules\site\gql\mutations\ExampleMutation;
use modules\site\gql\queries\ExampleQuery;
use modules\site\gql\types\ExampleType;

use craft\events\RegisterGqlMutationsEvent;
use craft\events\RegisterGqlQueriesEvent;
use craft\events\RegisterGqlSchemaComponentsEvent;
use craft\events\RegisterGqlTypesEvent;

class GqlService extends \craft\base\Component
{
    // Public Methods
    // =========================================================================

    /**
     * Register gql schema components handler.
     *
     * @param RegisterGqlSchemaComponentsEvent $event
     */
    public function registerGqlSchemaComponentsHandler(RegisterGqlSchemaComponentsEvent $event): void
    {
        $queries = [
            'Custom Queries' => [
                'example:read' => ['label' => 'View Example'],
            ],
        ];

        $event->queries = array_merge($event->queries, $queries);

        $mutations = [
            'Custom Mutations' => [
                'example:save' => ['label' => 'Save Example'],
            ],
        ];

        $event->mutations = array_merge($event->mutations, $mutations);
    }

    /**
     * Register gql types handler.
     *
     * @param RegisterGqlTypesEvent $event
     */
    public function registerGqlTypesHandler(RegisterGqlTypesEvent $event): void
    {
        $event->types[] = ExampleType::class;
    }

    /**
     * Register gql queries handler.
     *
     * @param RegisterGqlQueriesEvent $event
     */
    public function registerGqlQueriesHandler(RegisterGqlQueriesEvent $event): void
    {
        $queries = [
            ExampleQuery::getQueries(),
        ];

        $event->queries = array_merge($event->queries, ...$queries);
    }

    /**
     * Register gql mutations handler.
     *
     * @param RegisterGqlMutationsEvent $event
     */
    public function registerGqlMutationsHandler(RegisterGqlMutationsEvent $event): void
    {
        $mutations = [
            ExampleMutation::getMutations(),
        ];

        $event->mutations = array_merge($event->mutations, ...$mutations);
    }
}
