<?php

namespace app\controllers;

use open20\amos\comuni\models\IstatProvince;
use yii\helpers\ArrayHelper;

class FrontendUtility
{

    public static function getIstatProvince()
    {
        $provinceQuery = IstatProvince::find()->orderBy('nome');
        $moduleEvents = \Yii::$app->getModule('events');
        if ($moduleEvents && !empty($moduleEvents->showOnlyRegionInWizard)) {
            $provinceQuery->andWhere(['istat_regioni_id' => $moduleEvents->showOnlyRegionInWizard]);
        }
        return ArrayHelper::map($provinceQuery->asArray()->all(), 'id', 'nome');
    }


}