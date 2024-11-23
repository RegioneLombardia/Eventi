<?php

use open20\amos\core\forms\ActiveForm;
use \open20\amos\events\utility\EventsUtility;
use open20\amos\events\AmosEvents;

$js = <<<JS
$('form').on('beforeSubmit', function(){
    var val =$("input[name='RecordDynamicModel[privacy_2]']:checked").val();
         $('#error_preference_tag').html('');

        if (val == '1') {
            var selected = $('#preference-tags-select-id').val();
                  if(selected === undefined || selected === null || selected.length === 0){
                $('#error_preference_tag').html("E' necessario selezionare almeno un tag di  interesse informativo");
                return false;
            }
            // if(selected.length === 0){
            //     $('#error_preference_tag').html("E' necessario selezionare almeno un tag di interesse informativo");
            //     return false;
            // }
            
        }
        return true;
});
 
JS;
$this->registerJs($js);

$preferenceCenterTags = \open20\amos\events\utility\EventsUtility::getPreferenceCenterTags();
$tagsValues = [];
foreach ($preferenceCenterTags as $tag){
    $tagsValues [$tag->id] = AmosEvents::t('amosevents', $tag->nome);
}
$form = ActiveForm::begin([
]); ?>

    <div class="from-group m-b-10">
        <label class='control-label'><strong><?= AmosEvents::t('amosevents','Tag di interesse informativo') ?></strong></label>
        <?php echo \kartik\select2\Select2::widget([
            'name' => 'preferences_tags',
            'data' => $tagsValues,
            'options' => [
                'id' => 'preference-tags-select-id',
                'placeholder' => \Yii::t('site', "Seleziona i tag ..."),
                'multiple' => true,
                'title' => AmosEvents::t('amosevents','Tag di interesse informativo'),
            ],
            'pluginOptions' => ['allowClear' => true]
        ])?>
<!--        --><?php //\yii\helpers\Html::checkboxList('preferences_tags', $model->preference_tags, EventsUtility::getLabelPreference(), ['encode' => false]) ?>
        <span class="tooltip-error-field">
            <span id="error_preference_tag" class="help-block help-block-error">
            </span>
        </span>
    </div>


    <br>
    <div>
        <?php
        $items = $model->attributeSubvalues()['privacy_2'];
        foreach ($items as $key => $label){
            $items[$key] =   $label;
        }?>
        <?=
        $form->field($model, 'privacy_2')->radioList($items)
            ->label(\backend\modules\eventsadmin\utility\EventsAdminUtility::getLabelPrivacy2())
        ?>
    </div>
<?= \yii\helpers\Html::hiddenInput('update_tags', '1') ?>
    <div class="uk-form-controls">
        <?= \yii\helpers\Html::submitButton(\Yii::t('site', 'Conferma'), [
            'class' => 'btn btn-primary'
        ]) ?>
    </div>

<?php ActiveForm::end(); ?>