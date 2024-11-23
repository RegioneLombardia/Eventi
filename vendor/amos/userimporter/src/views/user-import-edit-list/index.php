<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/amos/userimporter/src/views
 */

use open20\amos\core\helpers\Html;
use open20\amos\core\views\DataProviderView;
use yii\widgets\Pjax;
use open20\amos\layout\utility\AmosIconsBi;
use amos\userimporter\Module;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var amos\userimporter\models\search\UserImportEditListSearch $model
 */

$this->title = Yii::t('amoscore', 'Crea lista');
$this->params['titleSection']  = $this->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-import-edit-list-index">
        <?php echo  $this->render('_search', ['model' => $model, 'originAction' => Yii::$app->controller->action->id]); ?>

    <?= DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
                'name',
                'surname',
//                'fiscal_code',
                'email',
                [
                    'class' => 'open20\amos\core\views\grid\ActionColumn',
                    'template' => '{update}{delete}',
                    'buttons' => [

                        'update' => function ($url, $model) {
                            return \yii\helpers\Html::a(AmosIconsBi::show('ic_edit'), $url, [
                                'title' => Yii::t('amoscore', 'Modifica'),
                                'class' => 'btn btn-xs btn-icon'
                            ], true);
                        },
                        'delete' => function ($url, $model) {
                            return \yii\helpers\Html::a(AmosIconsBi::show('ic_delete', 'danger'), $url, [
                                'title' => Yii::t('amoscore', 'Elimina'),
                                'class' => 'btn btn-xs btn-icon',
                                'data-confirm' => \Yii::t('amosuserimporter', "Sei sicuro di voler cancellare questo elemento?")
                            ], true);
                        },
                    ]
                ]
            ],
        ],
    ]); ?>
    <div class="row">
        <div class="col-md-12">
            <?= Html::a(Module::t('amosuserimporter', "Crea lista inviti"), ['/userimporter/user-import-task/create', 'create_list' => true], [
                'class' => 'btn btn-primary btn-xs float-right',
                'title' => Module::t('amosuserimporter', "Crea lista inviti")
            ]) ?>

        </div>
    </div>

</div>
