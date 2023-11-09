<?php
/**
 * Yii Application Config
 *
 * Edit this file at your own risk!
 *
 * The array returned by this file will get merged with
 * vendor/craftcms/cms/src/config/app.php and app.[web|console].php, when
 * Craft's bootstrap script is defining the configuration for the entire
 * application.
 *
 * You can define custom modules and system components, and even override the
 * built-in system components.
 *
 * If you want to modify the application config for *only* web requests or
 * *only* console requests, create an app.web.php or app.console.php file in
 * your config/ folder, alongside this one.
 */

use diginov\sentrylogger\log\SentryTarget;

use craft\helpers\App;
use craft\log\MonologTarget;

use Psr\Log\LogLevel;

return [

    'id' => App::env('CRAFT_APP_ID') ?: 'CraftCMS',

    'components' => [

        'deprecator' => [
            'throwExceptions' => App::env('CRAFT_ENVIRONMENT') === 'dev',
        ],

        'log' => [
            'targets' => [

                'sentry' => function() {
                    if (!class_exists(SentryTarget::class)) {
                        return null;
                    }

                    return Craft::createObject([
                        'class' => SentryTarget::class,
                        'enabled' => App::env('CRAFT_ENVIRONMENT') !== 'dev',
                        'anonymous' => false,
                        'dsn' => App::env('SENTRY_DSN'),
                        'release' => App::env('SENTRY_RELEASE'),
                        'environment' => App::env('SENTRY_ENVIRONMENT'),
                        'levels' => ['error', 'warning'],
                        'exceptCodes' => [403, 404],
                        'exceptPatterns' => [
                            'Invalid cookie auth key attempted for user',
                            'Invalid session auth key attempted for user',
                            'Request didn’t meet the user agent and IP requirement for maintaining a user session',
                            'Tried to restore session from the the identity cookie',
                        ],
                    ]);
                },

                'site' => [
                    'class' => MonologTarget::class,
                    'name' => 'site',
                    'extractExceptionTrace' => !App::devMode(),
                    'allowLineBreaks' => App::devMode(),
                    'level' => App::devMode() ? LogLevel::INFO : LogLevel::WARNING,
                    'categories' => ['modules\site\*'],
                ],

            ],
        ],

    ],

    'modules' => [
        'site' => \modules\site\Site::class,
    ],

    'bootstrap' => ['site'],

];
