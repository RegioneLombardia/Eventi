<?php

use app\assets\ResourcesAsset;
use app\modules\cms\helpers\CmsHelper;
use open20\amos\news\AmosNews;
use luya\helpers\Url;

$resourceAsset = ResourcesAsset::register($this);


$truncate = 250;
?>

<?php $route = \open20\amos\events\utility\EventsUtility::getUrlLanding($model) ?>
<a href="<?= $route ?>" class="el-link title" title="Visualizza dettaglio evento: <?= $model->getTitle(); ?>">
    <div class="uk-transition-toggle">

        <?php
        $url = $model->getMainImageEvent();
        $contentImage = CmsHelper::img($url, [
            // 'class' => 'el-image',
            'alt' => AmosNews::t('amosnews', 'Immagine delle evento: ') . $model->getTitle()
        ]);
        $contentImageLink = CmsHelper::a($contentImage, $route, ['title' => 'Leggi tutto']);
        ?>
        <?= $contentImage; ?>

        <div class="uk-position-bottom uk-panel uk-transition-slide-bottom-small">
            <div class="titolo-slider-eventi">
                <?php if (CmsHelper::in_arrayCmsViewField('title', $viewFields) && isset($model->title) && !empty($model->title)) : ?>
                    <?php ?>

                    <?= $model->getTitle(); ?>

                    <?php /*} */ ?>
                <?php endif; ?>
                <div class="data-gruppo-eventi uk-margin-remove-bottom">
                    <?php $beginDate = new \DateTime($model->begin_date_hour) ?>
                    <?php if (!empty($model->begin_date_hour)) {
                        $endDate = new \DateTime($model->end_date_hour) ?>
                        <?= \Yii::t('app', "dal") . ' ' . $beginDate->format('d/m/Y H:i') . ' al ' . $endDate->format('d/m/Y H:i') ?>
                    <?php } else { ?>
                        <?= $beginDate->format('d/m/Y H:i') ?>
                    <?php } ?>
                </div>
            </div>

        </div>

    </div>
</a>