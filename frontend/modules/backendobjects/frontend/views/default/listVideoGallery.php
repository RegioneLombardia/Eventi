<?php

use app\modules\backendobjects\frontend\Module;
use kartik\datecontrol\DateControl;
use luya\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \open20\amos\events\models\EventLanding;

?>
<?php
if (!is_null($dataProvider)) {
    if ($dataProvider->getTotalCount() > 0) {
        $js = <<<JS
    $('.section-video').attr('style',"display:block !important");
JS;
        $this->registerJs($js);?>
<?php if (!$withoutSearch) { ?>
    <?php
    $form = ActiveForm::begin([
        'action' => Url::toRoute(['/backendobjects']),
        'method' => 'get',
        'options' => [
            'id' => 'element_form_' . $model->id,
            'class' => 'form wrap-search',
            'enctype' => 'multipart/form-data',
        ]
    ]);
    ?>
    <?php
    foreach ($searchFields as $field) {
        switch ($field->type) {
            case "TEXT":
                ?>
                <div class="col-xs-12 col-sm-6 col-lg-4">
                    <?= $form->field($model, $field->name) ?>
                </div>
                <?php
                break;
            case "DATE":
                ?>
                <div class="col-xs-12 col-sm-6 col-lg-4">
                    <?=
                    $form->field($model, $field->name)->widget(DateControl::className(), [
                        'type' => DateControl::FORMAT_DATE
                    ])
                    ?>
                </div>
                <?php
                break;
        }
    }
    ?>

    <div class="col-xs-12 wrap-btn">
        <?= Html::a(Module::t('Annulla'), [Yii::$app->controller->action->id, 'currentView' => Yii::$app->request->getQueryParam('currentView')], ['class' => 'btn btn-secondary']) ?>
        <?= Html::submitButton(Module::t('Cerca'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
<?php } // !$withoutSearch
?>


        <div class="<?= $cssClass ?>">
            <div class="clearfix"></div>

            <?php $model = \app\modules\cms\utility\CachedEvent::getModelEvent($parms);
            $landing = \app\modules\cms\utility\CachedEvent::getModelLanding();

            $title = \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_video');
            if ($landing && !empty($landing->title_video)) {
                $title = $landing->title_video;
            }
            //echo $dataProvider->getModels();
            $models = $dataProvider->getModels();

            $firstVideo = $models[0];
            $i = 0;
            ?>
            <?php if (!empty($firstVideo->url_video)) { ?>
            <h2><?= $title ?></h2>

            <div class="wrap-playlist" id="video-active">
                <div class="wrap-active-video">
                    <?php
                    $cookies = new \backend\modules\eventsadmin\models\BannerCookieForm();
                    $cookies->loadCookie();
                    ?>
                    <?php if (!$cookies->canSeeMedia(EventLanding::STREAMING_TYPE_YOUTUBE)) { ?>
                        <p style="color:white"><?= $cookies->alternativeTextVideo(EventLanding::STREAMING_TYPE_YOUTUBE, $firstVideo->getUrlEmbeddedVideo()) ?></p>
                    <?php } else { ?>
                        <iframe src="<?= $firstVideo->getUrlEmbeddedVideo() ?>?rel=0" frameborder="0"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    <?php } ?>
                </div>
                <?php } ?>

                <?php if ($dataProvider->getTotalCount() > 1) { ?>
                    <div class="wrap-video-thumbs">
                        <?php foreach ($models as $model) : ?>
                            <?php
                            if (!empty($model->url_video)) {
                                $match = [];
                                $idVideo = '';
                                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $model->url_video, $match);
                                $idVideo = $match[1];
                                ?>
                                <a class="wrap-video" href="#video-active" uk-scroll="offset: 100">


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
    <?php } else {
        $js = <<<JS
    // $('.section-video').attr('style',"display:none !important");
    // $('#menu-video').remove();

JS;
        $this->registerJs($js);
    } ?>
<?php } else {
    $js = <<<JS
    // $('.section-video').attr('style',"display:none !important");
    // $('#menu-video').remove();

JS;
    $this->registerJs($js);
} ?>