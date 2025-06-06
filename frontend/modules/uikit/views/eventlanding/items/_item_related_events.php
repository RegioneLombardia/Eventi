<?php

use app\assets\ResourcesAsset;
use app\modules\cms\helpers\CmsHelper;
use open20\amos\news\AmosNews;
use luya\helpers\Url;

$resourceAsset = ResourcesAsset::register($this);


$truncate = 250;
?>

<?php $route = \open20\amos\events\utility\EventsUtility::getUrlLanding($model) ?>
<a class="el-link" href="<?= $route ?>" class="title" title="Visualizza dettaglio evento: <?= $model->getTitle(); ?>">
    <li class="uk-transition-toggle" tabindex="0">
        <?php
        $url = $model->getMainImageEvent('item');
        $contentImage = CmsHelper::img($url, [
            // 'class' => 'el-image',
            'alt' => AmosNews::t('amosnews', 'Immagine delle evento: ') . $model->getTitle()
        ]);
        $contentImageLink = CmsHelper::a($contentImage, $route, ['title' => 'Leggi tutto']);
        ?>
        <?= $contentImage; ?>
        <div class="uk-position-bottom uk-panel uk-transition-slide-bottom-small">
            <div class="titolo-slider-eventi">
                <?= $model->getTitle(); ?>
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

    </li>
</a>