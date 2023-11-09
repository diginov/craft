<?php

namespace modules\site\controllers;

use modules\site\Site;
use Craft;

class BuildController extends BaseController
{
    /**
     * Create a background job to run the build webhook.
     */
    public function actionRunWebhook(): void
    {
        Site::getInstance()->getBuild()->addQueueJob();
        $this->setSuccessFlash(Craft::t('site', 'Publish website launched.'));
    }
}
