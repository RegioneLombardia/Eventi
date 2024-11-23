<?php

use open20\amos\events\AmosEvents;
use open20\amos\layout\utility\AmosIconsBi;
use yii\helpers\Html;

$this->title = AmosEvents::t('amosevents', "Eventi figli di '{event}'", [
    'event' => $model->title
]);

$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">

    <div class="col-md-12">
        <h3><?= AmosEvents::t('amosevents', "Eventi figli") ?></h3>
        <?php
        echo \yii\grid\GridView::widget([
            'dataProvider' => $dataProviderChildren,
            'columns' => [
                'id',
                'title',
                'eventType.title',
                [
                    'attribute' => 'begin_date_hour',
                    'format' => 'datetime'
                ],
                [
                    'attribute' => 'end_date_hour',
                    'format' => 'datetime'
                ],
                'status' => [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->getWorkflowBaseStatusLabel();
                    }
                ],
                [
                    'class' => 'open20\amos\core\views\grid\ActionColumn',
                    'template' => '{community}{view}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('Visualizza', ['/events/event-dashboard/view', 'id' => $model->id], [
                                'class' => 'btn btn-secondary btn-xs ml-1',
                                'title' => AmosEvents::t('amosevents', "Gestisci evento")
                            ]);
                        },
                        'community' => function ($url, $model) {
                            return \open20\amos\events\utility\EventsUtility::getButtonCommunity($model);
                        }
                    ]
                ]
            ]
        ])
        ?>
    </div>

</div>

<div class="row">

    <div class="col-md-12">
        <h3><?= AmosEvents::t('amosevents', "Fasce orarie") ?></h3>
        <?php
        echo \kartik\grid\GridView::widget([
            'dataProvider' => $dataProviderTimePeriods,
            'columns' => [
//                'id',
                [
                    'attribute' => 'title',
                    'group' => true
                ],
                [
                    'attribute' => 'begin_date_hour',
                    'value' => function ($model) {
                        if (!empty($model->begin_date_hour)) {
                            $dateTime = new DateTime($model->begin_date_hour);
                            return $dateTime->format('d/m/Y');
                        }
                    },
                    'label' => AmosEvents::t('amosevents', "Data inizio"),
                    'group' => true

                ],
                [
                    'attribute' => 'begin_date_hour',
                    'value' => function ($model) {
                        if (!empty($model->begin_date_hour)) {
                            $dateTime = new DateTime($model->begin_date_hour);
                            return $dateTime->format('H:i');
                        }
                    },
                    'label' => AmosEvents::t('amosevents', "Ore inizio")
                ],
                [
                    'attribute' => 'begin_date_hour',
                    'value' => function ($model) {
                        if (!empty($model->end_date_hour)) {
                            $dateTime = new DateTime($model->end_date_hour);
                            return $dateTime->format('d/m/Y');
                        }
                    },
                    'label' => AmosEvents::t('amosevents', "Data fine")
                ],
                [
                    'attribute' => 'begin_date_hour',
                    'value' => function ($model) {
                        if (!empty($model->end_date_hour)) {
                            $dateTime = new DateTime($model->end_date_hour);
                            return $dateTime->format('H:i');
                        }
                    },
                    'label' => AmosEvents::t('amosevents', "Ore fine")
                ],
                [
                    'attribute' => 'end_date_hour',
                    'format' => 'datetime'
                ],
                'status' => [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->getWorkflowBaseStatusLabel();
                    }
                ],
                [
                    'class' => 'open20\amos\core\views\grid\ActionColumn',
                    'template' => '{community}{view}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('Visualizza', ['/events/event-dashboard/view', 'id' => $model->id], [
                                'class' => 'btn btn-secondary btn-xs ml-1',
                                'title' => AmosEvents::t('amosevents', "Gestisci evento")
                            ]);
                        },
                        'community' => function ($url, $model) {
                            return \open20\amos\events\utility\EventsUtility::getButtonCommunity($model);
                        }
                    ]
                ]
            ]
        ])
        ?>
    </div>

</div>

