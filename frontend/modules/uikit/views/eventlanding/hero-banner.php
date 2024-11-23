<?php

/**
 * @var $model \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 */

use open20\amos\events\utility\EventsUtility;

$url = $model->getMainImageEvent('big');
$urlView = \open20\amos\events\utility\EventsUtility::getUrlLanding($model);

$module = \Yii::$app->getModule('backendobjects');
$moduleEvents = \Yii::$app->getModule('events');
$enableFavorites = $moduleEvents->enableFavorites;
$enableLandingCalendar = $moduleEvents->enableLandingCalendar;
$eventLocation = $model->eventLocation;

$templates = \open20\amos\events\utility\EventsUtility::getCmsTemplates();
$class = \open20\amos\events\utility\EventsUtility::getBackgroundClassCmsTemplate($model, $templates);

if (!empty($url)) {
    if (empty($options)) {
        $options = [
            'id' => get_class($model) . '-' . $model->id,
            'title' => $model->title
        ];
    }
    $optionsDesktop = $options;
    $optionsMobile = $options;
    $optionsDesktop['class'] = 'img-event img-desktop';
    $optionsMobile['class'] = 'img-event img-mobile';
    //    $optionsMobile['style'] = 'display:none';
}

?>

<!-- HERO BANNER -->
<div id="layoutsection-47e" class="hero-banner <?= $class ?> uk-section- uk-section-overlap uk-visible@xl uk-section">
    <div class="uk-container">
        <?php echo \yii\helpers\Html::img($url, $optionsDesktop); ?>
        <?php
        if (!empty($model->eventLogoMobile)) {
            $urlMobile = $model->eventLogoMobile->getWebUrl(EventsUtility::getImageCrops('big'));
            echo \yii\helpers\Html::img($urlMobile, $optionsMobile);
        }
        ?>
        <div class="uk-container uk-grid-margin">
            <div class="content-hero-title uk-grid" uk-grid="">
                <div id="d38" class="uk-width-expand@m uk-first-column">

                    <?php
                    if ($enableFavorites && !\Yii::$app->user->isGuest) {
                        echo \open20\amos\favorites\widgets\SelectFavoriteUrlsWidget::widget([
                            'model' => $model,
                            'id' => 'event-' . $model->id,
                            'title' => $model->title,
                            'url' => \open20\amos\events\utility\EventsUtility::getUrlLanding($model),
                            'module' => 'events'
                        ]);
                    }
                    ?>
                    <?php
                    if ($enableLandingCalendar) {?>
                        <?php $iconCalendar = '<span class="mdi mdi-calendar-export"></span>'; ?>
                        <?= \yii\helpers\Html::a($iconCalendar . \Yii::t('amosevents', 'Aggiungi a calendario'), [
                            '/events/event/force-download-ics',
                            'eid' => $model->id,
                        ], ['class' => 'btn btn-primary btn-add-cal']) ?>
                    <?php } ?>


                    <h1 id="headline-7bf"><?= $model->title ?></h1>
                    <div id="text-da2" class="luogo-evento uk-margin">
                        <?php if ($eventLocation) { ?>
                            <?= $model->getCompleteLocationString() ?>
                        <?php } ?>
                    </div>
                    <div id="text-6d0" class="data-evento uk-margin-small uk-margin-remove-bottom">
                        <?= $model->getMatchedAttribute('event_date') ?>
                    </div>
                    <div id="text-356" class="data-evento uk-margin">
                        <?= $model->getMatchedAttribute('enter_time') ?>
                    </div>
                </div>

                <!--   SEZIONE STREAMING             -->
                <div id="dc4" class="uk-width-expand@m">
                    <?= $this->render('streaming', ['model' => $model, 'eventLanding' => $eventLanding]) ?>
                </div>
            </div>
        </div>
    </div>
</div>