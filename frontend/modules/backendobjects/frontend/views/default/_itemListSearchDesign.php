<?php

use app\modules\backendobjects\frontend\Module;

$route = "#";
$cls = $model->className();
$module = Module::getInstance();

$realModel = $cls::findOne($model->id);
if (!is_null($realModel)) {
    if (property_exists($realModel, 'usePrettyUrl')) {
        $realModel->usePrettyUrl = true;
    }
    $route = \open20\amos\events\utility\EventsUtility::getUrlLanding($realModel);
//    $route = $realModel->getFullViewUrl();
}

if (method_exists($realModel, 'getPublicatedFrom')) {
    $date = $realModel->getPublicatedFrom();
    if (!empty($date)) {
        $publicationDate = \Yii::$app->formatter->asDate($date);
    }
} else if ($realModel instanceof \luya\cms\models\NavItem) {
    $publicationDate = \Yii::$app->formatter->asDate($realModel->timestamp_create);
} else if ($realModel instanceof \open20\amos\core\record\Record) {
    $publicationDate = \Yii::$app->formatter->asDate($realModel->created_at);
}

$beginDateHour = \Yii::$app->formatter->asDate($realModel->begin_date_hour);
$endDateHour = \Yii::$app->formatter->asDate($realModel->end_date_hour);
$location = $realModel->eventLocation->name;
if($realModel->eventLanding) {
    $description = $realModel->eventLanding->getShortDescription();
}
?>

<?php
if (!empty($realModel)) {
    echo $this->render(
        'bi-search-results-item',
        [
            'url' => $route,
            'type' => $realModel->getGrammar()->getModelLabel(),
            'titleLink' => '',
            'title' => $realModel->title,
            'description' => $description,
            'publicationDate' => $publicationDate,
            'beginDateHour' => $beginDateHour,
            'endDateHour' => $endDateHour,
            'location' => $location
        ]
    );
}
?>