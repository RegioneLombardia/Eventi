<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\views\security
 * @category   CategoryName
 */

use open20\amos\admin\AmosAdmin;
use open20\amos\core\helpers\Html;
use open20\amos\admin\assets\ModuleAdminAsset;
use open20\amos\core\forms\ActiveForm;
use open20\amos\core\icons\AmosIcons;
use yii\helpers\ArrayHelper;
use open20\amos\core\forms\editors\Select;
use kartik\select2\Select2;
use open20\amos\events\utility\EventsUtility;

ModuleAdminAsset::register(Yii::$app->view);

/**
 * @var yii\web\View $this
 * @var yii\bootstrap\ActiveForm $form
 * @var \open20\amos\admin\models\RegisterForm $model
 */
$text = AmosAdmin::t('amosadmin', "#register_privacy_alert_not_accepted");

$js = <<<JS
    var selected_social_url = '';
    // $('.social-link').click(function(event){
    //     selected_social_url = $(this).attr('href');
    //     event.preventDefault();
    //     $('#modal-privacy').modal('show');
    // });
    
    $('.radio-privacy input').click(function(){
         var checked = $('.radio-privacy input:checked').val();
         if(checked == 1){
         $('.radio-privacy').append('<p class="help-block help-block-error">'+'$text'+'</p>');
         }
         else {
           $('.radio-privacy p').remove();
        }
    });
    
    var url_string = window.location.href;
    var url = new URL(url_string);
    var c = url.searchParams.get("social-done");
    if(c != null && c.length > 0){
               $([document.documentElement, document.body]).animate({
            scrollTop: $("#login-form").offset().top
        }, 1000);
    }
    //
    // $('#confirm-privacy-button').click(function(){
    //     var checked = $('.radio-privacy input:checked').val();
    //    if(checked == 0) {
    //         window.open(selected_social_url);
    //         $('#modal-privacy').modal('toggle');
    //     }
    //
    // });


JS;

$this->registerJs($js);

$js2 = <<<JS
$('form').on('beforeSubmit', function(){
    var val =$("input[name='RegisterForm[privacy_2]']:checked").val();
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
$this->registerJs($js2);

$this->title = AmosAdmin::t('amosadmin', 'Login');
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var $socialAuthModule \open20\amos\socialauth\Module
 */
$socialAuthModule = Yii::$app->getModule('socialauth');

$socialMatch = Yii::$app->session->get('social-pending');
$socialProfile = Yii::$app->session->get('social-profile');
$communityId = \Yii::$app->request->get('community');
$redirectUrl = \Yii::$app->request->get('redirectUrl');
$moduleEvents = \Yii::$app->getModule('events');
$showOnlyRegionInWizard = null;
if ($moduleEvents) {
    if (!empty($moduleEvents->showOnlyRegionInWizard)) {
        $showOnlyRegionInWizard = $moduleEvents->showOnlyRegionInWizard;
    }
}
if ($userImported) {
    $model->email = $userImported->email;
    if(empty(\Yii::$app->session->get('IDM'))) {
        $model->nome = $userImported->name;
        $model->cognome = $userImported->surname;
    }
    $model->token = $userImported->token;
}

$attrReadonly = false;
if (!empty(\Yii::$app->session->get('IDM'))) {
    $attrReadonly = true;
}
?>

<div id="bk-formDefaultLogin" class="loginContainerFullsize">

    <?php if (!isset(Yii::$app->params['logo']) || !Yii::$app->params['logo']) : ?>
        <p class="welcome-message"><?= AmosAdmin::t('amosadmin', '#login_welcome_message') ?></p>
    <?php endif; ?>


    <?php
    if (empty($userImported)) {
        if ($socialAuthModule && $socialAuthModule->enableSpid) : ?>

                <?php if ($socialAuthModule && $socialAuthModule->enableSpid && empty(\Yii::$app->session->get('IDM'))) : ?>
                    <div class="spid-block col-xs-12 nop">
                        <?= $this->render('parts' . DIRECTORY_SEPARATOR . 'spid'); ?>
                    </div>
                <?php endif; ?>
        <?php endif; ?>
        <?php if ($socialAuthModule && $socialAuthModule->enableLogin && !$socialMatch && !empty($socialAuthModule->enableLink)) : ?>
            <div class="social-block social-register-block col-xs-12 nop">
                <?= $this->render('parts' . DIRECTORY_SEPARATOR . 'social', [
                    'type' => 'register',
                    'communityId' => $communityId,
                    'redirectUrl' => $redirectUrl
                ]); ?>
            </div>

        <?php endif; ?>


        <?php if ($socialProfile) :
            echo Html::tag(
                'div',
                Html::tag(
                    'p',
                    AmosAdmin::t('amosadmin', 'You are right to register using {provider} account', ['provider' => $socialMatch]),
                    ['class' => '']
                ),
                ['class' => 'social-block social-register-block col-xs-12 nop']
            );
        endif;
    }
    ?>

    <?php if (!empty(\Yii::$app->session->get('IDM'))) { ?>
        <div class="col-xs-12 nop login-block registration-block">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <!--        --><?php //echo $form->errorSummary($model)
            ?>
            <div class="login-body">
                <?php if ($userImported) { ?>
                    <?= Html::tag('h1', \Yii::t('app', "<strong>INSERISCI</strong> i tuoi dati per rimanere informato sugli eventi di Regione Lombardia"), ['class' => 'title-login']) ?>
                <?php } else { ?>
                    <?= Html::tag('h1', AmosAdmin::t('amosadmin', '#fullsize_register'), ['class' => 'title-login']) ?>

                <?php } ?>
                <div class="row">
                    <div class="col-xs-12 nop">
                        <div class="col-xs-12">
                            <?= $form->field($model, 'nome')
                                ->textInput([
                                    'placeholder' => AmosAdmin::t('amosadmin', '#fullsize_field_name') . ' (obbligatorio)',
                                    'title' => 'Nome',
                                    'readonly' => $attrReadonly,

                                ])
                                ->label('nome') ?>
                            <?= AmosIcons::show('user', '', AmosIcons::IC) ?>
                        </div>
                        <div class="col-xs-12">
                            <?= $form->field($model, 'cognome')->textInput([
                                'placeholder' => AmosAdmin::t('amosadmin', '#fullsize_field_surname') . ' (obbligatorio)',
                                'readonly' => $attrReadonly,
                                'title' => 'Cognome',
                            ])->label('cognome') ?>
                            <?= AmosIcons::show('user', '', AmosIcons::IC) ?>
                        </div>
                        <div class="col-xs-12">
                            <?= $form->field($model, 'email')->textInput([
                                'placeholder' => AmosAdmin::t('amosadmin', '#fullsize_field_email') . ' (obbligatorio)',
                                'title' => 'Email',
                            ])->label('email') ?>
                            <?= AmosIcons::show('mail', '', AmosIcons::IC) ?>
                        </div>

                        <div class="col-xs-12 m-t-30">
                            <?= Html::tag('h2', AmosAdmin::t('amosadmin', 'Inoltre, se vuoi, completa i seguenti campi per permetterci di inviarti informazioni su eventi in base alle tue caratteristiche:'), ['class' => 'title-login']) ?>
                        </div>
                        <div class="col-xs-12">
                            <?=
                            $form->field(
                                $model,
                                'sesso',
                                [
                                    'template' => "{label}\n{hint}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}",
                                ]
                            )->widget(
                                Select::classname(),
                                [
                                    'options' => [
                                        'placeholder' => AmosAdmin::t('amosadmin', 'Sesso'),
                                        'disabled' => false,
                                        'title' => 'Sesso',
                                    ],
                                    'data' => [
                                        'None' => AmosAdmin::t('site', '#undefinded'),
                                        'Maschio' => AmosAdmin::t('amosadmin', '#man'),
                                        'Femmina' => AmosAdmin::t('amosadmin', '#women')
                                    ]
                                ]
                            )->label(false) ?>
                        </div>
                        <div class="col-xs-12">
                            <?=
                            $form->field(
                                $model,
                                'eta',
                                [
                                    'template' => "{label}\n{hint}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}",
                                ]
                            )->widget(
                                Select::classname(),
                                [
                                    'options' => [
                                        'placeholder' => AmosAdmin::t('amosadmin', 'età'),
                                        'disabled' => false,
                                        'title' => 'Età',
                                    ],
                                    'data' => ArrayHelper::map(
                                        \open20\amos\admin\models\UserProfileAgeGroup::find()->orderBy(['id' => SORT_ASC])->asArray()->all(),
                                        'id',
                                        'age_group'
                                    )
                                ]
                            )->label(false);
                            ?>
                        </div>
                        <div class="col-xs-12">
                            <div class="select">
                                <?php
                                $provinceQuery = AmosAdmin::instance()->createModel('IstatProvince')->find()->orderBy('nome');
                                if (!empty($moduleEvents->showOnlyRegionInWizard)) {
                                    $provinceQuery->andWhere(['istat_regioni_id' => $moduleEvents->showOnlyRegionInWizard]);
                                }
                                ?>
                                <?= $form->field($model, 'provincia')->widget(Select2::classname(), [
                                    'options' => [
                                        'placeholder' => AmosAdmin::t('amosadmin', 'Provincia'),
                                        'id' => 'nascita_province_id-id',
                                        'disabled' => false,
                                        'title' => 'Provincia',
                                    ],
                                    'data' => ArrayHelper::map($provinceQuery->orderBy('nome')->asArray()->all(), 'id', 'nome')
                                ])->label(false); ?>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="select">
                                <?php $comuniQuery = \open20\amos\comuni\models\IstatComuni::find()->andWhere(['istat_province_id' => $model->provincia]);
                                if (!empty($moduleEvents->showOnlyRegionInWizard)) {
                                    $comuniQuery->andWhere(['istat_regioni_id' => $moduleEvents->showOnlyRegionInWizard])
                                        ->orderBy('istat_comuni.nome ASC');
                                }
                                $comuni = $comuniQuery->all();
                                ?>
                                <?= $form->field($model, 'comune')->widget(\kartik\depdrop\DepDrop::classname(), [
                                    'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                                    'data' => $model->provincia ? ArrayHelper::map($comuni, 'id', 'nome') : [],
                                    'options' => [
                                        'id' => 'nascita_comuni_id-id',
                                        'disabled' => false,
                                        'placeholder' => AmosAdmin::t('amosadmin', 'Comune'),
                                        'title' => 'per selezionare il comune impostare prima una provincia',
                                    ],
                                    'select2Options' => [
                                        'pluginOptions' => ['allowClear' => true, 'title' => 'per selezionare il comune impostare prima una provincia'],
                                        'options' => ['title' => 'per selezionare il comune impostare prima una provincia']
                                    ],
                                    'pluginOptions' => [
                                        'depends' => [(false) ?: 'nascita_province_id-id'],
                                        'placeholder' => ['Seleziona ...'],
                                        'title' => 'per selezionare il comune impostare prima una provincia',
                                        'url' => \yii\helpers\Url::to(['/comuni/default/comuni-by-provincia']),
                                        'params' => ['nascita_comuni_id-id'],
                                    ],
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <?= $form->field($model, 'telefono')->textInput([
                                'placeholder' => AmosAdmin::t('amosadmin', 'Telefono'),
                                'title' => 'Telefono',
                            ])->label('') ?>
                            <?= AmosIcons::show('user', '', AmosIcons::IC) ?>
                        </div>

                        <div class="col-xs-12" style="display:none;">
                            <!--                        --><?php //$form->field($model, 'codice_fiscale')->textInput([
                            //                            'placeholder' => AmosAdmin::t('amosadmin', 'Codice fiscale'),
                            //                            'title' => 'Codice fiscale',
                            //                            'readonly' => true
                            //                        ])->label('');
                            //                        ?>
                            <!--                        --><?php //AmosIcons::show('user', '', AmosIcons::IC) ?>

                            <?php $form->field($model, 'codice_fiscale')->hiddenInput([
                                'placeholder' => AmosAdmin::t('amosadmin', 'Codice fiscale'),
                                'title' => 'Codice fiscale',
                                'readonly' => true
                            ])->label('');
                            ?>
                        </div>


                        <div class="col-xs-12">
                            <?= $form->field($model, 'azienda')->textInput([
                                'placeholder' => AmosAdmin::t('amosadmin', 'Azienda'),
                                'title' => 'Azienda',
                            ])->label('') ?>
                            <?= AmosIcons::show('user', '', AmosIcons::IC) ?>
                        </div>
                        <div id="tab-preferencetags" class="col-xs-12 m-t-30">
                            <?php
                            $preferenceCenterTags = EventsUtility::getPreferenceCenterTags();
                            ?>
                            <h3><?= \Yii::t('app', "Tag di interesse informativo") ?></h3>
                            <?php echo Select2::widget([
                                'name' => 'preferencesTags',
                                'data' => ArrayHelper::map($preferenceCenterTags, 'id', 'nome'),
                                'options' => [
                                    'id' => 'preference-tags-select-id',
                                    'placeholder' => \Yii::t('app', "Seleziona i tag ..."),
                                    'multiple' => true,
                                    'title' => 'Tag di interesse informativo',
                                ],
                                'pluginOptions' => ['allowClear' => true]
                            ]) ?>
                            <span class="tooltip-error-field">
                            <span id="error_preference_tag" class="help-block help-block-error"></span>
                        </span>
                            <!--                        --><?php
                            //                        \yii\helpers\Html::checkboxList('preferencesTags', [], EventsUtility::getLabelPreference(), ['encode' => false]);
                            ?>
                            <!--                        <span class="tooltip-error-field">-->
                            <!--                            <span id="error_preference_tag" class="help-block help-block-error"></span>-->
                            <!--                        </span>-->
                        </div>
                        
                        <div class="col-xs-12 h4 m-t-30">
                        <hr class="m-b-30">
                            <?= AmosAdmin::t('amosadmin', "Presa visione dell'informativa al trattamento di dati personali di cui al presente") . " " . Html::a(AmosAdmin::t('amosadmin', "link:"), \Yii::$app->params['platform']['frontendUrl'] . '/it/privacy', ['title' => AmosAdmin::t('amosadmin', '#cookie_policy_title'), 'target' => '_blank']) ?>
                            <?= Html::tag('p', AmosAdmin::t('amosadmin', 'esprimo il consenso al trattamento dei miei dati personali per la finalità indicata alla lettera a) dell’informativa - registrazione alla piattaforma Eventi')) ?>
                            <?= $form->field($model, 'privacy')->radioList([
                                1 => AmosAdmin::t('amosadmin', '#cookie_policy_ok'),
                                0 => AmosAdmin::t('amosadmin', '#cookie_policy_not_ok')
                            ]); ?>
                        </div>
                        <div class="col-xs-12 h4">
                            <?= Html::tag('p', \backend\modules\eventsadmin\utility\EventsAdminUtility::getLabelPrivacy2()) ?>
                            <?= $form->field($model, 'privacy_2')->radioList([
                                1 => AmosAdmin::t('amosadmin', '#cookie_policy_ok'),
                                0 => AmosAdmin::t('amosadmin', '#cookie_policy_not_ok')
                            ]); ?>
                        </div>
                        <div class="col-xs-12 recaptcha"><?= $form->field($model, 'reCaptcha')->widget(\himiklab\yii2\recaptcha\ReCaptcha::className())->label('') ?></div>

                        <?php echo $form->field($model, 'token')->hiddenInput()->label(false); ?>
                        <?php
                        if ($communityId) { ?>
                            <?= Html::hiddenInput('community', $communityId) ?>
                        <?php } else if ($redirectUrl) { ?>
                            <?= Html::hiddenInput('redirectUrl', $redirectUrl) ?>
                        <?php } ?>

                        <?php
                        if ($iuid) { ?>
                            <?= Html::hiddenInput('iuid', $iuid) ?>
                        <?php }

                        ?>
                        <div hidden>
                            <?php echo $form->field($model, 'userSocial')->hiddenInput()->label(false); ?>
                            <?php echo $form->field($model, 'datiRecuperatiDaSocial')->hiddenInput()->label(false); ?>
                            <?php echo $form->field($model, 'socialScelto')->hiddenInput()->label(false); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 action-block">

                <?= Html::submitButton(AmosAdmin::t('amosadmin', '#text_button_register'), ['class' => 'btn btn-primary btn-lg', 'name' => 'login-button', 'title' => AmosAdmin::t('amosadmin', '#text_button_register'), 'data-confirm' => \Yii::t('app', "Sei sicuro di inviare la richiesta?")]) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>



    <?php } ?>


    <?php
    \yii\bootstrap\Modal::begin(['id' => 'modal-privacy']);

    echo Html::tag(
        'div',

        Html::a(AmosAdmin::t('amosadmin', '#cookie_policy_message'), '/site/privacy', ['title' => AmosAdmin::t('amosadmin', '#cookie_policy_title'), 'target' => '_blank']) .
        Html::tag('p', AmosAdmin::t('amosadmin', '#cookie_policy_content')) .
        Html::radioList('privacy', null, [AmosAdmin::t('amosadmin', '#cookie_policy_ok'), AmosAdmin::t('amosadmin', '#cookie_policy_not_ok')], ['class' => 'radio radio-privacy']),
        ['class' => 'text-bottom']
    );

    echo Html::tag(
        'div',

        Html::submitButton(AmosAdmin::t('amosadmin', '#register_now'), ['class' => 'btn btn-primary btn-administration-primary pull-right', 'id' => 'confirm-privacy-button', 'title' => AmosAdmin::t('amosadmin', '#register_now')]) .
        Html::a(AmosAdmin::t('amosadmin', '#go_to_login'), null, ['data-dismiss' => 'modal', 'class' => 'btn btn-secondary pull-left', 'title' => AmosAdmin::t('amosadmin', '#go_to_login_title'), 'target' => '_self'])

    );

    \yii\bootstrap\Modal::end();
    ?>


</div>