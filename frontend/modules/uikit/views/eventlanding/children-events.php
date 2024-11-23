<?php
/**
 * @var $model \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

use yii\widgets\ListView;
use open20\amos\layout\assets\IsotopeAsset;


?>
<div id="listEvents-container">

    <div class="uk-section prossimi-eventi">
        <div class="uk-container">
            <div class="<?= '' ?>">

                <div class="clearfix"></div>

                <div class="slider-gruppo-eventi qui">

                    <div uk-slider="sets: true; finite: true">
                        <div class="uk-flex uk-flex-middle uk-margin-large-bottom">
                            <div class="tit-prox-eventi">
                                <span class="mdi mdi-calendar-multiselect"></span>
                                <?= \Yii::t('site', "Lista eventi")?>
                            </div>
                            <div class="uk-slidenav-container  uk-margin-auto-left">
                                <a href="#" uk-slidenav-previous uk-slider-item="previous" title="<?= \Yii::t('site', "Indietro")?>"></a>
                                <a href="#" uk-slidenav-next uk-slider-item="next" title="<?= \Yii::t('site', "Avanti")?>"></a>
                            </div>
                        </div>


                        <?php

                        echo ListView::widget([
                            'summary' => false,
                            'dataProvider' => $dataProvider,
                            'itemView' => '_item-event',
                        ]);

                        ?>
                        <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
