<?php
namespace app\modules\uikit\validators;


use yii\validators\Validator;
use open20\amos\core\module\BaseAmosModule;


class DynamicRecordSafeValidator extends Validator
{

    public function validateAttribute($model, $attribute)
    {
        return true;
    }




}

