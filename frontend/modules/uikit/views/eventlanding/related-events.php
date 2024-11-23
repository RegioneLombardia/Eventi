<?php
/**
 * @var $model \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 * @var $dataProvider \yii\data\ActiveDataProvider
 */


use yii\widgets\ListView;
use open20\amos\layout\assets\IsotopeAsset;


?>
<div id="layoutsection-310" class="section-related-events uk-section- uk-visible@xl uk-section"
     style="display:block !important">

    <div class="uk-container">
        <h2 id="headline-f9e"><?= $model->getMatchedAttribute('title_related_events') ?></h2>
        <div id="listRelatedEvents-container">
            <div>
                <div class="uk-container">
                    <div class="<?= $cssClass ?>">
                        <div class="clearfix"></div>
                        <div class="slider-gruppo-eventi">
                            <div uk-slider>
                                <div class="uk-flex uk-flex-middle uk-margin-bottom">
                                    <div class="uk-slidenav-container uk-margin-auto-left">
                                        <a href="#" uk-slidenav-previous uk-slider-item="previous" title="<?= \Yii::t('site', "Indietro")?>"></a>
                                        <a href="#" uk-slidenav-next uk-slider-item="next" title="<?= \Yii::t('site', "Avanti")?>"></a>
                                    </div>
                                </div>


                                <?php

                                echo ListView::widget([
                                    'summary' => false,
                                    'dataProvider' => $dataProvider,
                                    'itemView' => '@frontend/modules/uikit/views/eventlanding/items/_item_related_events',
                                    'options' => [
                                        'class' => 'uk-slider-items uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-4@m uk-grid',
                                    ]
                                ]);

                                ?>
                                <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
