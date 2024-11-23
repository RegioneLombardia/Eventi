<?php

use open20\amos\news\AmosNews;
use luya\helpers\Url;
use yii\helpers\Html;
use app\assets\ResourcesAsset;
use app\modules\uikit\assets\FrontAsset;

$resourceAsset = ResourcesAsset::register($this);
FrontAsset::register($this);

$js = <<<JS
windowHeight = $(window).height() - $('.nav-container').outerHeight();
    slider = $('#lightSlider');
    
    slider.lightSlider({
        gallery: true,
        item: 1,
        loop:true,
        slideMargin: 0,
        thumbItem: 4,
        sliderHeight: windowHeight,      
        speed: 1200,
        pause: 4000,
        mode: 'fade'
    });      

$('#lightSlider').find('.lSliderItem').css('height', windowHeight + 'px');
JS;

$this->registerJs($js, $this::POS_READY);
?>

<?php //$model->getSchema(); ?>

<!--<a href="< ?= $route = Url::toRoute(['/backendobjects']); ?>">Indietro</a>-->
<?php
$url = $resourceAsset->baseUrl . '/img/img_default.jpg';
$newsImage = $model->getNewsImage();
if (!is_null($newsImage)) {
    $url = $newsImage->getWebUrl('square_large', false, true);
}
if (isset($model->newsCategorie) && !empty($model->newsCategorie)) {

    $classCat = 'cat_community';
}
?>
<div class="wrap-modules">
    <div class="wrap-lightslider gallerySlider detail-news-event">
        <ul id="lightSlider">
            <li class="lSliderItem sliderItemDot">
                <?=
                Html::img($url, [
                    'title' => AmosNews::t('amosnews', 'Immagine della notizia'),
                    'class' => 'el-image uk-cover detail-photo'
                ]);
                ?>

                <div class="caption">
                    <div class="el-content">
<!--                        <div class="--><?php //echo $classCat ?><!--">-->
<!--                            <p class="category">-->
<!--                                <span>--><?php //echo $model->newsCategorie->titolo ?><!--</span>-->
<!--                            </p>-->
<!--                        </div>-->

                        <h1 class="el-title"><?= $model->getTitle(); ?></h1>

                        <?php if (isset($model->sottotitolo) && !empty($model->sottotitolo)): ?>
                            <p class="el-subtitle"><?= $model->sottotitolo ?></p>
                        <?php endif; ?>

                        <div class="published-details">
                            <!--    $created_by    -->
                            <?php if (isset($model->created_by) && !empty($model->created_by)): ?>
                                <span><?= \Yii::t('site', 'di') . ' ' ?><strong><?= $model->createdUserProfile->nomeCognome ?></strong></span>
                            <?php endif; ?>
                            <?php if (isset($model->data_pubblicazione) && !empty($model->data_pubblicazione)): ?>
                                <span>|</span>
                                <span><?= Yii::$app->getFormatter()->asDate($model->data_pubblicazione) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <div class="nop uk-section-default uk-section">
        <div class="uk-container">
            <div class="row">
                <div class="col-md-12 content-before-sidebar">
                    <div class="capolettera uk-section-default uk-visible@xl uk-section">
                        <?php if (isset($model->descrizione) && !empty($model->descrizione)): ?>
                            <p class=""><?= $model->descrizione ?></p>
                        <?php endif; ?>
                        <?php if (!empty($model->getAttachments())) { ?>
                            <?= \open20\amos\attachments\components\AttachmentsList::widget([
                                'model' => $model,
                                'attribute' => 'attachments',
                                'viewDeleteBtn' => false,
                                'viewDownloadBtn' => true,
                                'viewFilesCounter' => true,
                            ]); ?>
                        <?php } ?>
                        <?= Html::a(\open20\amos\events\AmosEvents::t('amosevents', 'Torna alla landing'), \Yii::$app->request->referrer, [
                            'class' => 'btn btn-primary'
                        ]) ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

