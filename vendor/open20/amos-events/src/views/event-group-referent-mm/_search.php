<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/open20/amos/events/src/views
 */

use open20\amos\core\helpers\Html;
use open20\amos\events\AmosEvents;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var open20\amos\events\models\search\EventGroupReferentMmSearch $model
 * @var yii\widgets\ActiveForm $form
 */


?>
<div class="event-search my-2 collapse" id="form-search-events">

    <?php $form = ActiveForm::begin([
        //'action' => (isset($originAction) ? [$originAction] : ['index']),
        'method' => 'get',
        'options' => [
            'class' => 'default-form'
        ]
    ]);
    ?>

    <div class="row">
        <!-- FIRST NAME -->
        <div class="col-md-6">
            <?= $form->field($model, 'firstName')->textInput(['placeholder' => AmosEvents::t('amosevents', 'Cerca per nome')]) ?>
        </div>

        <!-- LAST NAME -->
        <div class="col-md-6">
            <?= $form->field($model, 'lastName')->textInput(['placeholder' => AmosEvents::t('amosevents', 'Cerca per cognome')]) ?>
        </div>
    </div>

    <div class="row">
        <!-- EMAIL -->
        <div class="col-md-6">
            <?= $form->field($model, 'email')->textInput(['placeholder' => AmosEvents::t('amosevents', 'Cerca per email')]) ?>
        </div>

        <!-- CODICE FISCALE -->
        <div class="col-md-6">
            <?= $form->field($model, 'codiceFiscale')->textInput(['placeholder' => AmosEvents::t('amosevents', 'Cerca per codice fiscale')]) ?>
        </div>
    </div>

    <div class="row">
        <!-- ROLE -->
        <div class="col-md-6">
            <?= $form->field($model, 'ruolo')->widget(\kartik\select2\Select2::className(), [
                'data' => $model->getRoleLabel(),
                'hideSearch' => true,
                'options' => [
                    'placeholder' => AmosEvents::t('amosevents', 'Cerca per ruolo'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]) ?>
        </div>

        <!-- DG DI APPARTENENZA -->
        <!--<div class="col-md-6">
            < ?= $form->field($model, 'dgAppartenenza')->widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\open20\amos\events\models\EventGroupReferent::find()->all(), 'id', 'denominazione'),
                'hideSearch' => true,
                'options' => [
                    'placeholder' => AmosEvents::t('amosevents', 'Cerca per DG di appartenenza'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]) ?>
        </div>-->

        <div class="col-md-6">
            <?= $form->field($model, 'userStatus')->widget(\kartik\select2\Select2::className(), [
                'data' => [
                    1 => AmosEvents::t('amosevents', 'Attivo'),
                    2 => AmosEvents::t('amosevents', 'Disattivato'),
                    3 => AmosEvents::t('amosevents', 'Cancellato'),
                ],
                'hideSearch' => true,
                'options' => [
                    'placeholder' => AmosEvents::t('amosevents', 'Cerca per stato'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]) ?>
        </div>

    </div>

    <div class="col-xs-12">
        <div class="pull-right">
            <?= Html::a(Yii::t('amoscore', 'Reset'), ['index', 'group_id' => Yii::$app->request->get()['group_id']], ['class' => 'btn btn-secondary']) ?>
            <?= Html::submitButton(Yii::t('amoscore', 'Search'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>
</div>
