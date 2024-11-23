<?php
/**
 * @var $models
 * @var $landing
 */

use open20\amos\events\models\EventLanding;

$title =  \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_video');
if($landing && !empty($landing->title_video)){
    $title = $landing->title_video;
}


$firstVideo = $models[0];
$i = 0;
?>
<?php if (!empty($firstVideo->url_video)) { ?>
<div class="wrap-playlist" id="video-active">
    <div class="wrap-active-video">
        <?php
        $cookies = new \backend\modules\eventsadmin\models\BannerCookieForm();
        $cookies->loadCookie();
        ?>
        <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_YOUTUBE)) { ?>
            <p style="color:white"><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_YOUTUBE, $firstVideo->getUrlEmbeddedVideo()) ?></p>
        <?php } else { ?>
            <amp-iframe src="<?= $firstVideo->getUrlEmbeddedVideo() ?>?rel=0" frameborder="0"
                    allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"
                        sandbox="allow-scripts allow-same-origin allow-presentation"
                        layout="responsive"
                        height="400"
                        width="700"
                    allowfullscreen>

            </amp-iframe>
        <?php } ?>
    </div>
    <?php } ?>

</div>
