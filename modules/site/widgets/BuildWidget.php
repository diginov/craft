<?php

namespace modules\site\widgets;

use Craft;
use modules\site\Site;

class BuildWidget extends \craft\base\Widget
{
    /**
     * @inerhitdoc
     */
    public static function displayName(): string
    {
        return Craft::t('site', 'Publish website');
    }

    /**
     * @inheritdoc
     */
    public static function icon(): ?string
    {
        return Craft::getAlias('@appicons/globe.svg');
    }

    /**
     * @inheritdoc
     */
    public function getBodyHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('site/_widgets/build.twig', [
            'badge' => Site::getInstance()->getBuild()->getBuildBadge(),
            'minutes' => ceil(Site::getInstance()->getBuild()->getBuildTime() / 60),
        ]);
    }
}
