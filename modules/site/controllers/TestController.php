<?php

namespace modules\site\controllers;

use yii\web\Response;

class TestController extends BaseController
{
    // Public Methods
    // =========================================================================

    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        $variables = [
            'title' => 'Test Controller',
        ];

        return $this->renderTemplate('site/test/index', $variables);
    }
}
