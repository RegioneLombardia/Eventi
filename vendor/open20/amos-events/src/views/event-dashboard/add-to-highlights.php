<?php
use \open20\amos\events\AmosEvents;

$this->title = AmosEvents::t('amosevents', "Aggiungi evento in evidenza");
$this->params['breadcrumbs'][] = $this->title;

 echo $this->render('_search', ['model' => $model, 'originAction' => Yii::$app->session->get('previousUrl')]);

echo open20\amos\core\views\AmosGridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'title',
        'event_type_id' => [
            'attribute' => 'event_type_id',
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
            'template' => '{add}',
            'buttons' => [
                'add' => function($url, $model){
                   return \yii\helpers\Html::a(AmosEvents::t('amosevents', "Aggiungi"),['/events/event-dashboard/add-to-highlights','event_id' => $model->id],[
                       'class' => 'btn btn-xs btn-primary',
                       'title' => AmosEvents::t('amosevents', 'Aggiungi evento in evidenza')
                   ]);
                }
            ]
        ]
    ]
]);
?>