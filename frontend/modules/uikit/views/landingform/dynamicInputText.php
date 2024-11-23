<?php

/** @var $model
 * @var $attribute
 * @var $form
 */

$requiredText = '';
if (!empty($data)) {
    foreach ($data['items'] as $d) {
        if ($d['field'] == $attribute) {
            $this->registerJsVar('limit_' . $attribute, $d['limit']);

            if (isset($d['required']) && $d['required'] == 1) {
                $requiredText = "<span>*</span>";
            }
        }
    }
    //    $this->registerJsVar('limit', $data[$attribute]['limit']);
}
$this->registerJsVar('count_' . $attribute, $d['limit']);

$js = <<<JS
    $(document).on('click','.container-dynamic-text-$attribute .add-dynamic-text-input', function(e){
        e.preventDefault();
        count_$attribute ++;
      
        var elem = $('.container-dynamic-text-$attribute .element-to-copy').clone();
        $(elem).removeClass('element-to-copy');
        $(elem).find('.remove-dynamic-text-input').attr('style', '');
        $(elem).find('.element-field').val('');
        $(elem).find('.element-field input').each(function(index, elem){
            $(this).val('');
        });
        
        $('.container-dynamic-text-$attribute').append(elem);
        
        if(count_$attribute + 1 == parseInt(limit_$attribute)){
            $('.container-dynamic-text-$attribute .add-dynamic-text-input').each(function(){
                $(this).attr('disabled',true)
            });
        }
    });

    $(document).on('change focusout','.container-dynamic-text-$attribute input', function(){
        if($(this).val().trim() == ''){
             $(this).parents('.element-field').find('.server-side-error').text('Questo campo non può essere vuoto');
        }else{
            $(this).parents('.element-field').find('.server-side-error').text('');
        }
    });

    $(document).on('click','.container-dynamic-text-$attribute .remove-dynamic-text-input', function(e){
        count_$attribute--;
        e.preventDefault();
        
        $(this).parents('.element-row').remove();
         if(count_$attribute < parseInt(limit_$attribute)){
            $('.container-dynamic-text-$attribute .add-dynamic-text-input').each(function(){
                $(this).removeAttr('disabled');
            });
        }
    });
JS;

$this->registerJs($js);

?>
<div class="container-dynamic-text-<?= $attribute ?>">
    <?php if (!empty($model->getAttributeLabel($attribute))) { ?>
        <p><strong><?= $model->getAttributeLabel($attribute) ?></strong></p>
    <?php } ?>
    <?php
    //ripopolo in caso di errore di validazione
    $attributes = $model->$attribute;
    $count = 1;
    if (!empty($attributes)) {
        $a = array_pop(array_reverse($attributes));
        $count = count($a);
    }

    for ($i = 0; $i < $count; $i++) {
        $value = null;
    ?>
        <div class="<?= $i == 0 ? 'element-to-copy' : '' ?> element-row form-row">
            <?php foreach ($model->attributeSubvalues()[$attribute] as $attributeSubvalue => $subvalueLabel) { ?>
                <?php $value = $attributes[$attributeSubvalue][$i]; ?>
                <div  data-attribute="<?= $attributeSubvalue ?>" class="element-field col-sm">
                    <?= $form->field($model, $attribute . "[$attributeSubvalue][]")
                        ->textInput(['label' => $subvalueLabel . $requiredText, 'value' => $value,  'autocomplete' => 'off'
                        ])
                    ->label($subvalueLabel . $requiredText)?>
                </div>
            <?php } ?>
            <div class="col-md mb-4 mb-md-0 text-right">
                <div class="d-inline">
                    <?= \yii\helpers\Html::button('<span class="mdi mdi-minus"></span>', ['class' => 'btn btn-outline-danger remove-dynamic-text-input', 'style' => "display:none"]) ?>
                </div>
                <div class="ml-1 d-inline">
                    <?= \yii\helpers\Html::button('<span class="mdi mdi-plus mdi-28px"></span>', ['class' => 'btn btn-primary add-dynamic-text-input']) ?>
                </div>
            </div>

        </div>
    <?php } ?>
</div>