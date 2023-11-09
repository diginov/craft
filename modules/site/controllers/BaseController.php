<?php

namespace modules\site\controllers;

use Craft;
use craft\helpers\ArrayHelper;

abstract class BaseController extends \craft\web\Controller
{
    // Protected Methods
    // =========================================================================

    /**
     * Provide CORS headers on the response.
     *
     * @see GraphqlController::actionApi()
     */
    protected function addCorsHeaders(): void
    {
        $headers = $this->response->getHeaders();
        $headers->setDefault('Access-Control-Allow-Credentials', 'true');
        $headers->setDefault('Access-Control-Allow-Headers', 'Authorization, Content-Type, X-Craft-Authorization, X-Craft-Token');

        $generalConfig = Craft::$app->getConfig()->getGeneral();

        if ($generalConfig->allowedGraphqlOrigins === false) {
            return;
        }

        $origins = $this->request->getOrigin();

        if ($origins !== null) {
            $origins = ArrayHelper::filterEmptyStringsFromArray(array_map('trim', explode(',', $origins)));
        }

        if (is_array($generalConfig->allowedGraphqlOrigins)) {
            if (!empty($origins)) {
                foreach ($origins as $origin) {
                    if (in_array($origin, $generalConfig->allowedGraphqlOrigins, true)) {
                        $headers->setDefault('Access-Control-Allow-Origin', $origin);
                        break;
                    }
                }
            }
        } else if (!empty($origins)) {
            $headers->setDefault('Access-Control-Allow-Origin', reset($origins));
        } else {
            $headers->setDefault('Access-Control-Allow-Origin', '*');
        }
    }
}
