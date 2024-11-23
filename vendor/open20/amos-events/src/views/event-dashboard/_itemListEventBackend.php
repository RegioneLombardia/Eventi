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
use open20\amos\core\forms\PublishedByWidget;
use yii\helpers\Html;
use open20\amos\core\views\toolbars\StatsToolbar;
use open20\amos\events\AmosEvents;
use open20\amos\notificationmanager\forms\NewsWidget;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use open20\amos\layout\utility\AmosIconsBi;


$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);

/**
 * @var \open20\amos\events\models\Event $model
 */
?>
<div class="event-card-item h-100 d-flex flex-column">

    <div class="event-card-image">
        <?php

        $url = $model->getMainImageEvent();
        $contentImage = Html::img($url, [
            'class' => 'img-fluid w-100',
            'alt' => AmosEvents::t('amosevents', 'Immagine dell\'evento')
        ]);
        ?>
        <?= Html::a($contentImage,  \open20\amos\events\utility\EventsUtility::getUrlLanding($model)) ?>
    </div>

    <div class="event-card-body">


        <!-- < ?= NewsWidget::widget(['model' => $model]); ?>
        < ?php $visible = isset($statsToolbar) ? $statsToolbar : false; ?>
        < ?php if ($visible) : ?>
            <div class="counter-column">
                < ?php
                echo StatsToolbar::widget([
                    'model' => $model,
                    'layoutType' => StatsToolbar::LAYOUT_VERTICAL,
                    'disableLink' => true,
                ]);
                ?>
            </div>
        < ?php endif; ?> -->

        <div class="py-2 text-column">
            <div class="mb-2 row">
                <div class="col-md-7 col-sm-8">


                    <?= Html::a(Html::tag('span', $model->title),  \open20\amos\events\utility\EventsUtility::getUrlLanding($model), ['class' => 'event-card-title']) ?>
                    <!--            --><?php //Html::tag('p', $model->description, ['class' => 'text'])
                    ?>
                   
                </div>
                <div class="col-md-5 col-sm-4 text-right">
                
    
                <?php  $userEventCreator = $model->userCreator;
                    $userLastUpdate = $model->getUserLastUpdate();
                    $str =  AmosEvents::t('amosevents', '<strong>Creatore evento</strong>') . ': ' . ($userEventCreator ? $userEventCreator->userProfile->nomeCognome : '') ."<br>";
                    $str .= AmosEvents::t('amosevents', '<strong>Autore ultima modifica</strong>') . ': ' . ($userLastUpdate ? $userLastUpdate->userProfile->nomeCognome : '');
                    ?>
                    <?php 
                    $urlInfo = "#";
                    $iconInfo = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_info_outline></use>";
                    $svgIconInfo = Html::tag('svg', $iconInfo, ['class' => 'icon icon-sm icon-secondary']);
                    $spanSvgIconInfo = Html::tag('span', $svgIconInfo, ['class' => 'rounded-icon rounded-secondary p-0']) . Html::tag('span', AmosEvents::t('amosevents', 'Modifica'), ['class' => 'sr-only']);
                    echo Html::a($spanSvgIconInfo, $urlInfo, ['class' => '', 'data-container'=> 'body', 'data-toggle' => 'popover', 'data-trigger' => 'hover', 'data-placement' =>'top', 'data-html'=>'true', 'title' => 'Info', 'data-content'=> $str], true);
                    ?>
                    <?php
                // DOWNLOAD BIGLIETTO
                echo \open20\amos\events\utility\EventsUtility::getButtonDownloadTicket($model, \Yii::$app->params['platform']['backendUrl'], 'btn btn-xs btn-icon p-0 ml-1',true); ?>
                    <?php
                    if (\Yii::$app->user->can('EVENT_UPDATE')) {
                        $urlModifica = "/events/event-dashboard/view?id=" . $model->id;
                        $iconModifica = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_edit></use>";
                        $svgIconModifica = Html::tag('svg', $iconModifica, ['class' => 'icon icon-white']);
                        $spanSvgIconModifica = Html::tag('span', $svgIconModifica, ['class' => 'rounded-icon rounded-secondary p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Modifica'), ['class' => 'sr-only']);
                        echo Html::a($spanSvgIconModifica, $urlModifica, ['class' => 'btn btn-xs btn-icon p-0 ml-1', 'data-toggle' => 'tooltip', 'title' => 'Modifica'], true);
                    }
                    if (\Yii::$app->user->can('EVENT_DELETE')) {
                        $urlDelete = "/events/event-dashboard/delete-event?id=" . $model->id;
                        $iconDelete = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_close></use>";
                        $svgIconDelete = Html::tag('svg', $iconDelete, ['class' => 'icon icon-white']);
                        $spanSvgIconDelete = Html::tag('span', $svgIconDelete, ['class' => 'rounded-icon rounded-danger p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Elimina'), ['class' => 'sr-only']);
                        echo Html::a($spanSvgIconDelete, $urlDelete, ['class' => 'btn btn-xs btn-icon p-0 ml-1', 'data-toggle' => 'tooltip', 'title' => 'Elimina'], true);
                    }
                    ?>
                </div>
            </div>

        </div>

    </div>
    <div class="row d-flex align-items-center mt-auto">

        <div class="col-xl-5 event-card-date d-flex align-items-center pb-2">
            <div class="mr-1">
                <svg class="icon icon-sm">
                    <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_event_available"></use>
                </svg>
            </div>
            <small><?= !empty($model->begin_date_hour) ? \Yii::$app->formatter->asDate($model->begin_date_hour) : '' ?></small>
        </div>
        <div class="col-xl-7 event-card-type pb-2 text-xl-right">
            <span class="badge badge-secondary py-2 text-truncate w-100" data-toggle="tooltip"
                  title="<?= $model->eventType->title ?>"
                  style="background-color:<?= $model->eventType->color ?>"><?= $model->eventType->title ?></span>
                  
        </div>

    </div>
    <?php


    ?>

</div>
<!-- < ?= ContextMenuWidget::widget([
    'model' => $model,
    'actionModify' => "/events/event-dashboard/view?id=" . $model->id,
    'modelValidatePermission' => 'NewsValidate',

]) ?> -->