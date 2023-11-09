<?php

namespace modules\site\services;

use modules\site\helpers\ElementHelper;
use modules\site\helpers\StringHelper;

Use Craft;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\events\RegisterElementSourcesEvent;

class EntryService extends \craft\base\Component
{
    // Public Methods
    // =========================================================================

    /**
     * Before saving an entry.
     *
     * @param ModelEvent $event
     */
    public function beforeSaveHandler(ModelEvent $event): void
    {
        /** @var Entry $entry */
        $entry = $event->sender;

        // Clean the actual slug
        if (!$event->isNew && !empty($entry->slug)) {
            $entry->slug = ElementHelper::generateSlug((string) $entry->slug);
        }

        // Update the slug based on entry title
        if (!empty($entry->title) && $entry->getSection()->type !== 'single') {
            $entry->slug = ElementHelper::generateSlug((string) $entry->title);
        }
    }

    /**
     * Register entry sources.
     *
     * @param RegisterElementSourcesEvent $event
     */
    public function registerSourcesHandler(RegisterElementSourcesEvent $event): void
    {
        $globalSectionIds = [];
        foreach(Craft::$app->getSections()->getSectionsByType('single') as $section) {
            if (StringHelper::startsWith($section->handle, 'global')) {
                $globalSectionIds[] = $section->id;
            }
        }

        // Add global sections to a settings source
        $event->sources[] = [
            'key' => 'settings',
            'label' => Craft::t('app', 'Settings'),
            'criteria' => [
                'sectionId' => $globalSectionIds,
                'editable' => true,
            ],
            'defaultSort' => ['title', 'asc'],
        ];

        // Remove global sections from singles source
        foreach ($event->sources as $key => $source) {
            if (isset($source['key']) && ($source['key'] === 'singles')) {
                $sectionIds = $source['criteria']['sectionId'];

                foreach($globalSectionIds as $globalSectionId) {
                    if (($sectionKey = array_search($globalSectionId, $sectionIds)) !== false) {
                        unset($sectionIds[$sectionKey]);
                    }
                }

                $event->sources[ $key ]['criteria']['sectionId'] = $sectionIds;
            }
        }
    }
}
