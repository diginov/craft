<?php

namespace modules\site\validators;

use modules\site\helpers\StringHelper;

class PhoneNumberValidator extends \yii\validators\RegularExpressionValidator
{
    // Properties
    // =========================================================================

    /**
     * @inheritdoc
     */
    public $pattern = '/^[1-9][0-9]{2}-[0-9]{3}-[0-9]{4}$/';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $model->$attribute = StringHelper::formatPhone($model->$attribute);
        parent::validateAttribute($model, $attribute);
    }
}
