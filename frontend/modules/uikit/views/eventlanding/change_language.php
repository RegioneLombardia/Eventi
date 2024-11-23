<?php

use yii\helpers\Html;
use open20\amos\events\AmosEvents;

$flagAssets = \open20\amos\events\assets\FlagAssets::register($this);

if ($model) {
    if ($model->multilanguage) {
        ?>
        <div class="dropdwon-change-language">

            <?php
            if(empty($currentUrl)) {
                $preview = false;
                if (strpos(\Yii::$app->request->url, 'api/1/preview-event') != false) {
                    $preview = true;
                    $currentUrl = \Yii::$app->request->url;
                } else {
                    $currentUrl = \Yii::$app->menu->current;
                }
            }
            $currentUrl = str_replace('/it/', '/', $currentUrl);

            $changeLanguageUrl = \Yii::$app->urlManager->createUrl([$currentUrl, 'language' => 'it-IT']);
            if($preview){ $changeLanguageUrl= \Yii::$app->urlManager->createUrl([$currentUrl]).'&language=it-IT';}
            $labelIta = Html::img($flagAssets->baseUrl . '/img/italy.png', ['width' => 30]) . ' ' . AmosEvents::t('amosevents', "Evento in Italiano");
            $labelTxt = AmosEvents::t('amosevents', 'Italiano');
            $linkIta = Html::a($labelIta, $changeLanguageUrl, ['class' => 'ml-4', 'title' => AmosEvents::t('amosevents', "Cambia lingua in ") . $labelTxt]);
            //            $linkIta2 = Html::a($label, $changeLanguageUrl, ['class' => 'ml-4', 'title' => AmosEvents::t('amosevents', "Cambia lingua in ") . $labelTxt]);

            $changeLanguageUrl = \Yii::$app->urlManager->createUrl([$currentUrl, 'language' => 'en-GB']);;
            if($preview){ $changeLanguageUrl= \Yii::$app->urlManager->createUrl([$currentUrl]).'&language=en-GB';}
            $labelEng = Html::img($flagAssets->baseUrl . '/img/united-kingdom.png', ['width' => 30]) . ' ' . AmosEvents::t('amosevents', "English Event");
            $labelTxt = AmosEvents::t('amosevents', 'Inglese');
            $linkEng = Html::a($labelEng, $changeLanguageUrl, ['class' => 'ml-4', 'title' => AmosEvents::t('amosevents', "Cambia lingua in ") . $labelTxt]);
            //            $linkEng = Html::a($label, $changeLanguageUrl, ['class' => 'ml-4', 'title' => AmosEvents::t('amosevents', "Cambia lingua in ") . $labelTxt]);

            if (\Yii::$app->language == 'en-GB') {
                $current = $labelEng;
            } else {
                $current = $labelIta;
            }; ?>

            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        title="<?= AmosEvents::t('amosevents', "Cambia lingua") ?>">
                    <?= $current ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                    <li><?= $linkIta ?></li>
                    <li><?= $linkEng ?></li>
                </ul>

            </div>


        </div>
    <?php }
} ?>
