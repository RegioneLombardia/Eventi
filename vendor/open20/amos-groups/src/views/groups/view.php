<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\datecontrol\DateControl;
use yii\helpers\Url;

/**
* @var yii\web\View $this
* @var \open20\amos\groups\models\Groups $model
*/

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('amosgroups', 'Group'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="groups-view col-xs-12">

    <?= DetailView::widget([
    'model' => $model,    
    'attributes' => [
//                'id',
//            'parent_id',
            'name',
            'description',
            [
                'attribute'=>'created_at',
                'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],                
            ],
    ],    
    ]) ?>

    <?php
    echo \open20\amos\core\views\AmosGridView::widget([
        'dataProvider' => $dataProviderSelected,
        'id' => 'selected-members',
        'columns' => [
            'nome',
            'cognome',
            'codice_fiscale',
            [
                'attribute' => 'user.email',
                'label' => 'Email'
            ],
            [
                'class' => 'open20\amos\core\views\grid\ActionColumn',
                'template' => '{view-profile}',
                'buttons' => [
                    'view-profile' => function ($url, $model) {
                        $urlDestinazione = Yii::$app->urlManager->createUrl(['/admin/user-profile/view', 'id' => $model->id]);
                        return \yii\helpers\Html::a(\open20\amos\core\icons\AmosIcons::show('file', ['data-idrow' => $model->id]), $urlDestinazione, [
                            'title' => Yii::t('app', 'Detail profile'),
                            'model' => $model,
                            'class' => 'btn btn-tool-secondary',
                        ]);
                    },
                ]
            ],
        ],
    ]);
    ?>

    <div class="btnViewContainer pull-right">
        <?= Html::a(Yii::t('amoscore', 'Chiudi'), Url::previous(), ['class' => 'btn btn-secondary']); ?>    </div>

</div>
