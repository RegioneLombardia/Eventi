<?php

use app\modules\backendobjects\frontend\Module;
use kartik\datecontrol\DateControl;
use luya\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \open20\amos\events\utility\EventsUtility;
use \open20\amos\events\models\EventLanding;


?>


<?php
if (!is_null($dataProvider)) {
    if ($dataProvider->getTotalCount() > 0) { ?>
        <div class="section-today-streaming">
            <div class="title-intro-streaming">
                <div class="uk-container uk-width-1-1">
                    <h2>
                        <span class="mdi mdi-video-box uk-margin-small-right"></span><?= \Yii::t('app', "Oggi in streaming") ?>
                    </h2>
                </div>

            </div>

            <?php //echo $dataProvider->getModels();         
            $models = $dataProvider->getModels();

            $eventLanding = $models[0];
            $startStreaming = new \DateTime($eventLanding->date_begin_streaming);
            ?>

            <div class="wrap-playlist" id="video-active">
                <div class="uk-container uk-width-1-1">


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
                             

                                <!-- YOUTUBE -->
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
                                    <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_YOUTUBE)) { ?>
                                    <p><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_YOUTUBE, $eventLanding->streaming_url) ?></p>
                                <?php } else { ?>
                                    <iframe width="100%" height="480" src="<?= $url ?>" frameborder="0"
                                            allowfullscreen></iframe>
                                <?php } ?>

                                    <!-- FACEBOOK -->
                                <?php } elseif ($eventLanding->streaming_type == \open20\amos\events\models\EventLanding::STREAMING_TYPE_FACEBOOK) { ?>
                                    <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_FACEBOOK)) { ?>
                                    <p><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_FACEBOOK, $eventLanding->streaming_url) ?></p>
                                <?php } else { ?>
                                    <div id="fb-root"></div>
                                    <script async defer
                                            src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>
                                    <div class="fb-video" data-autoplay="true"
                                         data-href="<?= $eventLanding->streaming_url ?>" data-controls="false"
                                         data-allowfullscreen="false" data-show-captions="true">
                                    </div>
                                <?php } ?>

                                    <!-- MEDIAPORTAL -->
                                <?php } else if (($eventLanding->streaming_type == \open20\amos\events\models\EventLanding::STREAMING_TYPE_MEDIAPORTAL)) { ?>
                                    <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_MEDIAPORTAL)) { ?>
                                    <p><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_MEDIAPORTAL, $eventLanding->streaming_url) ?></p>
                                    <?php } else { ?>
                                        <iframe src="<?= $eventLanding->getUrlMediaPortalFormatted() ?>" width="100%"
                                                height="400"
                                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen>
                                        </iframe>
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
                            if ($key > 0) {
                                if (!empty($model->date_begin_streaming)) {
                                    $startStreamingModel = new \DateTime($model->date_begin_streaming);
                                    ?>
                                    <div class="col-md-4">
                                        <a class="wrap-video" href="<?= EventsUtility::getUrlLanding($model->event) ?>">
                                            <div class="uk-position-relative">
                                                <img src="<?= $model->event->getMainImageEvent('item'); ?>"
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
    <?php } ?>
<?php } ?>