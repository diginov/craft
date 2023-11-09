<?php

namespace modules\site\services;

use Craft;
use craft\helpers\FileHelper;
use craft\utilities\ClearCaches;

use yii\base\InvalidArgumentException;
use yii\caching\TagDependency;

class CacheService extends \craft\base\Component
{
    /**
     * Clear caches.
     *
     * @see UtilitiesController::actionClearCachesPerformAction()
     * @see UtilitiesController::actionInvalidateTags()
     */
    public function clearCaches(): void
    {
        // Clear selected caches
        $caches = [
            'data',
            'linkfieldElementCache',
        ];

        foreach (ClearCaches::cacheOptions() as $cacheOption) {
            if (!in_array($cacheOption['key'], $caches, true)) {
                continue;
            }

            $action = $cacheOption['action'];

            if (is_string($action)) {
                try {
                    FileHelper::clearDirectory($action);
                } catch (InvalidArgumentException) {
                    // The directory doesn't exist
                } catch (\Throwable $e) {
                    Craft::warning("Could not clear the directory $action: " . $e->getMessage(), __METHOD__);
                }
            } elseif (isset($cacheOption['params'])) {
                call_user_func_array($action, $cacheOption['params']);
            } else {
                $action();
            }
        }

        // Invalidate all tags
        $cache = Craft::$app->getCache();

        foreach(ClearCaches::tagOptions() as $tagOption) {
            TagDependency::invalidate($cache, $tagOption['tag']);
        }
    }
}
