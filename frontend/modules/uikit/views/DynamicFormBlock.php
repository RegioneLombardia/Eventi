<!DOCTYPE html>
<?php

use app\assets\SocialAsset;
use app\modules\uikit\Module;
use open20\amos\attachments\components\AttachmentsInput;
use open20\amos\admin\AmosAdmin;
use open20\amos\core\forms\ActiveForm;
use open20\amos\core\forms\editors\Select;
use open20\amos\core\helpers\Html;
use kartik\datecontrol\DateControl;
use luya\helpers\Url;
use trk\uikit\Uikit;


SocialAsset::register($this);

$js = <<<JS
   
var url_string = window.location.href;
var url = new URL(url_string);
var c = url.searchParams.get("social-done");
if(c != null && c.length > 0){
           $([document.documentElement, document.body]).animate({
        scrollTop: $("#social-container").offset().top
    }, 1000);
}

var a = url.searchParams.get("spid-done");
if(a != null && a.length > 0){
           $([document.documentElement, document.body]).animate({
        scrollTop: $("#compile-form").offset().top
    }, 1000);
}

var d = url.searchParams.get("pEmail");
if(d != null && d.length > 0){
           $([document.documentElement, document.body]).animate({
        scrollTop: $("#subscribtion-form").offset().top
    }, 1000);
}


JS;
$this->registerJs($js);

$jsModalConfirm = <<<JS
var canSubmit = false;
$('#btn-confirm-modal').click(function(e){
    canSubmit = true;
});
$('#cancel-button').click(function(e){
    e.preventDefault();
   $('#modal-riepilogo').modal('hide');
});

$('#submit-form-btn').click(function(e){
    canSubmit = false;
});

$('form').on('beforeSubmit', function (e){
    e.preventDefault();
    if(!canSubmit){
        //check if we are in submission process and if there are any errors
            $('.in-riepilogo').each(function(){
                $('#modal-riepilogo .modal-body').append($(this).val());
                var idAttrModal = $(this).attr('data-key');
                var type = $(this).attr('data-type');
                var value = '';
                if(type === 'select'){
                    var tmpValue = $(this).find('select').val();
                    value = $(this).find('option[value="'+tmpValue+'"]').text();
                } else if (type === 'attachmentsInput'){
                    value  = $(this).find('input[class="file-caption-name"]').val();
                } else if (type === 'dynamicInputText'){
                    var i = 0;
                    var firstField = '';
                    var string = '';
                     $(this).find('input').each(function(){
                         var attribute = $(this).parents('.element-field').attr('data-attribute');
                         if(firstField === ''){
                            firstField = attribute;
                         }else{
                             if(attribute === firstField){
                                 string += "<br>";
                             }
                         }                      
                         string += $(this).val()+' ';
                         // $(this).val();
                     });
                     value = "<div>"+string+"</div>";
                   // console.log( $(this).find('input'));
                } else if(type === 'selectMultiple'){
                     var currentInput = $(this);
                     var tmpValues = $(this).find('select').val();
                     if(tmpValues.length > 0){
                         $(tmpValues).each(function(){
                            value += "<br>-"+ $(currentInput).find('option[value="'+this+'"]').text();
                         });
                     }
                     // console.log(tmpValue);
                     // console.log($(this).find('select'));
                     
                     
                }else {
                    value  = $(this).find('input,textarea').val();
                }
                $('#'+idAttrModal+' .modal-value').html(value);
            });
           $('#modal-riepilogo').modal('show');
           return false;
        }
        // }
});
JS;

if ($data['confirmModal'] == 1) {
    $this->registerJs($jsModalConfirm);
}

$hideForm = false;

$id = '';
$class = [];
$attrs = [];

$item_attrs = [];

$style = $this->varValue('style', '');
if ($style) {
    $class[] = 'uk-form-' . $style;
    if ($style == 'stacked') {
        $item_attrs['class'][] = 'uk-margin';
    }
}

$attrs['role'] = 'form';
$attrs['method'] = 'post';


$dlSemplificazione = true;
$spidData = \Yii::$app->session->get('IDM');
?>
<div>
    <?php
    //Uikit::trace($data);
    //Uikit::trace($debug);
    //Uikit::trace($request);
    //Uikit::trace($model->attributes());
    $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => "off", 'class' => 'login-register-form',],
        'encodeErrorSummary' => false,
        'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']],

    ]);
    ?>

    <?php $hideSpid = false;
    $spidData = \Yii::$app->session->get('IDM');
    if ($spidData || !\Yii::$app->user->isGuest) {
        $hideSpid = true;
    }
    ?>
    <?php if (!$hideSpid) { ?>
        <?php if ($data['register_with_social']) { ?>
            <div id="spid-container" class="col-xs-12 m-t-30 nop spid-container social-container text-center">
                <span style="width:100%;display:block;"><strong>Accedi</strong> con la tua identità digitale</span>
                <a data-key="spid" class="btn btn-spid"
                   title="Accedi con SPID"
                   href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/socialauth/shibboleth/endpoint" ?>">
                    <span class="icon-logo-spid"><?= AmosAdmin::t('amosadmin', "accedi con la tua identità digitale") ?></span>
                </a>
            </div>
            <?php if (!$dlSemplificazione) { ?>
                <div id="social-container" class="col-xs-12 m-t-30 nop social-container text-center">
                    <span style="width:100%;display:block;"><strong>Registrati</strong> con i tuoi account social</span>
                    <div class="social-buttons col-xs-12 text-center" style="float:none;">
                        <!--                <a data-key="facebook" class="btn btn-google social-link btn-login-social"-->
                        <!--                   title="Accedi con Google" target="_self"-->
                        <!--                   href="-->
                        <?php //echo Url::ensureHttp(Yii::$app->menu->current->getAbsoluteLink(), true) ?><!--?social=google">-->
                        <!--                    <span class="am am-google"></span>-->
                        <!--                </a>-->
                        <a data-key="facebook" class="btn btn-facebook social-link btn-login-social"
                           title="Accedi con Facebook" target="_self"
                           href="<?= Url::ensureHttp(Yii::$app->menu->current->getAbsoluteLink(), true) ?>?social=facebook">
                            <span class="am am-facebook"></span>
                            <!--                            <span class="text">Facebook</span>-->

                        </a>
                        <a data-key="twitter" class="btn btn-twitter social-link btn-login-social"
                           title="Accedi con Twitter" target="_self"
                           href="<?= Url::ensureHttp(Yii::$app->menu->current->getAbsoluteLink(), true) ?>?social=twitter">
                            <span class="am am-twitter"></span>
                            <!--                            <span class="text">Twitter</span>-->

                        </a>
                        <a data-key="linkedin" class="btn btn-linkedin social-link btn-login-social"
                           title="Accedi con LinkedIn" target="_self"
                           href="<?= Url::ensureHttp(Yii::$app->menu->current->getAbsoluteLink(), true) ?>?social=linkedin">
                            <span class="am am-linkedin"></span>
                            <!--                            <span class="text">LinkedIn</span>-->
                        </a>
                    </div>
                    <span style="width:100%;display:block;margin-top:20px;">o <strong>compila</strong> i seguenti campi</span>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>



    <?php $hideFormClass = 'display:none;';
    $spidData = \Yii::$app->session->get('IDM');
    if ($spidData || !\Yii::$app->user->isGuest || empty($data['register_with_social'])) {
        $hideFormClass = '';
    }
    ?>
    <div id="compile-form" class="col-xs-12 nop" style="<?= $hideFormClass ?>">
        <?php
        echo Html::hiddenInput('id', $data['id']);

        $attributes = $model->attributes();
        unset($attributes[array_search($model->getPrimaryKey(), $attributes)]);
        foreach ($attributes as $attribute) {
            $type = $model->attributeTypes()[$attribute];
            $inRiepilogo = false;
            if (!in_array($type, ['hidden', 'label']) && !in_array($attribute, ['recaptcha', 'privacy'])) {
                $inRiepilogo = true;
                $riepilogo[] = ['id' => $attribute . '-id', 'label' => $model->getAttributeLabel($attribute), 'type' => $type];
            }

            //AUTOCOMPLETE
            $autocomplete = false;
            if(isset($data['items'])){
                $key = array_search($attribute, array_column($data['items'], 'field'));
                if(isset($data['items'][$key]) && $data['items'][$key]['autocomplete']){
                    if($data['items'][$key]['autocomplete'] == 1)
                        $autocomplete = true;
                }
            }
            $autocompleteVal = $autocomplete ? 'on' : 'off';

            ?>
            <div <?= Uikit::attrs($item_attrs) ?>    class="<?= $attribute ?>_item <?= $inRiepilogo ? 'in-riepilogo' : '' ?>"
                                                    data-key="<?= $attribute . '-id' ?>"
                                                    data-type="<?= $model->attributeTypes()[$attribute] ?>">
                <?php
                $readOnly = false;
                if (!empty($readonlyAttributes)) {
                    $readOnly = (in_array($attribute, $readonlyAttributes)) ? true : false;
                }
                $filedItem = '';
                switch ($model->attributeTypes()[$attribute]) {
                    case 'hidden':
                        $filedItem = $form->field($model, $attribute, ['options' => ['style' => 'display:none']])
                            ->hiddenInput($model->getAttributeOptions($attribute))
                            ->label(false);
                        break;
                    case 'label':
                        $filedItem = Html::tag('span', $model->getAttributeLabel($attribute),
                            $model->getAttributeOptions($attribute));
                        break;
                    case 'dynamicInputText':
                        $filedItem = $this->render('landingform/dynamicInputText', ['model' => $model, 'attribute' => $attribute, 'form' => $form, 'data' => $data]);
                        break;
                    case 'attachmentsInput':
                        $filedItem = $form->field($model, $attribute,
                            [
                                'labelOptions' => ['encode' => false],
                            ])->widget(AttachmentsInput::classname(),
                            [
                                'options' => [
                                    'owner' => 1,
                                    'multiple' => false,
                                    'capture' => false,
                                    'accept' => "application/*,.doc,.docx,.xls,.xlsx,.pdf,.odt,.txt,.png,.jpg,.jpeg",
                                ],
                                'pluginOptions' => [// Plugin options of the Kartik's FileInput widget
                                    'maxFileCount' => 1,
                                    'showRemove' => false,
                                    'indicatorNew' => false,
                                    'allowedPreviewTypes' => false,
                                    'previewFileIconSettings' => false,
                                    'overwriteInitial' => false,
                                    'layoutTemplates' => false,
                                    'showPreview' => false,
                                ]
                            ])->label($model->getAttributeLabel($attribute));
                        break;
                    case 'checkbox':
                        if ($attribute == 'recaptcha') {
                            $filedItem = $form->field($model, 'recaptcha')
                                ->widget(\himiklab\yii2\recaptcha\ReCaptcha2::className(), [
                                    'siteKey' => \Yii::$app->params['google_recaptcha_site_key'], // unnecessary is reCaptcha component was set up
                                ])->label('');
                        } else {
                            $filedItem = $form->field($model, $attribute, $model->getAttributeOptions($attribute))->checkbox([
                                'label' => $model->getAttributeLabel($attribute),
                                'value' => true,
                                'labelOptions' => ['encode' => false],
                                'autocomplete' => $autocompleteVal
                            ])->hint($model->getAttributeHint($attribute));
                        }
                        break;
                    case 'radioList':
                        $filedItem = $form->field($model, $attribute, $model->getAttributeOptions($attribute))->radioList($model->attributeSubvalues()[$attribute])
                            ->label($model->getAttributeLabel($attribute));
                        break;
                    case 'checkList':
                        $filedItem = $form->field($model, $attribute, $model->getAttributeOptions($attribute))->checkboxList($model->attributeSubvalues()[$attribute])
                            ->label($model->getAttributeLabel($attribute));
                        break;
                    case 'string':
                        $filedItem = $form->field($model, $attribute, $model->getAttributeOptions($attribute))->textInput([
                            'title' => $model->getAttributeHint($attribute),
                            'data-placement' => 'bottom',
                            'onclick' => "$(this).tooltip('show');",
                            'readonly' => $readOnly,
                            'autocomplete' => $autocompleteVal
                        ])->label($model->getAttributeLabel($attribute))->hint(false);
                        break;
                    case 'tel':
                        $filedItem = $form->field($model, $attribute, $model->getAttributeOptions($attribute))->textInput([
                            'title' => $model->getAttributeHint($attribute),
                            'data-placement' => 'bottom',
                            'onclick' => "$(this).tooltip('show');",
                            //'pattern' => "[0-9+-/]{3,16}",
                        ])->label($model->getAttributeLabel($attribute))->hint(false);
                        break;
                    case 'textarea':
                        $filedItem = $form->field($model, $attribute, $model->getAttributeOptions($attribute))->textarea([
                            'title' => $model->getAttributeHint($attribute),
                            'data-placement' => 'bottom',
                            'onclick' => "$(this).tooltip('show');",
                            'autocomplete' => $autocompleteVal
                        ])->label($model->getAttributeLabel($attribute))->hint(false);
                        break;
                    case 'textEditor':
                        $filedItem = $form->field($model, $attribute, $model->getAttributeOptions($attribute))
                            ->widget(\open20\amos\core\forms\TextEditorWidget::className(),[
                                'options' => ['autocomplete' => $autocompleteVal]
                            ])
                            ->label($model->getAttributeLabel($attribute),['class' => 'position-relative'])->hint(false);
                        break;
                    case 'date':
                        $filedItem = $form->field($model, $attribute, $model->getAttributeOptions($attribute))->widget(DateControl::className(),
                            [
                                'type' => DateControl::FORMAT_DATE
                            ])->label($model->getAttributeLabel($attribute));
                        break;
                    case 'password':
                        $filedItem = $form->field($model, $attribute, $model->getAttributeOptions($attribute))->passwordInput([
                            'autocomplete' => 'off'])->label($model->getAttributeLabel($attribute));
                        break;
                    case 'select':
                        $filedItem = $form->field($model, $attribute, $model->getAttributeOptions($attribute))->widget(Select::className(),
                            [
                                'auto_fill' => false,
                                'data' => $model->attributeSubvalues()[$attribute],
                                'options' => [
                                    'prompt' => Module::t('Seleziona'),
                                    'label' => $model->getAttributeLabel($attribute),
                                    'autocomplete' => 'off'
                                ]
                            ])->label($model->getAttributeLabel($attribute));
                        break;
                    case 'url':
                        $filedItem = $form->field($model, $attribute, $model->getAttributeOptions($attribute))->textInput([
                            'title' => $model->getAttributeHint($attribute),
                            'data-placement' => 'bottom',
                            'onclick' => "$(this).tooltip('show');",
                            'autocomplete' => $autocompleteVal
                        ])->label($model->getAttributeLabel($attribute))->hint(false);
                        break;
                    default:
                        $filedItem = $form->field($model, $attribute, $model->getAttributeOptions($attribute))
                            ->textInput(['autocomplete' => $autocompleteVal])
                            ->label($model->getAttributeLabel($attribute));
                        break;
                }
                echo $filedItem;
                ?>
            </div>
            <?php
        }
        ?>
        <div class="uk-form-controls">
            <?=
            Html::submitButton(!empty($data['submitlabel']) ? $data['submitlabel'] : 'Submit',
                ['class' => 'btn btn-primary'])
            ?>
        </div>
    </div>



    <div id="modal-riepilogo" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
         aria-labelledby="modal-riepilogo" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="modal2Title"><?= $data['confirmModalTitle'] ?></h3>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span class="mdi mdi-close"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><?= $data['confirmModalText'] ?></p>
                    <div class="fields-summary mt-3 border border-light rounded p-3">
                        <?php foreach ($riepilogo as $field) { ?>
                            <div data-key="<?= $field['id'] ?>" id="<?= $field['id'] ?>">
                                <p>
                                    <strong><?= $field['label'] . ': ' ?></strong>
                                    <span class="modal-value"></span>
                                </p>
                            </div>
                        <?php } ?>
                    </div>


                </div>
                <div class="modal-footer">
                    <?= Html::a($data['confirmModalCancel'], '#', [
                        'data-bs-dismiss' => 'modal',
                        'class' => 'btn btn-outline-secondary',
                        'id' => 'cancel-button',
                        'title' => $data['confirmModalCancel']

                    ]) ?>
                    <?= Html::submitButton($data['confirmModalSubmit'], [
                        'class' => 'btn btn-primary mb-0',
                        'id' => 'btn-confirm-modal',
                        'title' => $data['confirmModalSubmit']
                    ]) ?>
                </div>
            </div>

        </div>
    </div>
    <?php
    ActiveForm::end();
    ?>
</div>
