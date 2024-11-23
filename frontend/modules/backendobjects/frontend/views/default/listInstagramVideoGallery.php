<?php

use amos\sitemanagement\models\SiteManagementSliderElem;
use open20\amos\events\models\EventLanding;

if (!is_null($dataProvider) && $dataProvider->getTotalCount() > 0) {
    $js = <<<JS
    $('.section-instagram-video').attr('style',"display:block !important");
JS;
    $this->registerJs($js);
    $model = \app\modules\cms\utility\CachedEvent::getModelEvent($parms);
    $landing =  \app\modules\cms\utility\CachedEvent::getModelLanding();

    $sectionTitle = EventLanding::defaultLabelsTitleSections('title_instagram_video');
    if ($landing && !empty($landing->title_instagram_video)) {
        $sectionTitle = $landing->title_instagram_video;
    } ?>
    <h2><strong><?= $sectionTitle ?></strong></h2>
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
<?php
} else {
    // Hide section
    $js = <<<JS
    $('.section-instagram-video').attr('style',"display:none !important");
    $('#menu-instagram-video').remove();

JS;
    $this->registerJs($js);
} ?>