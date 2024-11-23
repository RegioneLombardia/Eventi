<?php
/**
 * @var $model \amos\userimporter\models\UserImportTask
 */
use yii\helpers\Html;
use amos\userimporter\Module;
?>


<?php
$variables = \amos\userimporter\utility\UserImporterUtility::getVariablesEmail();
$variablesJson = \yii\helpers\Json::encode($variables);

$tinyMCECallback = <<< JS
    function (editor) {

        let usersList = $variablesJson;
        let options = [];

        editor.on('change', function () {
                tinymce.triggerSave();
        });
        
        //iterate the user array and create the options with text and 
        //onclick event to insert the content on click to the editor

        $.each(usersList, function(label, mapping) {
            options.push({
                text: label, 
                onclick: function() { 
                    editor.insertContent(label); 
                }
            });
        });

        //add the dropdown button to the editor 
        editor.addButton('keyvalues', {
            type: 'menubutton',
            text: 'Variabili',
            icon: false,
            menu: options
        });
    }
JS;

?>
<div>
    <h5><?= $title ?></h5>
</div>

<div>
    <?php  if(empty($model->email_subject)){
        $model->email_subject = $this->render('../email/subject');
    }
?>
    <?= $form->field($model, 'email_subject')->label(Module::t('amosuserimporter','Oggetto email')); ?>
</div>

<div>
    <?php
    if(empty($model->email_text)){
        $model->email_text = $this->render('../email/text');
}?>
    <?=
    $form->field($model, 'email_text')->widget(\open20\amos\core\forms\TextEditorWidget::className(),[
            'clientOptions' =>         [
                    'toolbar' => \open20\amos\events\utility\EventsUtility::getToolbarTextEditor(),
//                'toolbar' => "fullscreen | undo redo code | styleselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media insertdatetime | removeformat | keyvalues",
                'setup' => new \yii\web\JsExpression($tinyMCECallback),
                ]
    ])
        ->label(Module::t('amosuserimporter','Testo email'))
    ?>
</div>

<div>
    <?= $form->field($model, 'email_test')->textInput(['placeholder' =>  Module::t('amosuserimporter', 'Inserisci la tua email per un invio di prova...')])
        ->label(Module::t('amosuserimporter', "Per vedere in anteprima l'aspetto della comunicazione, inserisci la tua email ed effettua un invio di prova")) ?>
</div>

<div>
    <?= Html::a(Module::t('amosuserimporter', 'Invio di prova'), '', [
        'id' => 'btn-send-email-email_test',
        'class' => 'btn btn-sm btn-secondary btn-send-test',
        'data-key' => 'email_test',
        'data-attr_text' => 'email_text',
        'data-attr_subject' => 'email_subject',
    ]) ?>
</div>