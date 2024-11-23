<?php

use open20\amos\events\AmosEvents;
use yii\helpers\Html;

$flagAssets = \open20\amos\events\assets\FlagAssets::register($this);
?>
<div class="change-language">
    <div class="row">
        <div class="col-md-12">
            <?php
            $changeLanguageUrlIt = \yii\helpers\Url::current(['language' => 'it-IT']);
            $labelIt = Html::img($flagAssets->baseUrl . '/img/italy.png', ['width' => 30]);
            $activeIt = '';
            $labelTxtIta = AmosEvents::t('amosevents', 'Italiano');

            $changeLanguageUrlEng = \yii\helpers\Url::current(['language' => 'en-GB']);
            $labelEng = Html::img($flagAssets->baseUrl . '/img/united-kingdom.png', ['width' => 30]);
            $activeEng = '';
            $labelTxtEng = AmosEvents::t('amosevents', 'Inglese');

            if (\Yii::$app->language == 'en-GB') {
                $activeEng = 'active';
            } else {
                $activeIt = 'active';
            }; ?>

            <strong class="mr-4"><?= AmosEvents::t('amosevents', "Seleziona lingua da tradurre") ?></strong>
            <?= Html::a($labelIt, $changeLanguageUrlIt, [
                'class' => $activeIt,
                'data-toggle' => 'tooltip',
                'title' => AmosEvents::t('amosevents', "Cambia lingua in ") . $labelTxtIta
            ]) ?>
            <?= Html::a($labelEng, $changeLanguageUrlEng, [
                'class' => $activeEng,
                'data-toggle' => 'tooltip',
                'title' => AmosEvents::t('amosevents', "Cambia lingua in ") . $labelTxtEng
            ]) ?>
        </div>
    </div>

</div>

