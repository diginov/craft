<?php
/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here. You can see a
 * list of the available settings in vendor/craftcms/cms/src/config/GeneralConfig.php.
 *
 * @see \craft\config\GeneralConfig
 */

use craft\helpers\App;

return [

    '*' => [

        'baseCpUrl' => App::env('CRAFT_WEB_URL'),

        'aliases' => [
            '@web' => App::env('CRAFT_WEB_URL'),
        ],

        'allowAdminChanges' => App::env('ALLOW_ADMIN_CHANGES'),

        'enableGql' => true,
        'sendPoweredByHeader' => false,

        // 'headlessMode' => true,
        // 'sameSiteCookieValue' => 'None',

        'csrfTokenName' => 'site_csrf',
        'phpSessionName' => 'site_session',

        'autoLoginAfterAccountActivation' => true,
        'userSessionDuration' => 604800, # 7 days
        'verificationCodeDuration' => 604800, # 7 days

        'defaultCpLanguage' => 'en',
        'defaultCpLocale' => 'en-CA',

        'cpTrigger' => 'admin',
        'postCpLoginRedirect' => 'entries',

        'allowUppercaseInSlug' => false,
        'convertFilenamesToAscii' => true,
        'limitAutoSlugsToAscii' => true,
        'omitScriptNameInUrls' => true,

        'resourceBaseUrl' => '@web/bundles',
        'resourceBasePath' => '@webroot/bundles',

        'maxRevisions' => 5,
        'preloadSingles' => true,

        'defaultImageQuality' => 90,
        'maxUploadFileSize' => 104857600, # 100 megabytes

        'allowedFileExtensions' => [
            'avif',
            'gif',
            'jpeg',
            'jpg',
            'json',
            'pdf',
            'png',
            'svg',
            'webp',
        ],

    ],

    'dev' => [

        'devMode' => true,
        'disallowRobots' => true,
        'enableGraphqlCaching' => false,
        'enableTemplateCaching' => false,

        'testToEmailAddress' => [
            'example@my-craft-project.dev' => 'Craft - Dev',
        ],

    ],

    'staging' => [

        'allowUpdates' => false,
        'disallowRobots' => true,

        'testToEmailAddress' => [
            'example@my-craft-project.dev' => 'Craft - Staging',
        ],

    ],

    'production' => [

        'allowUpdates' => false,
        'enableGraphqlIntrospection' => false,

    ],

];
