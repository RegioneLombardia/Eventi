<?php
/**
 * @var $templates
 * @var $showFavorites
 */

use app\assets\ResourcesAsset;
use app\modules\cms\helpers\CmsHelper;
use open20\amos\news\AmosNews;
use luya\helpers\Url;
use open20\amos\events\AmosEvents;
use yii\helpers\Html;
use open20\amos\events\utility\EventsUtility;


$resourceAsset = ResourcesAsset::register($this);

$flagAsset = \open20\amos\events\assets\FlagAssets::register($this);
$highlight = \open20\amos\events\models\EventHighlights::find()->andWhere(['event_id' => $model->id])->orderBy('id DESC')->count();
$eventLocation = $model->eventLocation;
$class = EventsUtility::getBackgroundClassCmsTemplate($model, $templates);


$truncate = 250;
?>

<?php $route = \open20\amos\events\utility\EventsUtility::getUrlLanding($model) ?>
<?php
if (!\Yii::$app->user->isGuest && $showFavorites) {
    echo \open20\amos\favorites\widgets\SelectFavoriteUrlsWidget::widget([
        'model' => $model,
        'id' => 'event-' . $model->id,
        'positionRelative' => true,
        'title' => $model->title,
        'url' => EventsUtility::getUrlLanding($model)
    ]);
} ?>
<a href="<?= $route ?>" class="link-evento" title="Visualizza dettaglio evento: <?= $model->getTitle(); ?>">
    <div class="box-evento">

        <div class="content-evento">

            <div class="img-evento">
                <?php
                $url = $model->getMainImageEvent('dashboard_news');
                $img = \yii\helpers\Html::img($url, ['alt' => AmosEvents::t('amosevents', 'Immagine delle evento: ') . $model->getTitle()]);
                echo $img;
                ?>
            </div>

            <div class="position-cover <?= $class ?>">
                <?php if ($model->eventType->webmeeting_webex && $model->webmeeting_webex_id) { ?>
                    <div class="badge-webex" uk-tooltip="<?= AmosEvents::t('amosevents', 'Evento Webex') ?>">
                        <span class="mdi mdi-television-play mdi-24px"></span>
                    </div>
                <?php } ?>
                <div class="content-tag">
                    <?php $tagsPreference = $model->getEventTagPreferences(true);
                    foreach ($tagsPreference as $tag) {
                        echo "<span class='tipologia-evento'>" . $tag['nome'] . "</span>";
                    }
                    ?>
                </div>
                <?php if ($highlight) { ?>
                    <div class="triangle"
                         uk-tooltip="title:<?= AmosEvents::t('amosevents', 'Evento in evidenza') ?>; pos: bottom-left"></div>
                <?php } ?>


                <div class="content-info-evento">
                    <?php if ($model->multilanguage) { ?>
                        <span class="multilingua" style="margin-right: 6px;">
                            <?= Html::img($flagAsset->baseUrl . '/img/italy.png', ['width' => 24, 'title' => AmosEvents::t('amosevents', "Evento multilingua")]) ?>
                            <?= Html::img($flagAsset->baseUrl . '/img/united-kingdom.png', ['width' => 24, 'title' => AmosEvents::t('amosevents', "Evento multilingua")]) ?>
                        </span>
                    <?php } ?>

                    <div class="titolo-evento">
                        <span><?= $model->getTitle(); ?></span>
                    </div>
                    <div class="content-data-location">
                        <div class="data-eventi uk-margin-remove-bottom">
                            <span class="mdi mdi-calendar-text mdi-24px data-icon"></span>
                            <div class="data-text">
                                <?php $beginDate = new \DateTime($model->begin_date_hour) ?>
                                <?php if (!empty($model->begin_date_hour)) {
                                    $endDate = new \DateTime($model->end_date_hour) ?>
                                    <span><?= \Yii::t('app', "dal") . ' ' . $beginDate->format('d.m.Y - H:i') . '</span><span>' . ' al ' . $endDate->format('d.m.Y - H:i') ?></span>
                                <?php } else { ?>
                                    <?= $beginDate->format('d.m.Y H:i') ?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="location-eventi">
                            <span class="mdi mdi-map-marker mdi-24px location-icon"></span>
                            <?php if ($eventLocation) { ?>
                                <span class="location-text"><?= $eventLocation->name ?></span>
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</a>