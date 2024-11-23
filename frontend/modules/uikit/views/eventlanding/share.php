<?php
/**
 * @var $model \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 * @var $dataProvider \yii\data\ActiveDataProvider
 */


?>
<?php if ($eventLanding && !empty($eventLanding->is_social_share_enabled)) { ?>
    <div id="share_section-container">
        <?php
        $title = \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('share_title');
        if ($eventLanding && !empty($eventLanding->share_title)) {
            $title = $eventLanding->share_title;
        }
        ?>
        <div class="uk-container uk-margin-large-top uk-margin-large-bottom uk-padding-top section-share-social">
            <h2><?= $title ?></h2>

            <?php $url = \open20\amos\events\utility\EventsUtility::getUrlLanding($model); ?>
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
                'showAlwaysSocials' => ['facebook', 'twitter', 'linkedin', 'email']


            ]);
            ?>
        </div>
    </div>
<?php } ?>
