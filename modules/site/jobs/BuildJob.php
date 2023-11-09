<?php

namespace modules\site\jobs;

use modules\site\Site;
use Craft;

class BuildJob extends \craft\queue\BaseJob
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): ?string
    {
        return Craft::t('site', 'Publish website');
    }

    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        Site::getInstance()->getBuild()->runWebhook();
        $time = Site::getInstance()->getBuild()->getBuildTime();

        for($i = 0; $i <= $time; $i++) {
            sleep(1);
            $this->setProgress($queue, $i / $time);
        }
    }
}
