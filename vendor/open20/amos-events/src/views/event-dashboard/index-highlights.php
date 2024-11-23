<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events\views\event
 * @category   CategoryName
 */

use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\utilities\ModalUtility;
use open20\amos\core\views\DataProviderView;
use open20\amos\events\AmosEvents;
use open20\amos\layout\utility\AmosIconsBi;


use yii\web\View;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\events\models\search\EventSearch $model
 * @var string $currentView
 */


/** @var AmosEvents $eventsModule */
$eventsModule = Yii::$app->getModule(AmosEvents::getModuleName());
$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;
$wizardAsset = \open20\amos\events\assets\WizardEventAsset::register($this);

?>

<div class="event-index mt-3">
    <?php

//    echo $this->render('_search', ['model' => $model, 'originAction' => Yii::$app->session->get('previousUrl')]);
    //    echo $this->render('_order', ['model' => $model,'originAction' => Yii::$app->session->get('previousUrl')]);
    //$dataProvider->pagination->pageSize = 5;
    $max = \open20\amos\events\models\EventHighlights::find()->max("n_order");
    $min = \open20\amos\events\models\EventHighlights::find()->min("n_order");

    echo open20\amos\core\views\AmosGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => 'Ordina',
                'value' => function ($model) use ($max, $min) {


                    $buttons = '';
                    if ($model->eventHighlights->n_order != $min) {
                        $buttons .= Html::a(
                            AmosIconsBi::show('ic_arrow_upward'),
                            [
                                '/events/event-dashboard/order-highlights',
                                'id' => $model->eventHighlights->id,
                                'direction' => 'up',
                            ],
                            [
                                'class' => 'btn btn-xs btn-icon',
                                'data-toggle' => 'tooltip',
                                'title' => AmosEvents::t('amosevents', 'Sposta sopra')
                            ]
                        );
                    }
                    if ($model->eventHighlights->n_order != $max) {
                        $buttons .= Html::a(
                            AmosIconsBi::show('ic_arrow_downward'),
                            [
                                '/events/event-dashboard/order-highlights',
                                'id' => $model->eventHighlights->id,
                                'direction' => 'down',
                            ],
                            [
                                'class' => 'btn btn-xs btn-icon',
                                'data-toggle' => 'tooltip',
                                'title' => AmosEvents::t('amosevents', 'Sposta sotto')
                            ]
                        );
                    }
                    return $buttons;
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'n_order',
                'value' => function ($model) {
                    return $model->eventHighlights->n_order;
                },
                'label' => 'n.'
            ],
            'title',
            'event_type_id' => [
                'attribute' => 'event_type_id',
                'label' => $model->getAttributeLabel('eventType'),
                'value' => 'eventType.title'
            ],
            'status' => [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getWorkflowBaseStatusLabel();
                }
            ],
            'begin_date_hour:datetime',
            [
                'class' => \yii\grid\ActionColumn::className(),
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function($url, $model){
                        return \yii\helpers\Html::a(AmosIconsBi::show('ic_close', 'danger'),['/events/event-dashboard/remove-highlights','id' => $model->eventHighlights->id],[
                            'title' => AmosEvents::t('amosevents', 'Rimuovi evento in evidenza'),
                            'class' => 'btn btn-xs btn-icon'
                        ]);
                    }
                ]
            ]
        ]
    ]);
    ?>
</div>