<?php

namespace modules\site\web;

use modules\site\Site;

class SiteVariable extends \yii\base\Behavior
{
    // Properties
    // =========================================================================

    /**
     * @var Site
     */
    public Site $site;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        // Point `craft.site` to the modules\site\Site instance
        $this->site = Site::getInstance();
    }
}
