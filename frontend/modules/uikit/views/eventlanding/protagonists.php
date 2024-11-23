<?php
/**
 * @var $model \open20\amos\events\models\Event
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

use yii\widgets\ListView;

$dataProvider->pagination= false;
?>

<div id="layoutsection-75b" class="section-protagonisti uk-section-default uk-visible@xl" style="display:block !important">
    <div style="background-image: url('/img/palazzo-regione.jpg');" class="uk-background-norepeat uk-section">

        <div class="uk-container">
            <div class="uk-grid-margin uk-grid uk-grid-stack" uk-grid="">
                <div id="f02" class="uk-width-1-1 uk-first-column">
                    <h2 id="headline-5dc"><?= $model->getMatchedAttribute('title_protagonisti') ?></h2>
                    <div id="text-97b" class="subtitle-section uk-margin">
                        <?= $model->getMatchedAttribute('description_protagonists') ?>
                    </div>

                    <div id="listProtagonist-container">
                        <div class="clearfix"></div>

                        <?php echo ListView::widget([
                            'dataProvider' => $dataProvider,
                            'itemView' => '@frontend/modules/uikit/views/eventlanding/items/_item-protagonist',

                            'options' => [
                                'uk-grid' => 'masonry: true',
                                'class' => 'list-view uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l',
                            ],
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
