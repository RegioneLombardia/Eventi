<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/amos/userimporter/src/views
 */

use open20\amos\core\helpers\Html;
use yii\bootstrap4\ActiveForm;

//use open20\amos\core\forms\ActiveForm;
use kartik\datecontrol\DateControl;
use open20\amos\core\forms\Tabs;
use open20\amos\core\forms\CloseSaveButtonWidget;
use open20\amos\core\forms\RequiredFieldsTipWidget;
use yii\helpers\Url;
use open20\amos\core\forms\editors\Select;
use yii\helpers\ArrayHelper;
use open20\amos\core\icons\AmosIcons;
use yii\bootstrap\Modal;
use open20\amos\core\forms\TextEditorWidget;
use yii\helpers\Inflector;

/**
 * @var yii\web\View $this
 * @var amos\userimporter\models\UserImportEditList $model
 * @var yii\widgets\ActiveForm $form
 */

$js = <<<JS
$('#btn-save').on('click', function(e){
    e.preventDefault();
    let valid = true;
    $('.field-email-id').removeClass('has-error');
    $('.field-email-id .help-block-error').text("");
     const email = $('#email-id').val();
       $.ajax({
       url: '/userimporter/user-import-edit-list/check-email-exist',
       type: 'get',
       data: {
                'email':email
             },
       success: function (data) {
           if(data && data['exist'] == true){
               $('.field-email-id').removeClass('has-success');
               $('.field-email-id').addClass('has-error');
               $('.field-email-id .help-block-error').text(data['message']);
               valid = false;
           }else{
            $('form').submit();
           }
       }
  })

});
  
JS;

$this->registerJs($js);

?>
<div class="user-import-edit-list-form col-xs-12 nop">

    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'user-import-edit-list_' . ((isset($fid)) ? $fid : 0),
            'data-fid' => (isset($fid)) ? $fid : 0,
            'data-field' => ((isset($dataField)) ? $dataField : ''),
            'data-entity' => ((isset($dataEntity)) ? $dataEntity : ''),
            'class' => ((isset($class)) ? $class : '')
        ]
    ]);
    ?>
    <?php // $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>

    <div class="row">
        <div class="col-xs-12">
            <div class="col-md-8 col xs-12"><!-- name string -->
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?><!-- surname string -->
                <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?><!-- email string -->
                <?= $form->field($model, 'email')
                    ->textInput([
                        'id' => 'email-id',
                        'maxlength' => true
                    ]) ?>

                <?= RequiredFieldsTipWidget::widget(); ?>

                <div class="col-md-12">
                    <?= Html::a(\Yii::t('amosuserimporter', 'Annulla'),'/userimporter/user-import-edit-list', [
                        'title' => \Yii::t('amosuserimporter', 'Annulla'),
                        'class' => 'btn btn-secondary'
                    ])?>

                    <?= Html::submitButton(\Yii::t('amosuserimporter', 'Salva'), [
                        'class' => 'btn btn-primary',
                        'title' => \Yii::t('amosuserimporter', 'Salva')
                    ])
                    ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-md-4 col xs-12"></div>
        </div>
        <div class="clearfix"></div>

    </div>
</div>
