<?php
/**
 * @var $model \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 */

use \open20\amos\events\models\EventLanding;
use open20\amos\events\AmosEvents;
use \amos\sitemanagement\models\SiteManagementSliderElem;

?>


<?php
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
                $showcountdown = 'display:none';
            } else {
                $show = 'display:none';
                $showcountdown = '';
            }
            ?>


            <div class="">
                <div class="section-streaming">
                    <div>
                        <div class="box-streaming">

                            <div>
                                <h3>Streaming</h3>
                            </div>

                            <?php if (!$isAlwaysVisible) { ?>
                                <div id="countdown-container" style="<?= $showcountdown ?>">
                                    <div class="box-widget countdown">
                                        <?= AmosEvents::t('amosevents', "Qui sarà visibile il video in streaming dell'evento.") ?>
                                        <div>
                                            <?php
                                            echo \russ666\widgets\Countdown::widget([
                                                'id' => 'countdown',
                                                'datetime' => date($eventLanding->date_begin_streaming), //date('Y-m-d H:i:s O', time() + 10),
                                                'format' => '<div><p>%D</p>giorni</div><div><p>%H</p>ore</div><div><p>%M</p>minuti</div><div><p>%S</p>secondi</div>',
                                                'tagName' => 'div',
                                                'events' => [
                                                    'finish' => 'function(){
                                                        $("#streaming-video-container").show();
                                                        $("#countdown-container").hide();
                                                    }',
                                                ],

                                            ]); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if (\Yii::$app->user->isGuest) { ?>
                                <div>

                                    <?php $redir = urlencode(\Yii::$app->request->absoluteUrl); ?>
                                    <?php if ($model->eventType->event_type == \open20\amos\events\models\EventType::TYPE_INFORMATIVE) { ?>
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
                                        }?>
                                        <p><?= AmosEvents::t('amosevents', "Per vedere lo streaming è necessario essere registrato all'evento.") ?></p>
                                        <div class="uk-flex uk-flex-middle">
                                            <?= \yii\helpers\Html::a(AmosEvents::t('amosevents', "Accedi"), \Yii::$app->params['platform']['backendUrl'] . '/admin/security/login-frontend?redir=' . $redir, [
                                                'class' => 'btn btn-primary uk-margin-remove-bottom uk-margin-small-right'
                                            ]); ?>
                                            <?= AmosEvents::t('amosevents', " oppure ") ?>
                                            <a href="#titlesubscribe"
                                               class="btn btn-primary uk-margin-remove-bottom uk-margin-small-left"
                                               uk-scroll="offset: 90"><?= AmosEvents::t('amosevents', "Registrati")?></a>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } else {
                                if (
                                    \open20\amos\events\utility\EventsUtility::isEventParticipant($model->id, \Yii::$app->user->id)
                                    || $model->eventType->event_type == \open20\amos\events\models\EventType::TYPE_INFORMATIVE
                                ) {
                                    ?>

                                    <div id="streaming-video-container" class="streaming-video-container" style="<?= $show ?>">
                                        <?php
                                        $cookies = new \backend\modules\eventsadmin\models\BannerCookieForm();
                                        $cookies->loadCookie();
                                        // YOUTUBE
                                        if ($eventLanding->streaming_type == EventLanding::STREAMING_TYPE_YOUTUBE) { ?>
                                            <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_YOUTUBE)) { ?>
                                            <p><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_YOUTUBE, $eventLanding->streaming_url) ?></p>
                                        <?php } else { ?>
                                            <iframe width="100%" height="400"
                                                    src="<?= SiteManagementSliderElem::getUrlEmbedVideoStatic($eventLanding->streaming_url) ?>"
                                                    frameborder="0" allowfullscreen></iframe>
                                        <?php } ?>
                                            <!-- FACEBOOK -->
                                        <?php } elseif ($eventLanding->streaming_type == EventLanding::STREAMING_TYPE_FACEBOOK) { ?>
                                            <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_FACEBOOK)) { ?>
                                            <p><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_FACEBOOK, $eventLanding->streaming_url) ?></p>
                                        <?php } else { ?>
                                            <div id="fb-root"></div>
                                            <script async defer
                                                    src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>
                                            <div class="fb-video" data-href="<?= $eventLanding->streaming_url ?>"
                                                 data-allowfullscreen="false" data-show-captions="true">

                                            </div>
                                        <?php } ?>
                                            <!-- MEDIAPORTAL -->
                                        <?php } else if (($eventLanding->streaming_type == EventLanding::STREAMING_TYPE_MEDIAPORTAL)) { ?>
                                            <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_MEDIAPORTAL)) { ?>
                                            <p><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_MEDIAPORTAL, $eventLanding->streaming_url) ?></p>
                                        <?php } else { ?>
                                            <iframe src="<?= $eventLanding->getUrlMediaPortalFormatted() ?>"
                                                    width="100%" height="400"
                                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                                    allowfullscreen>
                                            </iframe>
                                        <?php } ?>

                                        <?php } ?>
                                    </div>
                                <?php } else { ?>
                                    <?php if ($model->isSubscribtionsOpened()) { ?>
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
?>