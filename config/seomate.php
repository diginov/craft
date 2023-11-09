<?php

return [

    '*' => [
        'siteName' => [
            'en' => 'Craft CMS Starter',
            'fr' => 'Craft CMS Starter',
        ],

        'sitenamePosition' => 'after',
        'sitenameSeparator' => '-',

        'defaultMeta' => [
            'title' => ['globalMeta.metaTitle'],
            'description' => ['globalMeta.metaDescription'],
            'image' => ['globalMeta.metaImage'],
        ],

        'defaultProfile' => 'standard',

        'fieldProfiles' => [
            'standard' => [
                'title' => ['title'],
                'description' => ['metaDescription'],
                'image' => ['metaImage'],
            ]
        ],

        'additionalMeta' => [
            'robots' => 'index,follow',
            'og:type' => 'website',
            'twitter:card' => 'summary_large_image',
            'fb:profile_id' => '{{ globalMeta.metaFacebookApplication }}',
        ],

        'sitemapEnabled' => true,
        'sitemapLimit' => 100,
        'sitemapConfig' => [
            'elements' => [
                'pageHome' => ['changefreq' => 'weekly', 'priority' => 1],
            ],
        ],
    ],

    'dev' => [
        'cacheEnabled' => false,
        'sitemapEnabled' => false,

        'additionalMeta' => [
            'robots' => 'noindex,nofollow',
        ],
    ],

    'staging' => [
        'sitemapEnabled' => false,

        'additionalMeta' => [
            'robots' => 'noindex,nofollow',
        ],
    ],

];
