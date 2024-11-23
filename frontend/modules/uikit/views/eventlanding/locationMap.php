<?php

use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\overlays\Marker;
use open20\amos\core\utilities\MapsUtility;
use yii\helpers\ArrayHelper;
use open20\amos\events\AmosEvents;
use open20\amos\events\models\EventLanding;

?>
<?php
$showMap = false;

$gmap = $dataProvider->models;
$firstCoordinate = reset($gmap);
$mainEvent = $firstCoordinate['model'];
$mapStyle = null;

$title = \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_maps');
// non mostro la mappa se è un evento streaming
if (!empty($mainEvent)) {
    $eventLanding = $mainEvent->eventLanding;
    $title_maps = $eventLanding->title_maps;
    $mapStyle = $eventLanding->map_style;

    $locationStreaming = \open20\amos\events\models\EventLocation::find()->andWhere(['is_location_streaming' => true])->one();
    if ($locationStreaming) {
        if ($mainEvent->event_location_id != $locationStreaming->id) {
            $showMap = true;
        }
    }
}

$cookies = new \backend\modules\eventsadmin\models\BannerCookieForm();
$cookies->loadCookie();
?>

<?php if ($showMap) { ?>
    <div class="uk-section section-map">
        <div class="uk-container">
            <div class="header-map">
                <h2><?= $title_maps ?></h2>
                <?php if (count($gmap) > 1) { ?>
                    <div class="content-legend">
                        <div class="legend"><span
                                    class="badge-legend"></span><?= \Yii::t('site', "Evento principale") ?></div>
                        <div class="legend"><span
                                    class="badge-legend badge-legend-child"></span><?= \Yii::t('site', "Eventi correlati") ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php
            if (!$cookies->canSeeMedia(EventLanding::MEDIA_MAPS)) {
                if (empty($redirectUrl)) {
                    $redirectUrl = \Yii::$app->request->absoluteUrl;
                }
                $text = \Yii::t('site', "Per guardare la mappa modifica le impostazioni relative ai cookie analytics e di profilazione cliccando <a href='{url}'>qui</a>", [
                    'url' => \Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/personalize-cookie?urlRedirect=' . urlencode($redirectUrl),
                ]) ?>
                <p><?= $text ?></p>
            <?php } else {
                echo \open20\amos\events\widgets\EventLocationMapWidget::widget([
                    'dataProvider' => $dataProvider,
                    'mapType' => $mapStyle
                ]);
            } ?>
        </div>


    </div>
<?php } ?>