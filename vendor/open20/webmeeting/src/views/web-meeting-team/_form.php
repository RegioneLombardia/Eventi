<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting View
 */
use open20\webmeeting\WebMeetingModule;

use open20\amos\core\forms\ActiveForm;
use open20\amos\core\forms\CloseSaveButtonWidget;

use yii\helpers\Html;
use kartik\datecontrol\DateControl;
use kartik\widgets\SwitchInput;
use kartik\widgets\Select2;

$form = ActiveForm::begin([
    'options' => [
        'id' => 'webmeeting-team',
        'data-fid' => (isset($fid)) ? $fid : 0,
        'data-field' => ((isset($dataField)) ? $dataField : ''),
        'data-entity' => ((isset($dataEntity)) ? $dataEntity : ''),
        'class' => ((isset($class)) ? $class : '')
    ]
]);
?>

<div class="row">
    <div class="webmeeting-form col-xs-12">
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="form-group">
    <div class="col-xs-12 note_asterisk nop">
    <?= WebMeetingModule::_t('#required_fields', [
        '0' => '<span class="red">*</span>'
    ])
    ?>
    </div>

    <?= CloseSaveButtonWidget::widget([
        'model' => $model,
        'urlClose' => WebMeetingModule::getMyIndexLink('-team'),
        'closeButtonLabel' => WebMeetingModule::_t('#close')
    ]);
    ?>

</div>

<?php ActiveForm::end(); ?>