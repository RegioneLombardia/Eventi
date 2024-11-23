<?php
$this->title = \Yii::t('app', "Accettazione Privacy");


use yii\bootstrap4\ActiveForm;
use open20\amos\events\utility\EventsUtility;
use open20\amos\admin\AmosAdmin;

$js = <<<JS
$('form').on('beforeSubmit', function(){
    var val =$("input[name='AcceptPrivacyForm[privacy_2]']:checked").val();
         $('#error_preference_tag').html('');
        if (val == '1') {
            var selected = $('#preference-tags-select-id').val();
            if(selected === undefined || selected === null || selected.length === 0){
                $('#error_preference_tag').html('E\' necessario selezionare almeno un tag di  interesse informativo');
                return false;
            }
            
        }
        return true;
});
 
JS;
$this->registerJs($js);

$preferenceCenterTags = \open20\amos\events\utility\EventsUtility::getPreferenceCenterTags();

$form = ActiveForm::begin([
]); ?>

    <div id="tab-preferencetags" class="col-xs-8">
        <?php
        $preferenceCenterTags = EventsUtility::getPreferenceCenterTags();
        ?>
        <p><?= \Yii::t('app', "Tag di interesse informativo") ?></p>
        <?php echo \kartik\select2\Select2::widget([
            'name' => 'preferencesTags',
            'data' => \yii\helpers\ArrayHelper::map($preferenceCenterTags, 'id', 'nome'),
            'options' => [
                'id' => 'preference-tags-select-id',
                'placeholder' => \Yii::t('app', "Seleziona i tag ..."),
                'multiple' => true,
                'title' => 'Tag di interesse informativo',
            ],
            'pluginOptions' => ['allowClear' => true]
        ]) ?>
        <span class="tooltip-error-field">
            <span style="color:#a31f33  !important" id="error_preference_tag" class="help-block help-block-error"></span>
        </span>
    </div>

    <br>

    <div class="col-xs-12 cookie-privacy m-t-10">
        <?= AmosAdmin::t('amosadmin', "Presa visione dell'informativa al trattamento di dati personali di cui al presente") . " " . \yii\helpers\Html::a(AmosAdmin::t('amosadmin', "link:"), \Yii::$app->params['platform']['frontendUrl'] . '/it/privacy', ['title' => AmosAdmin::t('amosadmin', '#cookie_policy_title'), 'target' => 'blank']) ?>
        <?= \yii\helpers\Html::tag('p', AmosAdmin::t('amosadmin', 'esprimo il consenso al trattamento dei miei dati personali per la finalità indicata alla lettera a) dell’informativa - registrazione alla piattaforma Eventi')) ?>
        <?= $form->field($model, 'privacy')->radioList([
            1 => AmosAdmin::t('amosadmin', '#cookie_policy_ok'),
            0 => AmosAdmin::t('amosadmin', '#cookie_policy_not_ok')
        ])->label(false); ?>
    </div>
    <div class="col-xs-12 cookie-privacy">
        <?= \yii\helpers\Html::tag('p', \backend\modules\eventsadmin\utility\EventsAdminUtility::getLabelPrivacy2()) ?>
        <?= $form->field($model, 'privacy_2')->radioList([
            1 => AmosAdmin::t('amosadmin', '#cookie_policy_ok'),
            0 => AmosAdmin::t('amosadmin', '#cookie_policy_not_ok')
        ])->label(false); ?>
    </div>

    <div class="col-xs-12">
        <?= \yii\helpers\Html::submitButton(\Yii::t('app', 'Conferma'), [
            'class' => 'btn btn-primary'
        ]) ?>
    </div>

<?php ActiveForm::end(); ?>