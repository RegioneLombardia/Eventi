<?php

use \open20\amos\events\models\EventLanding;
use open20\amos\core\helpers\Html;
use open20\amos\events\utility\EventsUtility;


?>



<div class="section-today-streaming">
    <div class="title-intro-streaming">
        <div class="uk-container">
            <h2>
                <span class="material-icons">smart_display</span><?= \Yii::t('app', "Oggi in streaming") ?>
            </h2>
        </div>

    </div>

    <?php
    ;
    $models = $dataProviderStreaming->getModels();

    $eventLanding = $models[0];
    $startStreaming = new \DateTime($eventLanding->date_begin_streaming);
    ?>

    <div class="wrap-playlist" id="video-active">
        <div class="uk-container">


            <div class="row">

                <div class="col-md-5">
                    <div class="info-streaming">


                        <h3><strong><?= $eventLanding->event->title ?></strong></h3>
                        <h4><?= $startStreaming->format('d/m/Y') . ' alle ' . $startStreaming->format('H:i') ?></h4>
                        <p><?= $startStreaming->event->description ?></p>
                        <?= Html::a(\Yii::t('app', 'Guarda ora'), EventsUtility::getUrlLanding($eventLanding->event), [
                            'class' => 'btn btn-live-streaming btn-lg text-uppercase m-t-25'
                        ]) ?>
                    </div>
                </div>
                <?php
                $cookies = new \backend\modules\eventsadmin\models\BannerCookieForm();
                $cookies->loadCookie();
                $streamingBgClass = 'streaming-bg';
                $showIcon = true;
                if (!$cookies->canSeeMedia($eventLanding->streaming_type)) {
                    $streamingBgClass = '';
                    $showIcon = false;
                }

                ?>
                <div class="col-md-7 <?= $streamingBgClass ?>">
                    <div id="streaming-video-container" class="streaming-video-container">


                        <!-- ------------------ YOUTUBE -------------------  -->
                        <?php

                        if ($eventLanding->streaming_type == \open20\amos\events\models\EventLanding::STREAMING_TYPE_YOUTUBE) { ?>
                            <?php
                            $url = \amos\sitemanagement\models\SiteManagementSliderElem::getUrlEmbedVideoStatic($eventLanding->streaming_url);
                            if (strpos($url, '?')) {
                                $url .= '&controls=0&autoplay=1&mute=1&vq=tiny';
                            } else {
                                $url .= '?controls=0&autoplay=1&mute=1&vq=tiny';
                            }
                            ?>
                            <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_YOUTUBE)) {?>
                            <p><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_YOUTUBE, $eventLanding->streaming_url) ?></p>
                        <?php } else { ;?>
                            <amp-iframe
                                    sandbox="allow-scripts allow-same-origin allow-presentation"
                                    layout="responsive"
                                    width="16"
                                    height="9"
                                    src="<?= $url ?>"
                                    frameborder="0"
                                    allowfullscreen>
                                <amp-img layout="fill" src="/img/img_default.jpg" placeholder></amp-img>

                            </amp-iframe>
                        <?php } ?>

                            <!-- ------------------ FACEBOOK -------------------  -->
                        <?php } elseif ($eventLanding->streaming_type == \open20\amos\events\models\EventLanding::STREAMING_TYPE_FACEBOOK) { ?>
                            <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_FACEBOOK)) { ?>
                            <p><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_FACEBOOK, $eventLanding->streaming_url) ?></p>
                        <?php } else { ?>
                            <amp-facebook width="16" height="9"
                                          layout="responsive"
                                          data-embed-as="video"
                                          data-href="<?= $eventLanding->streaming_url ?>">
                            </amp-facebook>
                        <?php } ?>

                            <!-- ------------------ MEDIAPORTAL -------------------  -->
                        <?php } else if (($eventLanding->streaming_type == \open20\amos\events\models\EventLanding::STREAMING_TYPE_MEDIAPORTAL)) { ?>
                            <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_MEDIAPORTAL)) { ?>
                            <p><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_MEDIAPORTAL, $eventLanding->streaming_url) ?></p>
                        <?php } else { ?>
                            <amp-iframe src="<?= $eventLanding->getUrlMediaPortalFormatted() ?>"
                                        sandbox="allow-scripts allow-same-origin allow-presentation"
                                        width="16"
                                        height="9"
                                        layout="responsive"
                                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                <amp-img layout="fill" src="/img/img_default.jpg" placeholder></amp-img>

                            </amp-iframe>
                        <?php } ?>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>

        <?php if (count($models) > 1) { ?>
            <div class="wrap-video-thumbs row uk-margin-small-top">
                <?php foreach ($models as $key => $model) { ?>
                    <?php
                    if ($key > 0 && $key <= 3) {
                        if (!empty($model->date_begin_streaming)) {
                            $startStreamingModel = new \DateTime($model->date_begin_streaming);
                            ?>
                            <div class="col-md-4">
                                <a class="wrap-video" href="<?= EventsUtility::getUrlLanding($model->event).'/amp' ?>">
                                    <div class="uk-position-relative">
                                        <img src="<?= $model->event->getMainImageEvent(); ?>"
                                             alt="<?= $model->event->title ?>"
                                             data-video="<?= $model->event->title ?>" class="uk-width-1-1"/>
                                        <div class="uk-overlay-primary uk-position-cover"></div>
                                        <div class="uk-overlay uk-position-bottom uk-light">
                                            <h4><strong><?= $model->event->title ?></strong></h4>
                                            <p> <?= $startStreamingModel->format('d/m/Y') . ' alle ' . $startStreamingModel->format('H:i') ?></p>

                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php }
                    }
                } ?>
            </div>
        <?php } ?>
    </div>
</div>
