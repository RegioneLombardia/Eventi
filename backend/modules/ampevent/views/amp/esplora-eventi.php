<?php

/**
 * @var $dataProvider
 */

use yii\widgets\ListView;
use \open20\amos\events\models\EventLanding;
use open20\amos\core\helpers\Html;
use open20\amos\events\utility\EventsUtility;

$this->title = \Yii::t('amosevents', "Esplora eventi");

?>
<div class="amp-esplora-eventi" style="padding-bottom:134px">
    <div class="hero-banner">
        <div class="img-bg">
            <amp-img src="/amp-resources/img/bg-video.png" width="1.33" height="1" layout="fill" alt="Palazzo Lombardia" class="img-sfondo"></amp-img>
        </div>
        <div class="uk-container">
            <div class="content-hero-title">
                <h1><?= \Yii::t('app', "Scopri gli eventi di Regione Lombardia") ?></h1>
            </div>
        </div>

    </div>



    <?php
    $dataProvider->pagination->pageSize = 10;
    $dataProvider->pagination->route = '/esplora-eventi/amp';
    $dataProvider->pagination->pageParam = 'event-page';
    $dataProvider->pagination->page = \Yii::$app->request->get('event-page') - 1;

    echo $this->render('parts/today-in-streaming', ['dataProviderStreaming' => $dataProviderStreaming]);
    ?>
    <div id="coming-next">
        <div class="uk-container">


            <div id="event-coming-next-container" class="container-elenco-eventi">
                <?php
                $templates = \open20\amos\events\utility\EventsUtility::getCmsTemplates();

                echo ListView::widget([
                    'summary' => false,
                    'dataProvider' => $dataProvider,
                    'itemView' => 'parts/_item_event',
                    'viewParams' => ['templates' => $templates],
                    'options' => [
                        'class' => 'lista-eventi',
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
</div>