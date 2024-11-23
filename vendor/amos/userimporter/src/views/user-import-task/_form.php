<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @backend/views
 */

use amos\userimporter\models\UserImportTask;
use amos\userimporter\Module;
use open20\amos\attachments\components\AttachmentsInput;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

$js = <<<JS
$(document).on('click', '.btn-send-test', function(e) {
    // console.log(this);
    e.preventDefault();
    var text = $(this).attr('data-attr_text');
    var subject = $(this).attr('data-attr_subject');
    var email = $(this).attr('data-key');
    var invite = $('#event-id').val();
      $.ajax({
            url: '/userimporter/user-import-task/send-email-test?attr_text='+text+'&attr_subject='+subject+'&field_email='+email,
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
<?php
/**
 * @var View $this
 * @var UserImportTask $model
 * @var ActiveForm $form
 */


$this->title = Module::t('amosuserimporter', "Importa Utenti");
if ($isCreateList) {
    $this->title = Module::t('amosuserimporter', "Crea lista inviti");
}

$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
$module = \Yii::$app->getModule('userimporter');
$importOptin = false;
if ($module) {
    $importOptin = $module->importOptin;
}

?>
<div class="user-import-task-form">

    <?php
    $form = ActiveForm::begin([
        'options' => ['class' => 'needs-validation']
    ]);
    ?>
    <?php
    $form->errorSummary($model, ['class' => 'alert-danger alert fade in']);
    ?>

    <div class="row variable-gutters my-5">

        <div class="col-md-12">
            <div>
                <?= $form->field($model, 'name')->label(Module::t('amosevents', "Titolo dell'importazione")) ?>
            </div>
            <div>
                <?php if ($importOptin) {
                    $textSample = Module::t(
                        'amosuserimporter',
                        "Link al file di esempio. Solamente il campo Email è obbligatorio"
                    );
                } else {
                    $textSample = Module::t(
                        'amosuserimporter',
                        "#link_sample"
                    );
                } ?>

                <?php if (!$isCreateList) { ?>
                    <?=
                    $form->field($model, 'file_input')->widget(
                        AttachmentsInput::classname(),
                        [
                            'options' => [ // Options of the Kartik's FileInput widget
                                'multiple' => false, // If you want to allow multiple upload, default to false
                            ],
                            'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
                                'maxFileCount' => 1, // Client max files
                                'showRemove' => false,
                                'overwriteInitial' => false,
                            ]
                        ]
                    )->label(Module::t('amosuserimporter', '#attachments_field'))->hint(Module::t(
                        'amosuserimporter',
                        '#attachments_field_hint'
                    ))
                    ?>
                <?php } ?>
            </div>
            <br>

            <?= $this->render('../parts/_item_email_template', [
                'form' => $form,
                'model' => $model,
                'field_email' => 'email_test',
                'title' => Module::t('amosuserimporter', '<strong>Email di invito</strong>')
            ]); ?>
        </div>
        <div class="col-md-12 mt-5">
            <?= Module::t(
                'amosuserimporter',
                "#abuse"
            )
            ?>
            <?= $form->field($model, 'accept')->checkbox() ?>
        </div>
        <?php
        $module = \Yii::$app->getModule(Module::getModuleName());
        if (!empty($module->url_example_file) && !$isCreateList) {
            ?>
            <div class="col-md-12">

                <?= Html::a($textSample, $module->url_example_file) ?>

            </div>
            <?php
        }
        ?>
    </div>

    <div class="buttons d-flex">
        <?php
        $submitTitle = Module::t('amosuserimporter', 'Importa utenti');
        if ($isCreateList) {
            $submitTitle = Module::t('amosuserimporter', 'Crea lista inviti');
            echo Html::a(Module::t('amosuserimporter', 'Annulla'), '/userimporter/user-import-edit-list/index', [
                'class' => 'btn btn-secondary',
                'title' => Module::t('amosuserimporter', 'Annulla')
            ]);
        }
        echo Html::submitButton(
            $submitTitle,
            [
                'class' => 'btn btn-primary ml-auto',
                'title' => $submitTitle
            ]
        )
        ?>

    </div>
    <?php ActiveForm::end(); ?>
</div>