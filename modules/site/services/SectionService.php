<?php

namespace modules\site\services;

use modules\site\helpers\StringHelper;

use Craft;
use craft\base\Element;
use craft\events\SectionEvent;
use craft\helpers\App;

class SectionService extends \craft\base\Component
{
    // Public Methods
    // =========================================================================

    /**
     * Before saving a section.
     *
     * @param SectionEvent $event
     */
    public function beforeSaveSectionHandler(SectionEvent $event): void
    {
        if (!$event->isNew) {
            return;
        }

        // Set default preview target for headless mode
        if (Craft::$app->getConfig()->getGeneral()->headlessMode) {
            $event->section->previewTargets = [[
                'label' => 'Page',
                'urlFormat' => '{{ previewTargetUrl(object) }}',
                'refresh' => true,
            ]];
        }
    }

    /**
     * Returns the default preview target url.
     *
     * @param Element $element
     * @return string
     */
    public function getPreviewTargetUrl(Element $element)
    {
        $url = null;
        $site = $element->getSite();

        if ($site->handle === 'en') {
            $url = App::env('PREVIEW_URL_EN');
        }

        if ($site->handle === 'fr') {
            $url = App::env('PREVIEW_URL_FR');
        }

        if (empty($url)) {
            $url = App::env('PREVIEW_URL');
        }

        if (empty($url)) {
            $url = $site->getBaseUrl();
        }

        $url = StringHelper::ensureRight($url, '/');

        if (!$element->getIsHomepage()) {
            $url .= $element->uri;
        }

        if (Craft::$app->getConfig()->getGeneral()->addTrailingSlashesToUrls) {
            $url = StringHelper::ensureRight($url, '/');
        }

        return $url.'?preview=true';
    }
}
