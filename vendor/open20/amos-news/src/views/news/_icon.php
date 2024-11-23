<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\news\views\news
 * @category   CategoryName
 */

use open20\amos\core\forms\ContextMenuWidget;
use open20\amos\core\forms\ItemAndCardHeaderWidget;
use open20\amos\core\helpers\Html;
use open20\amos\news\AmosNews;
use open20\amos\notificationmanager\forms\NewsWidget;
use open20\amos\news\utility\NewsUtility;
use open20\amos\core\utilities\CurrentUser;
use open20\design\assets\ShimmerDesignAsset;
ShimmerDesignAsset::register($this);

$hideCategory = false;

$newsCategories = NewsUtility::getAllNewsCategories();
if ($newsCategories->count() == 1) {
    $hideCategory = true;
} else {
    $category = $model->newsCategorie->titolo;
    $customCategoryClass = 'custom-category-bg-' . str_replace(' ', '-', strtolower($category));
    $colorBgCategory = $model->newsCategorie->color_background;
    $colorTextCategory = $model->newsCategorie->color_text;
}

if (strlen($model->descrizione) > 150)
    $model->descrizione = strip_tags($model->descrizione, 0, 147) . '...';
/**
 * @var \open20\amos\news\models\News $model
 */
?>

<div class="listview-container news-item nop">
    <div class="container-news col-xs-12 nop">
        <div class="card-wrapper">
            <div class="card card-img">
                <div class="img-responsive-wrapper w-100 pr-xl-3">
                    <div class="image-wrapper position-relative w-100 h-100">
                        <?php
                        $url = '/img/img_default.jpg';
                        if (!is_null($model->newsImage)) {
                            $url = $model->newsImage->getWebUrl('item_news', false, true);
                        }
                        $contentImage = Html::img($url, [
                            'class' => 'news-image shimmer-image',
                            
                            'alt' => AmosNews::t('amosnews', 'Vai alla notizia ' .  $model->titolo),
                            
                        ]);
                        ?>
                        <?= Html::a(
                            $contentImage,
                            $model->getFullViewUrl(),
                            [
                                'title' => 'Vai alla notizia ' .  $model->titolo,
                                'class' => 'img-shimmer'
                            ]
                        )

                        ?>
                        <?= ContextMenuWidget::widget([
                            'model' => $model,
                            'actionModify' => "/news/news/update?id=" . $model->id,
                            'actionDelete' => "/news/news/delete?id=" . $model->id,
                            'labelDeleteConfirm' => AmosNews::t('amosnews', 'Sei sicuro di voler cancellare questa notizia?'),
                            'modelValidatePermission' => 'NewsValidate'
                        ]) ?>
                        <?= NewsWidget::widget(['model' => $model]); ?>
                        <div class="card-calendar d-flex flex-column justify-content-center position-absolute rounded-0">
                            <span class="card-day font-weight-bold text-600 lead"><?= Html::tag('strong', \Yii::$app->getFormatter()->asDate($model->getPublicatedFrom(), 'd')) ?></span>
                            <span class="card-month text-uppercase font-weight-bold text-600 small"><?= Html::tag('strong', \Yii::$app->getFormatter()->asDate($model->getPublicatedFrom(), 'MMM')) ?></span>
                            <span class="card-year font-weight-light text-600 small"><?= \Yii::$app->getFormatter()->asDate($model->getPublicatedFrom(), 'y') ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-body pl-0">
                    <?=
                    ItemAndCardHeaderWidget::widget(
                        [
                            'model' => $model,
                            'publicationDateNotPresent' => true,
                            'enableLink' => !(CurrentUser::isPlatformGuest()),
                            'showPrevalentPartnership' => true,
                        ]
                    )
                    ?>
                    <hr class="w-75 my-2 ml-0">
                    <?php if (!$hideCategory) : ?>
                        <span class="card-category text-uppercase font-weight-normal <?= $customCategoryClass ?>  mb-3" <?php if ((!empty($colorBgCategory))) : ?> style="background-color: <?= $colorBgCategory ?> !important; padding:3px; " <?php endif; ?>><strong <?php if ((!empty($colorTextCategory))) : ?> style="color: <?= $colorTextCategory ?>" <?php endif; ?>><?= $category ?></strong></span>
                    <?php endif ?>
                    <div>

                        <?= Html::a(Html::tag('h3', $model->titolo, ['class' => 'card-title font-weight-bold']), $model->getFullViewUrl(), ['class' => 'link-list-title', 'title' => 'Vai alla notizia ' .  $model->titolo]) ?>
                        <?php if (!empty(\open20\amos\core\utilities\CwhUtility::getTargetsString($model))) : ?>
                            <a href="javascript:void(0)" data-toggle="tooltip" title="<?= \open20\amos\core\utilities\CwhUtility::getTargetsString($model) ?>">

                                <span class="mdi mdi-account-supervisor-circle text-muted"></span>

                                <span class="sr-only"><?= \open20\amos\core\utilities\CwhUtility::getTargetsString($model) ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php
                    $abstract = $model->getShortDescription();
                    $description = strip_tags($model->getDescription(true));
                    ?>
                    <p class="card-description font-weight-light"><?= !empty($abstract) ? $abstract : $description ?></p>
                    <a class="read-more small" href="<?= $model->getFullViewUrl() ?>" title="Vai alla news <?= $model->titolo ?>">
                        <?= Html::tag('span', AmosNews::t('amosnews', 'Leggi'), ['class' => 'text']) ?>
                        <!-- <span class="mdi mdi-arrow-right"></span> -->
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>