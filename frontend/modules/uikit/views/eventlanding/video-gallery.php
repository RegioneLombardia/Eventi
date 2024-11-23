<?php

/**
 * @var $model \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

use yii\widgets\ListView;
use open20\amos\events\AmosEvents;
use open20\amos\events\models\EventLanding;

$js = <<<JS
    $('.wrap-video-thumbs a').click(function(e){
        var videoId = $(this).attr('data-video');
        var url = $('.wrap-active-video iframe').attr('src');
        url = url.replace(/\/[^\/]*$/, '/'+videoId);
        $('.wrap-active-video iframe').attr('src', url);
    });
JS;

$this->registerJs($js);
?>
<div id="layoutsection-0be" class="section-video landing-cms-section-playlist full-size-content green-blu-video uk-section-default uk-visible@xl uk-section" style="display:block !important">
    <div class="uk-container">

        <div class="">
            <div class="clearfix"></div>

            <?php
            $urlLanding = \open20\amos\events\utility\EventsUtility::getUrlLanding($model);
            $title = \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_video');
            if ($eventLanding && !empty($eventLanding->title_video)) {
                $title = $eventLanding->title_video;
            }
            //echo $dataProvider->getModels();
            $models = $dataProvider->getModels();

            $firstVideo = $models[0];
            $i = 0;
            ?>
            <?php if (!empty($firstVideo->url_video)) { ?>
            <h2><?= $title ?></h2>

            <?php
            $cookies = new \backend\modules\eventsadmin\models\BannerCookieForm();
            $cookies->loadCookie();
            ?>

            <div class="wrap-playlist" id="video-active">
                <div class="wrap-active-video">
                    <?php $canSeeMedia = $cookies->canSeeMedia(EventLanding::STREAMING_TYPE_YOUTUBE); ?>
                    <?php if (!$canSeeMedia) { ?>
                        <p style="color:white"><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_YOUTUBE, $firstVideo->getUrlEmbeddedVideo(), $urlLanding) ?></p>
                    <?php } else { ?>
                        <iframe src="<?= $firstVideo->getUrlEmbeddedVideo() ?>?rel=0" frameborder="0"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    <?php } ?>
                </div>
                <?php } ?>

                <?php if ($canSeeMedia && $totalCount > 1) { ?>
                    <div class="wrap-video-thumbs">
                        <?php foreach ($models as $model) : ?>
                            <?php
                            if (!empty($model->url_video)) {
                                $match = [];
                                $idVideo = '';
                                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $model->url_video, $match);
                                $idVideo = $match[1];
                                ?>
                                <a class="wrap-video" href="#video-active" uk-scroll="offset: 100"  data-video="<?= $idVideo ?>">
                                    <div class="uk-inline">
                                        <img src="https://img.youtube.com/vi/<?= $idVideo ?>/mqdefault.jpg"
                                             alt="<?= $model->title ?>" data-video="<?= $idVideo ?>"/>

                                        <div class="uk-overlay uk-overlay-default uk-position-bottom">
                                            <p><?= $model->title ?></p>
                                        </div>
                                    </div>
                                </a>
                            <?php } ?>
                        <?php endforeach; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>



</div>
