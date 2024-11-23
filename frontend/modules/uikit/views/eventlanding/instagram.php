<?php

/**
 * @var $model \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 * @var $dataProvider \yii\data\ActiveDataProvider
 */


use amos\sitemanagement\models\SiteManagementSliderElem;
use open20\amos\events\models\EventLanding;

$cookies = new \backend\modules\eventsadmin\models\BannerCookieForm();
$cookies->loadCookie();
?>
<div id="layoutsection-7e5" class="section-instagram-video uk-section- uk-visible@xl uk-section"
     style="display:block !important">

    <div class="uk-container">
        <div id="listInstagramVideoGallery-container">
            <?php
            $sectionTitle = EventLanding::defaultLabelsTitleSections('title_instagram_video');
            if ($eventLanding && !empty($eventLanding->title_instagram_video)) {
                $sectionTitle = $eventLanding->title_instagram_video;
            } ?>
            <h2><strong><?= $sectionTitle ?></strong></h2>
            <?php if ($cookies->canSeeMedia(EventLanding::MEDIA_INSTAGRAM)) { ?>
                <div class="uk-child-width-1-2@s uk-child-width-1-3@m" uk-grid="masonry: true">
                    <?php foreach ($dataProvider->models as $model) {
                        $videoTitle = $model->title;
                        $videoUrl = $model->instagram_url_video;
                        $videoCaption = $model->instagram_video_caption;
                        ?>

                        <div>
                            <h3><?= $videoTitle ?></h3>
                            <?= SiteManagementSliderElem::instance()->getInstagramVideoEmbedCode($videoUrl, $videoCaption) ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } else {
                $redirectUrl = \open20\amos\events\utility\EventsUtility::getUrlLanding($model);
                $text = \Yii::t('site', "Per guardare i video instagram modifica le impostazioni relative ai cookie analytics e di profilazione cliccando <a href='{url}'>qui</a>",[
                    'url' => \Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/personalize-cookie?urlRedirect=' . urlencode($redirectUrl),
                ]) ?>
                <p><?= $text ?></p>
            <?php } ?>
        </div>
    </div>


</div>
