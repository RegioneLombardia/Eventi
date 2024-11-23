<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @backend/views
 */

use open20\amos\core\helpers\Html;
use open20\amos\core\forms\ActiveForm;
use kartik\datecontrol\DateControl;
use open20\amos\core\forms\Tabs;
use open20\amos\core\forms\CloseSaveButtonWidget;
use open20\amos\core\forms\RequiredFieldsTipWidget;
use yii\helpers\Url;
use open20\amos\core\forms\editors\Select;
use yii\helpers\ArrayHelper;
use open20\amos\core\icons\AmosIcons;
use yii\bootstrap\Modal;
use yii\redactor\widgets\Redactor;
use yii\helpers\Inflector;
use open20\amos\groups\Module;

/**
 * @var yii\web\View $this
 * @var open20\amos\groups\models\GroupsComunication $model
 * @var yii\widgets\ActiveForm $form
 */

$js = <<<JS
$(document).on('click', '#btn-send', function(e) {
    // console.log(this);
    e.preventDefault();
    var text = $('text-id').val();
    var subject = $('subject-id').val();
      $.ajax({
            url: '/groups/groups-comunication/send-email-test',
            data: $('form').serialize(),
            method: 'post',
            success: function(data){
                alert('messaggio spedito a '+ data)
            }
      });
});


  
JS;
$this->registerJs($js);
?>

<div class="groups-comunication-form col-xs-12 nop">

    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'groups-comunication_' . ((isset($fid)) ? $fid : 0),
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

            <h3><strong><?= \open20\amos\groups\Module::t('amosgroups', "Group") ?></strong>: <?= $group->name ?>
            </h3>
            <?= $form->field($model, 'groups_id')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'subject')->textInput([
                'id' => 'subject-id'
            ]); ?>

            <?= $form->field($model, 'text')->widget(\open20\amos\core\forms\TextEditorWidget::className(), [
                'options' => [
                    'id' => 'text-id'
                ],
                'clientOptions' => [
                    'toolbar' => "fullscreen | undo redo code | styleselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media insertdatetime | removeformat",
                ]
            ]);
            ?>

            <?= $form->field($model, 'email')->textInput(['placeholder' => Module::t('amospodcast', 'Inserisci la tua email per un invio di prova...')])
                ->label(Module::t('amospodcast', "Per vedere in anteprima l'aspetto della comunicazione, inserisci la tua email ed effettua un invio di prova")) ?>
            <?= Html::a(Module::t('amospodcast', 'Invio di prova'), '', [
                'id' => 'btn-send',
                'class' => 'btn btn-sm btn-secondary btn-send-test',
//                'data-key' => $field_email,
//                'data-attr_text' => $attribute_text,
//                'data-attr_subject' => $attribute_subject,
            ]) ?>

            <?= CloseSaveButtonWidget::widget(['model' => $model, 'urlClose' => '/groups/groups-comunication/index?idGroup=' . $group->id]); ?>

            <?php ActiveForm::end(); ?>

        </div>
        <div class="clearfix"></div>

    </div>
</div>
