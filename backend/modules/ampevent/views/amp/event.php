<?php

/**
 * @var $model \open20\amos\events\models\Event
 * @var $landing \open20\amos\events\models\EventLanding
 */


use open20\amos\events\models\EventType;

$this->title = $model->title;
$url = $model->getMainImageEvent();
$urlView = \open20\amos\events\utility\EventsUtility::getUrlLanding($model);

$module = \Yii::$app->getModule('backendobjects');
$eventLocation = $model->eventLocation;

$templates = \open20\amos\events\utility\EventsUtility::getCmsTemplates();
$class = \open20\amos\events\utility\EventsUtility::getBackgroundClassCmsTemplate($model, $templates);

?>

<div class="detail-event" style="padding-bottom:71px">
    <div class="hero-banner <?= $class ?>">
        <div class="img-bg">
            <amp-img src="<?= $url ?>" width="1.33" height="1" layout="fill" alt="Palazzo Lombardia"
                     class="img-sfondo"></amp-img>
        </div>
        <div class="uk-container">
            <div class="content-hero-title">
                <div>
                    <h1><?= $model->title ?></h1>
                    <div class="luogo-evento">
                        <?php if ($eventLocation) { ?>
                            <?= $eventLocation->name ?>
                        <?php } ?>
                    </div>
                    <div class="data-evento">
                        <?php $beginDate = new \DateTime($model->begin_date_hour) ?>
                        <?php if (!empty($model->begin_date_hour)) {
                            $endDate = new \DateTime($model->end_date_hour) ?>
                            <span><?= \Yii::t('app', "dal") . ' ' . $beginDate->format('d.m.Y - H:i') . '</span><span>' . ' al ' . $endDate->format('d.m.Y - H:i') ?></span>
                        <?php } else { ?>
                            <?= $beginDate->format('d.m.Y H:i') ?>
                        <?php } ?>
                    </div>
                </div>
                <div>
                    <!-- STREAMING   -->
                    <?php echo $this->render('parts/_streaming', ['event' => $model, 'eventLanding' => $landing]) ?>
                </div>
            </div>
        </div>

    </div>
    <div class="text-intro">
        <div class="uk-container">
            <?= $landing->description ?>
        </div>
    </div>

    <div class="section-programma">
        <div class="uk-container">
            <h2><?= \Yii::t('site', "Agenda") ?></h2>
            <div class="testo-programma">
                <?= $landing->schedule ?>
            </div>
        </div>


    </div>


    <!-- INFORMAZIONI DI CONTATTO-->


    <?php
    if (!empty($landing->contact_info_organizator)) {
        $title = \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_request_info');
        if ($landing && !empty($landing->title_request_info)) {
            $title = $landing->title_request_info;
        }
        ?>
        <div class="section-contact-info uk-container">
            <h2 class="uk-h1"><strong><?= $title ?></strong></h2>
            <p><?= $landing->contact_info_organizator ?></p>
        </div>
    <?php } ?>


    <!--    PROTAGONISTI -->
    <?php $protagonists = $landing->eventLandingProtagonists;
    if (!empty($protagonists)) {
        $titleProtagonist = \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_protagonists');
        if (!empty($landing->title_protagonists)) {
            $titleProtagonist = $landing->title_protagonists;
        }

        ?>
        <div class="section-protagonisti">
            <div class="uk-container">
                <h3><?= $titleProtagonist ?></h3>
                <div class="list-protagonist">
                    <div class="list-view uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l uk-grid uk-flex-top uk-flex-wrap-top">
                        <?php foreach ($protagonists as $protagonist) {
                            echo $this->render('parts/_item_protagonist', ['model' => $protagonist]);
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- GALLERIA IMMAGINI-->
    <?php $imageSlider = $landing->imageSlider;
    if (!empty($imageSlider)) {
        $images = $imageSlider->sliderElems;
        if (!empty($images)) {

            $titlePics = \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_pics');
            if (!empty($landing->title_pics)) {
                $titlePics = $landing->title_pics;
            }
            ?>
            <div class="section-gallery">
                <div class="uk-container">


                    <h3><?= $titlePics ?></h3>
                    <div class="row">
                        <?php foreach ($images as $image) {
                            echo $this->render('parts/_item_image_slider', ['model' => $image]);
                        } ?>
                    </div>

                </div>
            </div>
        <?php } ?>
    <?php } ?>

    <!-- NEWS / INFO   -->
    <?php $news = $landing->news;
    if (!empty($news)) {
        $titleInfo = \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_info');
        if (!empty($landing->title_info)) {
            $titleInfo = $landing->title_info;
        }
        ?>
        <div class="section-news">
            <div class="uk-container">
                <h3><?= $titleInfo ?></h3>
                <?php foreach ($news as $singleNews) {
                    echo $this->render('parts/_news', ['model' => $singleNews]);
                } ?>
            </div>
        </div>
    <?php } ?>

    <!-- GALLERIA VIDEO-->
    <?php $videSlider = $landing->videoSlider;
    if (!empty($videSlider)) {
        $videos = $videSlider->sliderElems;
        if (!empty($videos)) {

            $titleVideo = \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_video');
            if (!empty($landing->title_video)) {
                $titleVideo = $landing->title_video;
            } ?>

            <div class="section-video">
                <div class="uk-container">
                    <h3><?= $titleVideo ?></h3>
                    <?php echo $this->render('parts/_video_gallery', ['models' => $videos, 'landing' => $landing]); ?>
                </div>
            </div>

        <?php } ?>
    <?php } ?>

    <!--    EVENTI FIGLI-->
    <?php $eventChildren = $model->eventChildren;
    if (!empty($eventChildren)) { ?>
        <div class="prossimi-eventi">
            <div class="uk-container">
                <div class="tit-prox-eventi">
                    <span class="material-icons">
                        calendar_month
                    </span>
                    <?= \Yii::t('site', 'Lista eventi') ?>
                </div>
                <div class="row">
                    <?php foreach ($eventChildren as $eventChild) {
                        echo $this->render('parts/_item_event-child', ['model' => $eventChild]);
                    } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if ($model->eventType->event_type != EventType::TYPE_INFORMATIVE) { ?>
        <div class="form-section green-blu-form section-landing-cms-form">
            <div class="uk-container">
                <h3><?= \Yii::t('app', "Iscriviti all'evento") ?></h3>
                <p style="color:white"><?= \Yii::t('app', "Per iscriverti all'evento clicca sul <a href='{link}'>link</a> e poi ACCEDI CON LA TUA IDENTITA' DIGITALE", [
                        'link' => $urlView
                    ]) ?>
                </p>
            </div>
        </div>
    <?php } ?>


</div>