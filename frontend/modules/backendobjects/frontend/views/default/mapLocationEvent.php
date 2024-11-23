<?php

use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\overlays\Marker;
use open20\amos\core\utilities\MapsUtility;
use yii\helpers\ArrayHelper;
use open20\amos\events\AmosEvents;

?>
<?php
$showMap = false;

$gmap = $dataProvider->models;
$firstCoordinate = reset($gmap);
$mainEvent = $firstCoordinate['model'];

$title =  \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_maps');
// non mostro la mappa se è un evento streaming
if (!empty($mainEvent)) {
    $title_maps = $mainEvent->eventLanding->title_maps;
    $locationStreaming = \open20\amos\events\models\EventLocation::find()->andWhere(['is_location_streaming' => true])->one();
    if ($locationStreaming) {
        if ($mainEvent->event_location_id != $locationStreaming->id) {
            $showMap = true;
        }
    }
}

?>

<?php if ($showMap) { ?>
    <div class="uk-section section-map">

        <div class="uk-container">
            <div class="header-map">
                <h2><?= $title_maps ?></h2>
                <?php if (count($gmap) > 1) { ?>
                    <div class="content-legend">
                        <div class="legend"><span class="badge-legend"></span><?= \Yii::t('site', "Evento principale") ?></div>
                        <div class="legend"><span class="badge-legend badge-legend-child"></span><?= \Yii::t('site', "Eventi correlati") ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php
            echo \open20\amos\events\widgets\EventLocationMapWidget::widget([
                'dataProvider' => $dataProvider
            ]);
            ?>
        </div>


    </div>
<?php } ?>