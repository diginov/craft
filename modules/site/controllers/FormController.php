<?php

namespace modules\site\controllers;

use Craft;
use yii\web\Response;

class FormController extends BaseController
{
    // Properties
    // =========================================================================

    /**
     * @inerhitdoc
     */
    protected int|bool|array $allowAnonymous = [
        'csrf',
        'submit',
    ];

    // Public Methods
    // =========================================================================

    /**
     * @inerhitdoc
     */
    public function beforeAction($action): bool
    {
        // Add CORS headers for all actions
        $this->addCorsHeaders();

        // Disable validation for the csrf action
        if ($action->id === 'csrf') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Returns the current CSRF token.
     *
     * @return Response
     */
    public function actionCsrf(): Response
    {
        return $this->asJson([
            'name' => Craft::$app->getConfig()->getGeneral()->csrfTokenName,
            'value' => Craft::$app->getRequest()->getCsrfToken(),
        ]);
    }

    /**
     * Submits and saves a form submission.
     *
     * @return Response
     */
    public function actionSubmit(): Response
    {
        // This is a preflight request, no need to run the action
        if ($this->request->getIsOptions()) {
            $this->response->format = Response::FORMAT_RAW;
            $this->response->data = '';
            return $this->response;
        }

        // Run the form submit action
        $this->request->setAcceptableContentTypes([
            'application/json' => ['q' => 1],
        ]);

        if ($this->request->getIsPost()) {
            return Craft::$app->runAction('formie/submissions/submit');
        }

        $this->response->data = '';
        return $this->response;
    }
}
