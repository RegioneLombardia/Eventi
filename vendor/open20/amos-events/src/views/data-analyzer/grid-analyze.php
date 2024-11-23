<?php

use open20\amos\events\AmosEvents;

$js = <<<JS
    $('.pagination li a').on('click', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        $('form').attr('action', href);
        $('form button[type=submit]').trigger('click');
    });
JS;
$this->registerJs($js);

?>
<?php
if ($actionType == 'analyze-event-types') { ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'title',
                'label' => AmosEvents::t('amosevents', "Title")
            ],
            [
                'attribute' => 'count',
                'label' => AmosEvents::t('amosevents', "Eventi")
            ],
            [
                'value' => function ($model) use ($tot) {
                    return round(($model['count'] * 100) / $tot);
                },
                'label' => AmosEvents::t('amosevents', "%")
            ]
        ]
    ]) ?>
<?php } else if ($actionType == 'analyze-event-dg') { ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'denominazione',
                'label' => AmosEvents::t('amosevents', "Direzione generale")
            ],
            [
                'attribute' => 'count',
                'label' => AmosEvents::t('amosevents', "Eventi")
            ],
            [
                'value' => function ($model) use ($tot) {
                    return round(($model['count'] * 100) / $tot);
                },
                'label' => AmosEvents::t('amosevents', "%")
            ]
        ]
    ]) ?>
<?php } else if ($actionType == 'analyze-event-delegations') { ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'denominazione',
                'label' => AmosEvents::t('amosevents', "Direzione generale")
            ],
            [
                'attribute' => 'count',
                'label' => AmosEvents::t('amosevents', "Eventi delegati")
            ],
            [
                'value' => function ($model) use ($tot) {
                    return round(($model['count'] * 100) / $tot);
                },
                'label' => AmosEvents::t('amosevents', "%")
            ]
        ]
    ]) ?>
<?php } else if ($actionType == 'analyze-event-preference-tags') { ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'nome',
                'label' => AmosEvents::t('amosevents', "Tag evento informativo")
            ],
            [
                'attribute' => 'count',
                'label' => AmosEvents::t('amosevents', "Eventi")
            ],
//            [
//                'value' => function ($model) use ($tot) {
//                    return round(($model['count'] * 100) / $tot);
//                },
//                'label' => AmosEvents::t('amosevents', "%")
//            ]
        ]
    ]) ?>
<?php } else if ($actionType == 'analyze-event-participants') { ?>
    <?php
    $totEventi = 0;
    foreach ($resultTotEventi['result'] as $resElem) {
        $totEventi += $resElem['count'];
    } ?>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'title',
                'label' => AmosEvents::t('amosevents', "Tipologia evento")
            ],
            [
                'value' => function ($model) use ($resultTotEventi, $totEventi) {
                    if (!empty($resultTotEventi)) {
                        foreach ($resultTotEventi['result'] as $res) {
                            if ($res['title'] == $model['title']) {
                                return $res['count'];
                            }
                        }
                    }

                    if ($model['title'] == 'Totale') {
                        return $totEventi;
                    }
                    return '0';
                },
                'label' => AmosEvents::t('amosevents', "Eventi")
            ], [
                'attribute' => 'tot_reg',
                'label' => AmosEvents::t('amosevents', "Partecipanti")
            ],
            [
                'attribute' => 'tot_inv',
                'label' => AmosEvents::t('amosevents', "Invitati")
            ],
//            [
//                'value' => function ($model) use ($tot) {
//                    return round(($model['count'] * 100) / $tot);
//                },
//                'label' => AmosEvents::t('amosevents', "%")
//            ]
        ]
    ]) ?>

<?php } else if ($actionType == 'analyze-user-access') { ?>
    <?php if ($model->access_type != 'all') {
        $isMonthly = $model->isMonthly; ?>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'fulldate',
                    'label' => AmosEvents::t('amosevents', "Data"),
                    'value' => function ($model) use ($isMonthly) {
                        if ($model['fulldate'] == 'tot') {
                            return 'Totale';
                        }
                        if ($isMonthly) {
                            return $model['day'] . '/' . $model['month'];
                        }
                        return $model['fulldate'];
                    }
                ],
                [
                    'attribute' => 'count',
                    'label' => AmosEvents::t('amosevents', "Accessi")
                ],

                [
                    'value' => function ($model) use ($tot) {
                        return round(($model['count'] * 100) / $tot);
                    },
                    'label' => AmosEvents::t('amosevents', "%")
                ]
            ]
        ]) ?>
    <?php } else { ?>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'access_method',
                    'value' => function ($model) {
                        if ($model['access_method'] == 'tot') {
                            return 'Totale';
                        }
                        return open20\amos\admin\models\UserAccessLog::getAccessTypeLabels()[$model['access_method']];
                    },
                    'label' => AmosEvents::t('amosevents', "Tipologia accesso")
                ],
                [
                    'attribute' => 'count',
                    'label' => AmosEvents::t('amosevents', "Accessi")
                ],

                [
                    'value' => function ($model) use ($tot) {
                        return round(($model['count'] * 100) / $tot);
                    },
                    'label' => AmosEvents::t('amosevents', "%")
                ]
            ]
        ]) ?>
    <?php } ?>

<?php } else if ($actionType == 'registration-users') { ?>
    <?php if ($model->event_group_referent_id != 'all') {
        $isMonthly = $model->isMonthly; ?>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'fulldate',
                    'label' => AmosEvents::t('amosevents', "Data"),
                    'value' => function ($model) use ($isMonthly) {
                        if ($model['fulldate'] == 'tot') {
                            return 'Totale';
                        }
                        if ($isMonthly) {
                            return $model['day'] . '/' . $model['month'];
                        }
                        return $model['fulldate'];
                    }
                ],
                [
                    'attribute' => 'count',
                    'label' => AmosEvents::t('amosevents', "Utenti registrati")
                ],

                [
                    'value' => function ($model) use ($tot) {
                        return round(($model['count'] * 100) / $tot);
                    },
                    'label' => AmosEvents::t('amosevents', "%")
                ]
            ]
        ]) ?>
    <?php } else { ?>
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'event_group_referent_id',
                    'value' => function ($model) {
                        if (empty($model['dg'])) {
                            return AmosEvents::t('amosevents', "Nessuna");
                        }
                        return $model['dg'];
                    },
                    'label' => AmosEvents::t('amosevents', "Direzione generale")
                ],
                [
                    'attribute' => 'count',
                    'label' => AmosEvents::t('amosevents', "Utenti registrati")
                ],
                [
                    'value' => function ($model) use ($tot) {
                        return round(($model['count'] * 100) / $tot);
                    },
                    'label' => AmosEvents::t('amosevents', "%")
                ]
            ]
        ]) ?>
    <?php } ?>
<?php } ?>


