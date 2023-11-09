<?php

namespace modules\site\validators;

use modules\site\helpers\StringHelper;

class PostalCodeValidator extends \yii\validators\RegularExpressionValidator
{
    // Properties
    // =========================================================================

    /**
     * @inheritdoc
     */
    public $pattern = '/^(?!.*[DFIOQU])[A-VXY][0-9][A-Z] [0-9][A-Z][0-9]$/';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $model->$attribute = StringHelper::formatPostal($model->$attribute);
        parent::validateAttribute($model, $attribute);
    }
}
