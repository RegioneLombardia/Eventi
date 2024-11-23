<!DOCTYPE html>
<?php

use app\assets\SocialAsset;
use app\modules\uikit\Module;
use open20\amos\attachments\components\AttachmentsInput;
use open20\amos\core\forms\ActiveForm;
use open20\amos\core\forms\editors\Select;
use open20\amos\core\helpers\Html;
use kartik\datecontrol\DateControl;
use kartik\widgets\DepDrop;
use luya\helpers\Url;
use trk\uikit\Uikit;
use open20\amos\events\AmosEvents;

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

var d = url.searchParams.get("pEmail");
if(d != null && d.length > 0){
           $([document.documentElement, document.body]).animate({
        scrollTop: $("#subscribtion-form").offset().top
    }, 1000);
}

var a = url.searchParams.get("spid-done");
if(a != null && a.length > 0){
           $([document.documentElement, document.body]).animate({
        scrollTop: $("#subscribtion-form").offset().top
    }, 1000);
}

$('#enable-companions-id').on('change', function(){
    if($(this).val() == '1'){
        $('#n-companions-id').show();
        $('#n-companions-value-id').val(1).trigger('change');
    }else{
        $('#n-companions-id').hide();
        $('#n-companions-value-id').val(0).trigger('change');
    }
});
JS;
$this->registerJs($js);

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
        }
        return true;
});

 $('body').on('beforeValidate', 'form', function (e) {
        $(':input[type="submit"]', this).attr('disabled', 'disabled');
        $(':input[type="submit"]', this).each(function (i) {
            if ($(this).prop('tagName').toLowerCase() === 'input') {
                $(this).data('enabled-text', $(this).val());
                $(this).val($(this).data('disabled-text'));
            } else {
                $(this).data('enabled-text', $(this).html());
                $(this).html($(this).data('disabled-text'));
            }
        });
    }).on('afterValidate', 'form', function (e) {
        if ($(this).find('.has-error').length > 0) {
            $(':input[type="submit"]', this).removeAttr('disabled');
            $(':input[type="submit"]', this).each(function (i) {
                if ($(this).prop('tagName').toLowerCase() === 'input') {
                    $(this).val($(this).data('enabled-text'));
                } else {
                    $(this).html($(this).data('enabled-text'));
                }
            });
        }
    });
JS;
$this->registerJs($js);

$moduleEvents = \Yii::$app->getModule('events');
$disableSocial = $moduleEvents->disableSocial;
$dataSpid = \Yii::$app->session->get('IDM');
$canCompileFields = true;
if ($disableSocial) {
    if (!empty($dataSpid) || !\Yii::$app->user->isGuest) {
        $canCompileFields = true;
    } else {
        $canCompileFields = false;
    }
}


?>

<h2 id="titlesubscribe" class="form-title uk-text-left"><?= AmosEvents::t('amosevents', "Iscriviti all'evento") ?></h2>
<div>
    <?php
    //Uikit::trace($data);
    //Uikit::trace($debug);
    //Uikit::trace($request);
    //Uikit::trace($model->attributes());
    $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => "off"],
        'encodeErrorSummary' => false,
        'fieldConfig' => ['errorOptions' => ['encode' => false, 'class' => 'help-block']]
    ]);
    ?>
    <?php if ($data['register_with_social'] || $data['spid_cns_reg']) { ?>
        <div id="social-container" class="col-xs-12 m-t-30 nop social-container text-center">
            <?php if (\Yii::$app->user->isGuest && empty($dataSpid)) { ?>

                <?php if ($disableSocial) { ?>
                    <span style="width:100%;display:block;"><?= AmosEvents::t('amosevents', "<strong>ACCEDI</strong> con la tua identità digitale") ?></span>
                    <?php if ($data['spid_cns_reg']) { ?>
                        <div class="social-buttons text-center" style="float:none;">
                            <?php
                            $socialauthName = 'socialauth';
                            if (YII_ENV == 'prod') {
                                $socialauthName = 'socialauthfe';
                            } ?>
                            <a href="<?= \Yii::$app->params['platform']['backendUrl'] . "/$socialauthName/shibboleth/endpoint" ?>"
                               title="Accedi con la tua identità digitale"
                               class="btn btn-lg btn-idpc"><?= AmosEvents::t('amosevents', 'Accedi con la tua identità digitale') ?></a>

                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <span style="width:100%;display:block;"><strong>Accedi</strong> con i tuoi account </span>
                    <div class="social-buttons col-xs-12 text-center" style="float:none;">
                        <?php if ($data['register_with_social'] && $data['goolge_reg']) { ?>

                            <a data-key="google" class="btn btn-facebook social-link btn-login-social"
                               title="Accedi con Google" target="_self"
                               href="<?= Url::ensureHttp(Yii::$app->menu->current->getAbsoluteLink(),
                                   true) ?>?social=google">
                                <span class="am am-google"></span>
                                <!--                            <span class="text">Facebook</span>-->

                            </a>
                        <?php } ?>

                        <?php if ($data['register_with_social'] && $data['apple_reg']) { ?>
                            <a data-key="apple" class="btn btn-apple social-link btn-login-social"
                               title="Accedi con Apple" target="_self"
                               href="<?= Url::ensureHttp(Yii::$app->menu->current->getAbsoluteLink(),
                                   true) ?>?social=apple">
                                <span class="am am-apple"></span>
                                <!--                            <span class="text">Facebook</span>-->
                            </a>
                        <?php } ?>

                        <?php if ($data['register_with_social'] && $data['facebook_reg']) { ?>
                            <a data-key="facebook" class="btn btn-facebook social-link btn-login-social"
                               title="Accedi con Facebook" target="_self"
                               href="<?= Url::ensureHttp(Yii::$app->menu->current->getAbsoluteLink(),
                                   true) ?>?social=facebook">
                                <span class="am am-facebook"></span>
                                <!--                            <span class="text">Facebook</span>-->

                            </a>
                        <?php } ?>

                        <?php if ($data['register_with_social'] && $data['twitter_reg']) { ?>
                            <a data-key="twitter" class="btn btn-twitter social-link btn-login-social"
                               title="Accedi con Twitter" target="_self"
                               href="<?= Url::ensureHttp(Yii::$app->menu->current->getAbsoluteLink(),
                                   true) ?>?social=twitter">
                                <span class="am am-twitter"></span>
                                <!--                            <span class="text">Twitter</span>-->
                            </a>
                        <?php } ?>

                        <?php if ($data['register_with_social'] && $data['linkedin_reg']) { ?>

                            <a data-key="linkedin" class="btn btn-linkedin social-link btn-login-social"
                               title="Accedi con LinkedIn" target="_self"
                               href="<?= Url::ensureHttp(Yii::$app->menu->current->getAbsoluteLink(),
                                   true) ?>?social=linkedin">
                                <span class="am am-linkedin"></span>
                                <!--                            <span class="text">LinkedIn</span>-->
                            </a>
                        <?php } ?>

                        <?php if ($data['spid_cns_reg']) { ?>
                            <a data-key="spid" class="btn btn-spid social-link btn-login-social"
                               title="Accedi con SPID" target="_self"
                               href="<?= \Yii::$app->params['platform']['backendUrl'] . "/socialauthfe/shibboleth/endpoint" ?>"
                               style="width:60px !important;; background: transparent !important;; position: relative; background-color: transparent !important;">
                                <span class="icon-logo-spid"></span>
                                <!--                            <span class="text">LinkedIn</span>-->
                            </a>
                        <?php } ?>

                        <a data-key="rl" class="btn btn-rl social-link btn-login-rl m-t"
                           title="Accedi con RL" target="_self"
                           href="<?= \Yii::$app->params['platform']['backendUrl'] . "/admin/security/login-frontend?redir=" . urlencode(\Yii::$app->request->absoluteUrl) ?>">
                            <span class="icon-rl-white"></span>
                            <!--                            <span class="text">LinkedIn</span>-->
                        </a>

                    </div>
                <?php } ?>
                <?php if ($canCompileFields) { ?>
                    <span style="width:100%;display:block;margin-top:20px;">o <?= AmosEvents::t('amosevents', "<strong>compila</strong> i seguenti campi") ?></span>
                <?php } ?>
            <?php } else { ?>
                <span style="width:100%;display:block;margin-top:20px;"><?= AmosEvents::t('amosevents', "<strong>compila</strong> i seguenti campi") ?></span>
            <?php } ?>
        </div>


    <?php } ?>

    <?php if ($canCompileFields) { ?>
        <div id="subscribtion-form" class="col-xs-12 nop">
            <?php
            echo Html::hiddenInput('id', $data['id']);

            $attributes = $model->attributes();
            unset($attributes[array_search($model->getPrimaryKey(), $attributes)]);
            foreach ($attributes as $attribute) {
                $readonlyAttribute = !empty($model->$attribute) ? true : false;
                if (!empty($dataSpid) && $attribute == 'email') {
                    $readonlyAttribute = false;
                }
                ?>
                <div<?= Uikit::attrs($item_attrs) ?> class="<?= $attribute ?>_item">
                    <?php
                    $filedItem = '';
                    switch ($model->attributeTypes()[$attribute]) {
                        case 'hidden':
                            $filedItem = $form->field($model, $attribute)->hiddenInput($model->getAttributeOptions($attribute))->label(false);
                            break;
                        case 'label':
                            $filedItem = Html::tag('span',
                                AmosEvents::t('amosevents', $model->getAttributeLabel($attribute)),
                                $model->getAttributeOptions($attribute));
                            break;
                        case 'attachmentsInput':
                            $filedItem = $form->field($model, $attribute,
                                [
                                    'labelOptions' => ['encode' => false],
                                ])->widget(AttachmentsInput::classname(),
                                [
                                    'options' => [
                                        'owner' => 1,
                                        'multiple' => FALSE,
                                    ],
                                    'pluginOptions' => [// Plugin options of the Kartik's FileInput widget
                                        'maxFileCount' => 1,
                                        'showRemove' => false,
                                        'indicatorNew' => false,
                                        'allowedPreviewTypes' => false,
                                        'previewFileIconSettings' => false,
                                        'overwriteInitial' => false,
                                        'layoutTemplates' => false,
                                    ]
                                ])->label($model->getAttributeLabel($attribute));
                            break;
                        case 'checkbox':
                            $filedItem = $form->field($model, $attribute,
                                $model->getAttributeOptions($attribute))->checkbox([
                                'label' => $model->getAttributeLabel($attribute),
                                'labelOptions' => ['encode' => false]
                            ])->hint(AmosEvents::t('amosevents', $model->getAttributeHint($attribute)));
                            break;
                        case 'radioList':
                            $hide = '';
                            if ($attribute == 'privacy' && $model->privacy) {
                                $hide = 'display:none';
                            }
                            $items = $model->attributeSubvalues()[$attribute];
                            foreach ($items as $key => $label) {
                                $items[$key] = AmosEvents::t('amosevents', $label);
                            }
                            $label = AmosEvents::t('amosevents', $model->getAttributeLabel($attribute));
                            if ($attribute == 'privacy_2') {
                                $label = \backend\modules\eventsadmin\utility\EventsAdminUtility::getLabelPrivacy2();
                            } else if ($attribute == 'privacy') {
                                $label = \backend\modules\eventsadmin\utility\EventsAdminUtility::getLabelPrivacy1();
                            }
                            $filedItem = "<div style='$hide'>" . $form->field($model, $attribute,
                                    $model->getAttributeOptions($attribute))->radioList($items)
                                    ->label($label) . "</div>";
                            break;
                        case 'radioListPlusTags':
                            $filedItem = $form->field($model, $attribute,
                                $model->getAttributeOptions($attribute))->radioList($model->attributeSubvalues()[$attribute])
                                ->label($model->getAttributeLabel($attribute));
                            $preferenceCenterTags = \open20\amos\events\utility\EventsUtility::getPreferenceCenterTags();
                            $filedItem .= "<div id=\"tab-preferencetags\" class=\"col-xs-12\" style=\"display:none\">"
                                . "<h4 class='control-label'>Compila Tag di interesse informativo</h4>"
                                . \yii\helpers\Html::checkboxList('preferencesTags', $preferencesTags, \open20\amos\events\utility\EventsUtility::getLabelPreference(), ['encode' => false])
                                . "</div>";

                            break;
                        case 'checkList':
                            $filedItem = $form->field($model, $attribute,
                                $model->getAttributeOptions($attribute))->checkboxList($model->attributeSubvalues()[$attribute])
                                ->label(AmosEvents::t('amosevents', $model->getAttributeLabel($attribute)));
                            break;
                        case 'string':
                            $filedItem = $form->field($model, $attribute,
                                $model->getAttributeOptions($attribute))->textInput([
                                'title' => $model->getAttributeHint($attribute),
                                'data-placement' => 'bottom',
                                'readonly' => $readonlyAttribute,
                                'onclick' => "$(this).tooltip('show');"
                            ])->label(AmosEvents::t('amosevents', $model->getAttributeLabel($attribute)))->hint(false);
                            break;
                        case 'textarea':
                            $filedItem = $form->field($model, $attribute,
                                $model->getAttributeOptions($attribute))->textarea([
                                'title' => $model->getAttributeHint($attribute),
                                'data-placement' => 'bottom',
                                'onclick' => "$(this).tooltip('show');"
                            ])->label(AmosEvents::t('amosevents', $model->getAttributeLabel($attribute)))->hint(false);
                            break;
                        case 'date':
                            $filedItem = $form->field($model, $attribute,
                                $model->getAttributeOptions($attribute))->widget(DateControl::className(),
                                [
                                    'type' => DateControl::FORMAT_DATE
                                ])->label(AmosEvents::t('amosevents', $model->getAttributeLabel($attribute)));
                            break;
                        case 'password':
                            $filedItem = $form->field($model, $attribute,
                                $model->getAttributeOptions($attribute))->passwordInput([
                                'autocomplete' => 'off'])->label($model->getAttributeLabel($attribute));
                            break;
                        case 'select':
                            $hiddenInput = '';
                            if (!empty($model->$attribute)) {
                                $hiddenInput = "<div style='display:none'>" . $form->field($model, $attribute)->hiddenInput()->label(false) . "</div>";
                            }
                            $items = $model->attributeSubvalues()[$attribute];
                            foreach ($items as $key => $label) {
                                $items[$key] = AmosEvents::t('amosevents', $label);
                            }
                            $filedItem = $form->field($model, $attribute,
                                    $model->getAttributeOptions($attribute))->widget(Select::className(),
                                    [
                                        'auto_fill' => false,
                                        'data' => $items,
                                        'options' => [
                                            'disabled' => !empty($model->$attribute) ? true : false,
                                            'prompt' => Module::t('Seleziona'),
                                            'label' => $model->getAttributeLabel($attribute)
                                        ]
                                    ])->label(AmosEvents::t('amosevents', $model->getAttributeLabel($attribute))) . $hiddenInput;
                            break;
                        case 'selectdb':
                            $hiddenInput = '';
                            if (!empty($model->$attribute)) {
                                $hiddenInput = "<div style='display:none'>" . $form->field($model, $attribute)->hiddenInput()->label(false) . "</div>";
                            }
                            if (count($model->attributeSubvalues()[$attribute])) {
                                $data = [];
                                $method_data = key($model->attributeSubvalues()[$attribute]);
                                if (!empty($method_data)) {
                                    $method_params = $model->attributeSubvalues()[$attribute][$method_data];
                                    $data = $method_data($method_params);
                                }
                                $filedItem = $form->field($model, $attribute,
                                        $model->getAttributeOptions($attribute))->widget(Select::className(),
                                        [
                                            'auto_fill' => false,
                                            'data' => $data,
                                            'options' => [
                                                'disabled' => !empty($model->$attribute) ? true : false,
                                                'id' => $attribute . '-id',
                                                'prompt' => Module::t('Seleziona'),
                                                'label' => $model->getAttributeLabel($attribute)
                                            ]
                                        ])->label(AmosEvents::t('amosevents', $model->getAttributeLabel($attribute))) . $hiddenInput;

                            }
                            break;
                        case 'selectrel':
                            $hiddenInput = '';
                            if (!empty($model->$attribute)) {
                                $hiddenInput = "<div style='display:none'>" . $form->field($model, $attribute)->hiddenInput()->label(false) . "</div>";
                            }
                            $data = [];
                            if ($attribute == 'city') {
                                $data = !empty($model->country) ? \yii\helpers\ArrayHelper::map(\open20\amos\comuni\models\IstatComuni::find()->andWhere(['istat_province_id' => $model->country])->all(), 'id', 'nome') : [];
                            }
                            if (count($model->attributeSubvalues()[$attribute])) {
                                $id_rel = key($model->attributeSubvalues()[$attribute]);
                                if (!empty($id_rel)) {
                                    $method_data = $model->attributeSubvalues()[$attribute][$id_rel];
                                }
                                $filedItem = $form->field($model, $attribute,
                                        $model->getAttributeOptions($attribute))->widget(DepDrop::className(),
                                        [
                                            'data' => $data,
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'options' => [
                                                'disabled' => !empty($model->$attribute) ? true : false,
                                                'id' => $attribute . '-id',
                                                'label' => $model->getAttributeLabel($attribute),
                                            ],
                                            'pluginOptions' => [
                                                'depends' => [$id_rel],
                                                'placeholder' => Module::t('Seleziona'),
                                                'url' => Url::to([$method_data]),
                                                'params' => [$attribute . '-id'],
                                            ],
                                        ])->label(AmosEvents::t('amosevents', $model->getAttributeLabel($attribute))) . $hiddenInput;
                            }

                            $countyExist = !in_array('country', $attributes);
                            if ($countyExist && $attribute == 'city') {
                                $comuniQuery = \open20\amos\comuni\models\IstatComuni::find()->orderBy('nome');
                                $moduleEvents = \Yii::$app->getModule('events');
                                if ($moduleEvents && !empty($moduleEvents->showOnlyRegionInWizard)) {
                                    $comuniQuery->andWhere(['istat_regioni_id' => $moduleEvents->showOnlyRegionInWizard]);
                                }
                                $data = \yii\helpers\ArrayHelper::map($comuniQuery->all(), 'id', 'nome');
                                $filedItem = $form->field($model, $attribute,
                                    $model->getAttributeOptions($attribute))->widget(\kartik\select2\Select2::className(),
                                    [
                                        'data' => $data,
                                        'options' => [
                                            'id' => $attribute . '-id',
                                            'label' => $model->getAttributeLabel($attribute),
                                        ],
                                        'pluginOptions' => [
                                            'placeholder' => Module::t('Seleziona'),
                                        ],
                                    ])->label(AmosEvents::t('amosevents', $model->getAttributeLabel($attribute)));
                            }
                            break;
                        case 'preferenceTags':
                            $preferenceCenterTags = \open20\amos\events\utility\EventsUtility::getPreferenceCenterTags();
                            $tagsValues = [];
                            foreach ($preferenceCenterTags as $tag) {
                                $tagsValues [$tag->id] = AmosEvents::t('amosevents', $tag->nome);
                            }
//                        $filedItem = "<h4 class='control-label'><strong>Tag di interesse informativo</strong></h4>"
//                            . "<div id=\"tab-preferencetags\" class=\"col-xs-12 from-group\">"
//                            . \yii\helpers\Html::checkboxList('preferences_tags', $model->preference_tags, \open20\amos\events\utility\EventsUtility::getLabelPreference(), ['encode' => false])
//                            . "<span class=\"tooltip-error-field\">
//                                    <span id=\"error_preference_tag\" class=\"help-block help-block-error\"></span>
//                                </span>"
//                            . "</div>";
                            $label = '<label class="control-label">' . AmosEvents::t('amosevents', 'Tag di interesse informativo') . '</label>';
                            $filedItem = $label . \kartik\select2\Select2::widget([
                                    'name' => 'preferences_tags',
                                    'value' => $model->$attribute,
                                    'data' => $tagsValues,
                                    'options' => [
                                        'id' => 'preference-tags-select-id',
                                        'placeholder' => \Yii::t('site', "Seleziona i tag ..."),
                                        'multiple' => true,
                                        'title' => 'Tag di interesse informativo',
                                    ],
                                    'pluginOptions' => ['allowClear' => true]
                                ])
                                . '<span class="tooltip-error-field"><span id="error_preference_tag" class="help-block help-block-error"></span></span><br>';
                            break;
                        case 'companions':
                            if($event->enable_companions) {
                                $dataCompanions = $event->getListNcompanions(true);

                                $filedItem = "<label class=\"control-label\" for=\"recorddynamicmodel-n_companions\">" . \Yii::t('site', 'Aggiungi partecipanti aggiuntivi') . "</label>";
                                $value = null;
                                $hideNcompanions = 'display:none';
                                if (!is_null(\Yii::$app->request->post('enable_companions'))) {
                                    $value = true;
                                    $hideNcompanions = '';
                                }
                                $filedItem .= \kartik\select2\Select2::widget([
                                    'data' => [1 => \Yii::t('site', 'Si'), 0 => \Yii::t('site', 'No')],
                                    'name' => 'enable_companions',
                                    'value' => $value,
                                    'options' => ['id' => 'enable-companions-id', 'placeholder' => \Yii::t('site', 'Seleziona'),]

                                ]);
                                if (isset(\Yii::$app->request->post('RecordDynamicModel')['n_companions'])) {
                                    $model->n_companions = \Yii::$app->request->post('RecordDynamicModel')['n_companions'];
                                }

                                if (empty($dataCompanions)) {
                                    $filedItem .= "<div id='n-companions-id' class='text-warning' style='margin-bottom:10px;$hideNcompanions'>" . \Yii::t('site', "Non ci sono posti disponibili per accompagnatori.") . "</div>";
                                } else {
                                    $tooltip= "<a href='javascript:void(0)' uk-tooltip='Indicare minorenni o altre persone impossibilitate ad iscriversi alla piattaforma secondo le modalità previste'><span class='am am-info'></span></a>";
                                    $filedItem .= "<div id='n-companions-id' style='$hideNcompanions'>" . $form->field($model, $attribute, $model->getAttributeOptions($attribute))->widget(\kartik\select2\Select2::className(), [
                                            'data' => $dataCompanions,
                                            'options' => [
                                                'placeholder' => \Yii::t('site', 'Seleziona'),
                                                'id' => 'n-companions-value-id'

                                            ]
                                        ])->label(\Yii::t('site', "Numero partecipanti aggiuntivi").' '.$tooltip) . "</div>";

                                }
                            }
                            break;
                        default:
                            $filedItem = $form->field($model, $attribute,
                                $model->getAttributeOptions($attribute))->label($model->getAttributeLabel($attribute))->textInput(['readonly' => $readonlyAttribute,
                            ]);
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
                Html::submitButton(!empty($data['submitlabel']) ? AmosEvents::t('amosevents', $data['submitlabel']) : AmosEvents::t('amosevents', '#subscribe_form'),
                    ['class' => 'btn btn-primary'])
                ?>
            </div>
        </div>
    <?php } ?>
    <?php
    ActiveForm::end();
    ?>
</div>
