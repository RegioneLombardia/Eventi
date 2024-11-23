<?php

use open20\amos\core\forms\ActiveForm;

$asterisk = '<span class="text-danger">*</span>';
$js = <<<JS
           $([document.documentElement, document.body]).animate({
        scrollTop: $("#id-form-contact-sale").offset().top - 125
    }, 1000);

JS;
if(!empty($messageOk)){
    $this->registerJs($js);
}

?>

<div id="id-form-contact-sale" class="form-contact-sale">
    <?php

    if (!empty($messageOk)) { ?>
        <h2><?= $messageOk ?></h2>
    <?php } else {
        $form = ActiveForm::begin();
        ?>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'name')
                    ->label(\Yii::t('app', 'Nome') . $asterisk)
                ?>
            </div>
            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'surname')
                    ->label(\Yii::t('app', 'Cognome') . $asterisk)
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'email')
                    ->label(\Yii::t('app', 'Email') . $asterisk)
                ?>
            </div>
            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'phone')
                    ->label(\Yii::t('app', 'Telefono') . $asterisk)
                ?>
            </div>
        </div>
        <div style="display:none">
            <?php $model->sala = \Yii::$app->menu->current->title?>
            <?= $form->field($model, 'sala') ?>
        </div>
        <?php ?>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'type_event')->widget(\kartik\select2\Select2::className(), [
                    'data' => \yii\helpers\ArrayHelper::map(\open20\amos\events\models\EventType::find()->all(), 'title', 'title'),
                    'options' => ['placeholder' => 'Seleziona...']
                ])->label(\Yii::t('app', 'Tipo di evento') . $asterisk) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <?= $form->field($model, 'body')->textarea(['rows' => 4])
                    ->label(\Yii::t('app', 'Richiesta estesa') . $asterisk) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <?= \yii\helpers\Html::submitButton(\Yii::t('app', 'Invia richiesta'), [
                    'class' => 'btn btn-black uk-text-uppercase',
                    'data-confirm' => \Yii::t('app', 'Sei sicuro di inviare la richiesta?')
                ]) ?>
            </div>
        </div>

        <div style="display:none">
            <?php  echo $form->field($model, 'emailToSend')->hiddenInput()->label(false) ?>
        </div>


        <?php
        ActiveForm::end();
    } ?>
</div>

