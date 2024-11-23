<?php

use yii\helpers\Html;
use open20\amos\events\utility\EventsUtility;

$mainEvent = $firstCoordinate['model'];
$model = $event;

$lat = $place['lat'];
$lng = $place['lng'];
$countLocations = 1;

$events = [];
if (!empty($coordinateInfos[$lat][$lng])) {
    $countLocations = (count($coordinateInfos[$lat][$lng]));
    $events = $coordinateInfos[$lat][$lng];
};
?>

<?php if ($countLocations == 1) {
    $events = [$model];
}
foreach ($events as $event) { ?>
    <h5 style="margin-bottom:5px;">
        <?php if ($mainEvent->id == $model->id) {
            echo $event->title;
        } else { ?>
            <?= Html::a($event->title, EventsUtility::getUrlLanding($event), [
                'target' => '_blank',
                'title' => \Yii::t('site', "Vai alla landing: {title}", ['title' => $event->title])
            ]) ?>
        <?php } ?>
    </h5>
    <p style="margin-top:0">
        <strong><?= $event->eventLocation->name ?></strong>
        <br><?= $event->eventLocationEntrance->name ?>
    </p>
<?php }

