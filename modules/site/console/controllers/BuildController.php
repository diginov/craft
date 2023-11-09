<?php

namespace modules\site\console\controllers;

use modules\site\Site;

use craft\helpers\Console;
use yii\console\ExitCode;

/**
 * Build commands.
 */
class BuildController extends \yii\console\Controller
{
    // Properties
    // =========================================================================

    /**
     * @var int number of minutes when checking for pending or expired entries.
     */
    public int $minutes = 15;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function options($actionID): array
    {
        $options = parent::options($actionID);

        if ($actionID === 'propagate-entries') {
            $options[] = 'minutes';
        }

        return $options;
    }

    /**
     * Start a build if there are entries that should be published or unpublished.
     */
    public function actionPropagateEntries(): int
    {
        $propagate = Site::getInstance()->getBuild()->propagateEntries($this->minutes);

        if ($propagate) {
            $this->stdout('Publish website launched.'.PHP_EOL, Console::FG_GREEN);
        } else {
            $this->stdout('No entries to propagate.'.PHP_EOL, Console::FG_YELLOW);
        }

        return ExitCode::OK;
    }
}
