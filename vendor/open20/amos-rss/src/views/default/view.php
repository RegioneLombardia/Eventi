<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\news
 * @category   CategoryName
 */
use amos\rss\Module;
use open20\amos\core\forms\CloseButtonWidget;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Module::t('amosrss', '#view_feed');

$this->params['breadcrumbs'][] = $this->title;

$element = $feed->current();
?>

<div class="post-details row rss-details">
    <div class="post-content col-xs-12">
        <h1 class="post-title col-xs-12 nop"><strong><?= $feed->getTitle() ?></strong></h1>
        <?php
        $enclosure = $element->getEnclosure();
        if (!is_null($enclosure)) {
            $url = $enclosure->url;
            echo Html::img($url, ['class' => 'img-responsive img-rss', 'alt' => Module::t('amosnews', 'Immagine della notizia')]);
        }
        ?>


        <h3 class="m-b-30"><?= $element->getTitle() ?></h3>
        <?= $element->getContent() ?>
    </div>
    <div class="col-xs-12 m-t-30">
        <?= CloseButtonWidget::widget(['urlClose' => $element->getCommentLink(), 'title' => 'Commenta', 'btnClass' => 'btn m-l-5 btn-administration-primary']) ?>

    </div>
    <div class="col-xs-12 m-t-30">
        <?= CloseButtonWidget::widget(['urlClose' => Url::previous()]) ?>
    </div>
</div>
