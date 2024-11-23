<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/amos/userimporter/src/views
 */

use open20\amos\core\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var amos\userimporter\models\search\UserImportEditListSearch $model
 * @var yii\widgets\ActiveForm $form
 */


?>
<div class="user-import-edit-list-search my-2 collapse" id="form-search-events">
    <?php $form = ActiveForm::begin([
        'action' => (isset($originAction) ? [$originAction] : ['index']),
        'method' => 'get',
        'options' => [
            'class' => 'default-form'
        ]
    ]);
    ?>

    <div class="neutral-2-bg p-4">
        <div class="row">
            <!-- id --> <?php // echo $form->field($model, 'id') ?>

            <!-- name -->
            <div class="col-md-4"> <?=
                $form->field($model, 'name')->textInput() ?>

            </div>

            <!-- surname -->
            <div class="col-md-4"> <?=
                $form->field($model, 'surname')->textInput() ?>

            </div>

            <!-- email -->
            <div class="col-md-4"> <?=
                $form->field($model, 'email')->textInput() ?>

            </div>

            <div class="col-md-12">
                <div class="float-right">
                    <?= Html::a(
                        \Yii::t('amoscore', 'Reset'),
                        [Yii::$app->controller->action->id, 'currentView' => Yii::$app->request->getQueryParam('currentView')],
                        ['class' => 'btn btn-xs']
                    ) ?>
                    <?= Html::submitButton(\Yii::t('amoscore', 'Search'), [
                        'class' => 'btn btn-xs btn-outline-primary'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>




        <div class="clearfix"></div>

        <?php ActiveForm::end(); ?>
    </div>
