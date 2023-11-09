<?php

namespace modules\site\bundles\site;

use craft\web\assets\cp\CpAsset;
use craft\web\assets\vue\VueAsset;
use craft\web\View;

class SiteBundle extends \craft\web\AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->sourcePath = __DIR__ . '/dist';

        $this->depends = [
            CpAsset::class,
            VueAsset::class,
        ];

        $this->css = [
            'bundle.css',
        ];

        $this->js = [
            'bundle.js',
        ];

        parent::init();
    }

    /**
     * @inerhitdoc
     */
    public function registerAssetFiles($view): void
    {
        parent::registerAssetFiles($view);

        // Fix time picker format
        $view->registerJs('Craft.timepickerOptions.timeFormat = "H:i";', View::POS_HEAD);
    }
}
