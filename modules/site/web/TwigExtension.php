<?php

namespace modules\site\web;

use modules\site\Site;

use craft\elements\Entry;

use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends \Twig\Extension\AbstractExtension implements \Twig\Extension\GlobalsInterface
{
    // Properties
    // =========================================================================

    /**
     * @var Entry|null
     */
    private ?Entry $_globalMeta = null;

    /**
     * @inheritdoc
     */
    public function getGlobals(): array
    {
        if ($this->_globalMeta === null) {
            $this->_globalMeta = Entry::find()->section('globalMeta')->one();
        }

        return [
            'globalMeta' => $this->_globalMeta,
        ];
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getFilters(): array
    {
        return [
            // Add `shuffle` filter to a shuffle array
            new TwigFilter('shuffle', function(array $value) {
                shuffle($value);
                return $value;
            }),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions(): array
    {
        $assetService = Site::getInstance()->getAsset();
        $sectionService = Site::getInstance()->getSection();

        return [

            // Add `rev` function to add query string revision to front-end asset file
            new TwigFunction('rev', [$assetService, 'revisionFile']),

            // Add `image64` function to get the image base 64 encoded string
            new TwigFunction('image64', [$assetService, 'imageBase64']),

            // Add `getPreviewUrl` function to get the default preview target url
            new TwigFunction('previewTargetUrl', [$sectionService, 'getPreviewTargetUrl']),

        ];
    }
}
