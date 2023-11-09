<?php

namespace modules\site\services;

use modules\site\helpers\StringHelper;

use Craft;
use craft\elements\User;
use craft\events\ModelEvent;
use craft\events\RegisterElementSourcesEvent;

class UserService extends \craft\base\Component
{
    // Public Methods
    // =========================================================================

    /**
     * Before saving a user.
     *
     * @param ModelEvent $event
     */
    public function beforeSaveHandler(ModelEvent $event): void
    {
        /** @var User $user */
        $user = $event->sender;

        // Set preferred language and locale
        $request = Craft::$app->getRequest();

        if (!$request->getIsConsoleRequest()) {
            $bodyParams = $request->getBodyParams();
            $preferredLanguage = $bodyParams['preferredLanguage'] ?? null;

            if (StringHelper::startsWith((string) $preferredLanguage, 'fr')) {
                $bodyParams['preferredLanguage'] = 'fr';
                $bodyParams['preferredLocale'] = 'fr-CA';
            } else if (StringHelper::startsWith((string) $preferredLanguage, 'en')) {
                $bodyParams['preferredLanguage'] = 'en';
                $bodyParams['preferredLocale'] = 'en-CA';
            }

            $request->setBodyParams($bodyParams);
        }

        // Set title case on first and last names
        if ($user->firstName) {
            $user->firstName = StringHelper::titleizeForHumans($user->firstName);
        }

        if ($user->lastName) {
            $user->lastName = StringHelper::titleizeForHumans($user->lastName);
        }

        if ($user->fullName) {
            $user->fullName = StringHelper::titleizeForHumans($user->fullName);
        }
    }

    /**
     * Register user sources.
     *
     * @param RegisterElementSourcesEvent $event
     */
    public function registerSourcesHandler(RegisterElementSourcesEvent $event): void
    {
        $isAdmin = Craft::$app->getUser()->getIsAdmin();

        if (!$isAdmin) {
            foreach($event->sources as $key => $source) {
                $sourceKey = $source['key'] ?? null;

                // Hide `Administrator` users from `All users` source
                if ($sourceKey === '*') {
                    $event->sources[$key]['criteria'] = [
                        'admin' => false,
                    ];
                }

                // Remove `Administrators` source
                if ($sourceKey === 'admins') {
                    unset($event->sources[$key]);
                }
            }
        }
    }
}
