<?php
$modelSearch = new \open20\amos\events\models\search\EventSearch();
$dataProvider = $modelSearch->cmsMarkerMapSearch(['conditionSearch' => "['event_id' => {$model->id}]"]);
?>
<?= \open20\amos\events\widgets\EventLocationMapWidget::widget([
    'dataProvider' => $dataProvider,
    'mapType' => $eventLanding->map_style
]);

