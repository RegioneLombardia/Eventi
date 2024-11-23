<?php

use open20\amos\events\AmosEvents;
?>

<?php
if (!empty($dataProvider)) {
    if ($dataProvider->getTotalCount() == 1) {
        $model = \app\modules\cms\utility\CachedEvent::getModelEvent($parms);
        $landing =  \app\modules\cms\utility\CachedEvent::getModelLanding();
        if ($landing && !empty($landing->is_social_share_enabled)) { ?>
            <?php

            $title =  \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('share_title');
            if($landing && !empty($landing->share_title)){
                $title = $landing->share_title;
            }
            ?>
            <div class="uk-container uk-margin-large-top uk-margin-large-bottom uk-padding-top section-share-social">
                <h2><?= $title ?></h2>

            <?php $url =  \open20\amos\events\utility\EventsUtility::getUrlLanding($model); ?>
                <?=
                \open20\amos\core\forms\editors\socialShareWidget\SocialShareWidget::widget([
                    'mode' => \open20\amos\core\forms\editors\socialShareWidget\SocialShareWidget::MODE_NORMAL,
                    'configuratorId' => 'socialShare',
                    'model' => $model,
                    'url' => $url,
                    'description' => $model->title,
                    //'imageUrl' => (!empty($model->video) ? $url : $urlOther),
                    'isRedationalContent' => true,
                    'isProtected' => false,
                    'labelShare' => '',
                     'showAlwaysSocials' => ['facebook', 'twitter', 'linkedin','email']


                ]);
                ?>
            </div>
        <?php }
    }
}

?>

