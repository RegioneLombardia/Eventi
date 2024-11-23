<?php
use open20\amos\core\forms\ActiveForm;
/**
*
 **/
?>
        <?php
        //$newEmail = new \open20\amos\groups\models\GroupsMailer();
        $form = ActiveForm::begin([
            'action' => 'send-email-to-group',
            'method' => 'post'
        ]);

        echo $form->field($model, 'idGroup')->hiddenInput()->label(false);
        echo $form->field($model, 'subject');
        echo $form->field($model, 'text')->widget(\open20\amos\core\forms\TextEditorWidget::className(),[
            ]);

echo \open20\amos\core\helpers\Html::submitButton('Send',['class' => 'btn btn-primary']);

        ActiveForm::end();
        ?>

