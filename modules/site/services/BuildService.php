<?php

namespace modules\site\services;

use modules\site\Site;
use modules\site\jobs\BuildJob;

use Craft;
use craft\elements\Entry;
use craft\helpers\App;
use craft\helpers\Queue;

use DateInterval;
use DateTime;

class BuildService extends \craft\base\Component
{
    // Properties
    // =========================================================================

    /**
     * @var string[]
     */
    private array $_envs = [
        'BUILD_URL',
        'BUILD_URL_EN',
        'BUILD_URL_FR',
    ];

    // Public Methods
    // =========================================================================

    /**
     * Returns build badge image url.
     *
     * @return string|null
     */
    public function getBuildBadge(): ?string
    {
        return Craft::$app->getConfig()->getCustom()->buildBadge;
    }

    /**
     * Returns build time in seconds.
     *
     * @return int
     */
    public function getBuildTime(): int
    {
        $time = (int) Craft::$app->getConfig()->getCustom()->buildTime;
        return max($time, 60);
    }

    /**
     * Add a background job to run the build webhook.
     */
    public function addQueueJob(): void
    {
        Site::getInstance()->getCache()->clearCaches();

        $ttr = $this->getBuildTime() + 60;
        Queue::push(new BuildJob(), null, null, $ttr);
    }

    /**
     * Run build webhook.
     *
     * @return bool
     */
    public function runWebhook(): bool
    {
        $client = Craft::createGuzzleClient();

        foreach($this->_envs as $env) {
            $url = App::env($env);

            if ($url) {
                try {
                    $response = $client->request('POST', $url);
                } catch(\Throwable $e) {
                    Craft::error('Unable send request to build webhook: '.$e->getMessage(), __METHOD__);
                    return false;
                }

                if ($response->getStatusCode() !== 200) {
                    Craft::error('Invalid response from build webhook: '.$response->getBody(), __METHOD__);
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Propagate entries that should be published or unpublished.
     *
     * @param int $minutes
     * @return bool
     */
    public function propagateEntries(int $minutes = 15): bool
    {
        $propagate = false;

        $date = new DateTime();
        $date->setTime((int) $date->format('H'), (int) $date->format('i'), 0, 0);

        // Already expired entries
        $expireDate = clone $date;
        $expireDate->sub(new DateInterval("PT{$minutes}M"));

        $expireCount = Entry::find()
            ->expiryDate(['and', ">= {$expireDate->format(DateTime::ATOM)}", "<= {$date->format(DateTime::ATOM)}"])
            ->status(Entry::STATUS_EXPIRED)
            ->count();

        if ($expireCount > 0) {
            $propagate = true;
        }

        // Not published entries
        $publishDate = clone $date;
        $publishDate->add(new DateInterval("PT{$minutes}M"));

        $publishEntries = Entry::find()
            ->postDate(['and', ">= {$date->format(DateTime::ATOM)}", "<= {$publishDate->format(DateTime::ATOM)}"])
            ->status(Entry::STATUS_PENDING)
            ->all();

        if (count($publishEntries) > 0) {
            $elementService = Craft::$app->getElements();

            // Update the post date
            foreach($publishEntries as $entry) {
                $entry->postDate = $date;
                $elementService->saveElement($entry, updateSearchIndex: true);
            }

            $propagate = true;
        }

        if ($propagate) {
            $this->addQueueJob();
        }

        return $propagate;
    }
}
