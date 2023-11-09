<?php

namespace modules\site\services;

use modules\site\helpers\StringHelper;

use Craft;
use craft\helpers\FileHelper;

class AssetService extends \craft\base\Component
{
    // Public Methods
    // =========================================================================

    /**
     * Add a query string revision to front-end asset file.
     *
     * @param string $file
     * @return string
     */
    public function revisionFile(string $file): string
    {
        $filePath = Craft::getAlias('@webroot' . DIRECTORY_SEPARATOR . StringHelper::trimLeft($file, DIRECTORY_SEPARATOR));

        if (!file_exists($filePath)) {
            return $file;
        }

        if ($time = filemtime($filePath)) {
            return $file.'?v='.$time;
        }

        return $file;
    }

    /**
     * Returns the image base 64 encoded string.
     *
     * @param string $file
     * @return string|null
     */
    public function imageBase64(string $file): ?string
    {
        $filePath = Craft::getAlias('@webroot' . DIRECTORY_SEPARATOR . StringHelper::trimLeft($file, DIRECTORY_SEPARATOR));

        if (!file_exists($filePath)) {
            return null;
        }

        $mimeType = FileHelper::getMimeType($filePath);

        if (!StringHelper::startsWith($mimeType, 'image/')) {
            return null;
        }

        $binary = file_get_contents($filePath);
        $extension = StringHelper::toLowerCase(pathinfo($filePath, PATHINFO_EXTENSION));

        return sprintf('data:image/%s;base64,%s', $extension, base64_encode($binary));
    }
}
