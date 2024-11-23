<?php
/**
 * @var $dataProviderRelatedEvents
 * @var $model \open20\amos\events\models\Event
 * @var $max integer
 * @var $min integer
 */

use open20\amos\core\helpers\Html;
use open20\amos\events\AmosEvents;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use open20\amos\events\models\EventRelated;

$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);

$eventModel = $model;

$iconArrowDown = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_arrow_downward></use>";
$svgIconArrowDown = Html::tag('svg', $iconArrowDown, ['class' => 'icon']);
$spanSvgIconArrowDown = Html::tag('span', $svgIconArrowDown, ['class' => 'p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Sposta sotto'), ['class' => 'sr-only']);

$iconArrowUp = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_arrow_upward></use>";
$svgIconArrowUp = Html::tag('svg', $iconArrowUp, ['class' => 'icon']);
$spanSvgIconArrowUp = Html::tag('span', $svgIconArrowUp, ['class' => 'p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Sposta sopra'), ['class' => 'sr-only']);


$max = EventRelated::find()->andWhere(['event_id' => $model->id])->max("n_order");
$min = EventRelated::find()->andWhere(['event_id' => $model->id])->min("n_order");


echo Html::a(\Yii::t('amosevents', 'Aggiungi evento correlato'),
    ['/events/event-related/create', 'event_id' => $model->id],
    ['class' => 'btn btn-sm btn-secondary mb-3 save-all-data',
//            'data-confirm' => \Yii::t('amosevents','Stai per lasciare la pagina, assicurarsi di aver salvato dati. Proseguire?')
    ]);


echo \open20\amos\core\views\AmosGridView::widget([
    'dataProvider' => $dataProviderRelatedEvents,
    'columns' => [
        [
            'label' => 'Ordina',
            'value' => function ($model) use ($max, $min, $eventModel, $spanSvgIconArrowUp, $spanSvgIconArrowDown, $dataProviderRelatedEvents) {
                /**
                 * @var                 $dataProviderRelatedEvents \yii\data\ActiveDataProvider
                 */
                $buttons = '';
                if ($model->n_order != $min && $dataProviderRelatedEvents->totalCount > 1) {
                    $buttons .= Html::a(
                        $spanSvgIconArrowUp,
                        [
                            '/events/event-related/order-related-events',
                            'id' => $model->id,
                            'event_id' => $eventModel->id,
                            'direction' => 'up',
                        ],
                        [
                            'class' => 'btn btn-xs btn-icon',
                            'data-toggle' => 'tooltip',
                            'title' => AmosEvents::t('amosevents', 'Sposta sopra')
                        ]
                    );
                }
                if ($model->n_order != $max) {
                    $buttons .= Html::a(
                        $spanSvgIconArrowDown,
                        [
                            '/events/event-related/order-related-events', 'id' => $model->id,
                            'event_id' => $eventModel->id,
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
            'value' => function ($model) {
                return $model->n_order;
            },
            'label' => 'n.'
        ],
        [
            'label' => 'Evento correlato',
            'value' => function ($model) {
                $event = $model->eventRelated;
                if ($event) {
                    return $event->title;
                }
                return '';
            }
        ],

        'event_type_id' => [
            'attribute' => 'event_type_id',
            'label' => $model->getAttributeLabel('eventType'),
            'value' => 'eventRelated.eventType.title'
        ],
        'status' => [
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->eventRelated->getWorkflowBaseStatusLabel();
            }
        ],

        'eventRelated.begin_date_hour:datetime',
        [
            'class' => \yii\grid\ActionColumn::className(),
            'controller' => 'event-related',
            'template' => '{show}{delete}',
            'buttons' =>[
                'delete' => function($url, $model){
                    return Html::a(\open20\amos\layout\utility\AmosIconsBi::show('ic_close', 'danger'), $url, [
                        'class' => 'btn btn-xs btn-icon',
                        'data-toggle' => 'tooltip',
                        'title' => AmosEvents::t('amosevents', 'Elimina'),
                        'data-confirm' => 'Sei sicuro di eliminare la correlazione?'
                    ]);
                }
            ]
        ]
    ]

]);
