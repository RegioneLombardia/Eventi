<?php

/**
 * @var $event \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 */

use \open20\amos\events\models\EventLanding;
use open20\amos\events\AmosEvents;
use \amos\sitemanagement\models\SiteManagementSliderElem;

?>


<?php
if (!empty($event) && !empty($eventLanding)) {
    if ($eventLanding->streaming_url) {
        $currentTime = new \DateTime('now', new \DateTimeZone("Europe/Rome"));
        $isAlwaysVisible = false;
        if (empty($eventLanding->date_begin_streaming)) {
            $isAlwaysVisible = true;
        }
        $beginStreamingDate = new \DateTime($eventLanding->date_begin_streaming, new \DateTimeZone("Europe/Rome"));
        $timestamp = date('F d, Y H:i:s', strtotime($eventLanding->date_begin_streaming));
        $show = false;
        $showcountdown = '';
        if ($currentTime >= $beginStreamingDate || $isAlwaysVisible) {
            $show = '';
            $showcountdown = 'visibility: hidden; display:none';
        } else {
            $show = 'display:none';
            $showcountdown = '';
        }
?>


        <div>
            <div class="section-streaming">
                <div>
                    <div class="box-streaming">
                        <div>
                            <h3>Streaming</h3>
                        </div>

                        <?php if (!$isAlwaysVisible) { ?>
                            <amp-animation id="hide-timeout-event" layout="nodisplay">
                                <script type="application/json">
                                    [{
                                            "duration": "1s",
                                            "fill": "both",
                                            "selector": "#countdown-container",
                                            "keyframes": {
                                                "visibility": "hidden"
                                            }
                                        },
                                        {
                                            "duration": "1s",
                                            "fill": "both",
                                            "selector": "#streaming-video-container",
                                            "keyframes": {
                                                "visibility": "visible"
                                            }
                                        }
                                    ]
                                </script>
                            </amp-animation>

                            <amp-date-countdown id="countdown-container" style="<?= $showcountdown ?>" timestamp-seconds="<?= strtotime($eventLanding->date_begin_streaming) ?>" on="timeout: hide-timeout-event.start" height="180" when-ended="stop" locale="en">
                                <template type="amp-mustache">
                                    <div id="countdown"><div class="numero"><p>{{d}}</p> giorni</div> <div><p>{{h}}</p> ore</div> <div><p>{{m}}</p> minuti</div> <div><p>{{s}}</p> secondi</div></div>
                                </template>
                            </amp-date-countdown>
                        <?php } ?>

                        <?php if (\Yii::$app->user->isGuest) { ?>
                            <div>

                                <?php $redir = urlencode(\Yii::$app->request->absoluteUrl); ?>
                                <?php if ($event->eventType->event_type == \open20\amos\events\models\EventType::TYPE_INFORMATIVE) { ?>
                                    <p><?= AmosEvents::t('amosevents', "Per vedere lo streaming è necessario accedere alla piattaforma.") ?></p>
                                    <div class="uk-flex uk-flex-middle">
                                        <?= \yii\helpers\Html::a(AmosEvents::t('amosevents', "Accedi"), \Yii::$app->params['platform']['backendUrl'] . '/admin/security/login-frontend?redirect_post_reg=' . $redir, [
                                            'class' => 'btn btn-primary uk-margin-remove-bottom uk-margin-small-right'
                                        ]); ?>
                                    </div>
                                <?php } else { ?>
                                    <?php
                                    if (strpos($redir, '?') !== false) {
                                        $redir .= '&spid-done=1';
                                    } else {
                                        $redir .= '?spid-done=1';
                                    } ?>
                                    <p><?= AmosEvents::t('amosevents', "Per vedere lo streaming è necessario essere registrato all'evento.") ?></p>
                                    <div class="uk-flex uk-flex-middle">
                                        <?= \yii\helpers\Html::a(AmosEvents::t('amosevents', "Accedi"), \Yii::$app->params['platform']['backendUrl'] . '/admin/security/login-frontend?redir=' . $redir, [
                                            'class' => 'btn btn-primary uk-margin-remove-bottom uk-margin-small-right'
                                        ]); ?>
                                        <?= AmosEvents::t('amosevents', " oppure ") ?>
                                        <a href="#titlesubscribe" class="btn btn-primary uk-margin-remove-bottom uk-margin-small-left">
                                            <?= AmosEvents::t('amosevents', "Registrati") ?></a>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php } else {
                            if (
                                \open20\amos\events\utility\EventsUtility::isEventParticipant($event->id, \Yii::$app->user->id)
                                || $event->eventType->event_type == \open20\amos\events\models\EventType::TYPE_INFORMATIVE
                            ) {
                            ?>

                                <div id="streaming-video-container" class="streaming-video-container" style="<?= $show ?>">
                                    <?php
                                    $cookies = new \backend\modules\eventsadmin\models\BannerCookieForm();
                                    $cookies->loadCookie();
                                    //                                      <!-- ------------------- YOUTUBE --------------------  -->
                                    if ($eventLanding->streaming_type == EventLanding::STREAMING_TYPE_YOUTUBE) { ?>
                                        <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_YOUTUBE)) { ?>
                                            <p><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_YOUTUBE, $eventLanding->streaming_url) ?></p>
                                        <?php } else {
                                            $url = SiteManagementSliderElem::getUrlEmbedVideoStatic($eventLanding->streaming_url);
                                        ?>
                                            <amp-iframe sandbox="allow-scripts allow-same-origin allow-presentation" layout="responsive" width="16" height="9" src="<?= $url ?>" frameborder="0" allowfullscreen>
                                                <amp-img layout="fill" src="/img/img_default.jpg" placeholder></amp-img>

                                            </amp-iframe>
                                        <?php } ?>
                                        <!-- ------------------ FACEBOOK -------------------  -->
                                    <?php } elseif ($eventLanding->streaming_type == EventLanding::STREAMING_TYPE_FACEBOOK) { ?>
                                        <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_FACEBOOK)) { ?>
                                            <p><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_FACEBOOK, $eventLanding->streaming_url) ?></p>
                                        <?php } else { ?>
                                            <amp-facebook width="16" height="9"
                                                          layout="responsive"
                                                          data-embed-as="video"
                                                          data-href="<?= $eventLanding->streaming_url ?>">
                                            </amp-facebook>
                                        <?php } ?>
                                        <!-- -------------------- MEDIAPORTAL ----------------------  -->
                                    <?php } else if (($eventLanding->streaming_type == EventLanding::STREAMING_TYPE_MEDIAPORTAL)) { ?>
                                        <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_MEDIAPORTAL)) { ?>
                                            <p><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_MEDIAPORTAL, $eventLanding->streaming_url) ?></p>
                                        <?php } else { ?>
                                            <amp-iframe src="<?= $eventLanding->getUrlMediaPortalFormatted() ?>" sandbox="allow-scripts allow-same-origin allow-presentation" width="16" height="9" layout="responsive" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
                                                <amp-img layout="fill" src="/img/img_default.jpg" placeholder></amp-img>
                                            <?php } ?>

                                        <?php } ?>
                                </div>
                            <?php } else { ?>
                                <?php if ($event->isSubscribtionsOpened()) { ?>
                                    <?= AmosEvents::t('amosevents', "Registrati per vedere lo streaming.") ?>
                                <?php } else { ?>
                                    <?= AmosEvents::t('amosevents', "Le registrazioni sono chiuse.") ?>
                                <?php } ?>

                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </div>
<?php
    }
}
?>