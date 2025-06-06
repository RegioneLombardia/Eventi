<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\views\user-profile
 * @category   CategoryName
 */

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\assets\AmosAsset;
use open20\amos\admin\assets\ModuleUserProfileAsset;
use open20\amos\admin\base\ConfigurationManager;
use open20\amos\admin\models\UserProfile;
use open20\amos\admin\rules\ValidateUserProfileWorkflowRule;
use open20\amos\core\forms\ActiveForm;
use open20\amos\core\forms\CloseSaveButtonWidget;
use open20\amos\core\forms\RequiredFieldsTipWidget;
use open20\amos\core\forms\Tabs;
use open20\amos\core\helpers\Html;
use open20\amos\privileges\widgets\UserPrivilegesWidget;
use kartik\alert\Alert;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;

AmosAsset::register($this);
ModuleUserProfileAsset::register($this);

/**
 * @var yii\web\View $this
 * @var open20\amos\admin\models\UserProfile $model
 * @var open20\amos\core\user\User $user
 */

/* @var \open20\amos\cwh\AmosCwh $moduleCwh */
$moduleCwh = \Yii::$app->getModule('cwh');
/** @var  $moduleNotify \open20\amos\notificationmanager\AmosNotify */
$moduleNotify = \Yii::$app->getModule('notify');

/** @var AmosAdmin $adminModule */
$adminModule = AmosAdmin::instance();

// Tabs ids
$idTabCard = 'tab-card';
$idTabInsights = 'tab-insights';
$idTabNetwork = 'tab-network';
$idTabSettings = 'tab-settings';
$idTabAdministration = 'tab-administration';
$idTabNotifications = 'tab-notifications';
$idTabPreferenceTags = 'tab-preferencetags';

$defaultFacilitatorId = Html::getInputId($model, 'default_facilitatore');

/** @var \open20\amos\core\user\User $loggedUser */
$loggedUser = Yii::$app->user->identity;
/** @var UserProfile $loggedUserProfile */
$loggedUserProfile = $loggedUser->getProfile();
//$ajaxUrl = Url::to(['/admin/user-profile/is-default-facilitator', 'id' => $loggedUserProfile->id]);
$ajaxUrl = Url::to(['/admin/user-profile/def-facilitator-present', 'id' => $model->id]);
$js = "
var isPreviousChecked = $('#$defaultFacilitatorId').is(':checked');
var isProfileModified = 0;

$('#$defaultFacilitatorId').on('change', function(event) {
    var isChecked = $(this).is(':checked');
    var ajaxData = {
        isChecked: isChecked
    };
    var errorMsg = '';
    $.ajax({
        url: '$ajaxUrl',
        type: 'post',
        dataType: 'json',
        success: function (response) {
            if (response.defaultFaciliatorPresent) {
                if (" . $loggedUserProfile->id . " == " . $model->id . ") {
                    if (isPreviousChecked && !isChecked) {
                        var ok = confirm('" . AmosAdmin::t('amosadmin', 'Attention! There will no longer be any default facilitator. Confirm?') . "');
                        $('#$defaultFacilitatorId').prop('checked', !ok);
                        isPreviousChecked = !ok;
                    } else if (!isPreviousChecked && isChecked) {
                        isPreviousChecked = true;
                    }
                } else {
                    if (!isPreviousChecked && isChecked) {
                        $('#$defaultFacilitatorId').prop('checked', false);
                        alert('" . AmosAdmin::t('amosadmin', 'Operation impossible because') . " ' + response.facilitatorNameSurname + ' " . AmosAdmin::t('amosadmin', 'is already referred to as default facilitator.') . "');
                    }
                }
            } else {
                if (isPreviousChecked && !isChecked) {
                    var ok = confirm('" . AmosAdmin::t('amosadmin', 'Attention! There will no longer be any default facilitator. Confirm?') . "');
                    $('#$defaultFacilitatorId').prop('checked', !ok);
                    isPreviousChecked = !ok;
                } else if (!isPreviousChecked && isChecked) {
                    isPreviousChecked = true;
                }
            }
        },
        error: function() {
            alert('" . AmosAdmin::t('amosadmin', 'AJAX error while checking default facilitator presence') . "');
        }
    });
});

";

$jsIsProfileModified = "
    $('#card-section, #tab-insights, #amos-cwh-tag').on('change', 'input, select, textarea', function(e) {
        isProfileModified = 1;
        $('#isProfileModified').val('1');
    });
    
    $('.saveBtn').on('click', function(e) {
        e.preventDefault();
        $('.has-error').each(function() {
            $(this).removeClass('has-error');
        });
        $('.help-block-error').each(function() {
            $(this).html('');
        });
        var required = $('.required input, .required textarea');
        var haserror = $('.has-error');
        var haserrorRequired = false;
        $(required).each(function() {      
            if ($(this).val().length <= 0) {
                haserrorRequired = true;
            } 
        });
        if (haserror.length <= 0 && haserrorRequired) {
            $('#user-profile-form').yiiActiveForm('submitForm');
        }
        if (haserror.length <= 0 && !haserrorRequired) {
            if (isProfileModified == 1) {
                $(modalId).modal('show'); 
            } else {
                $('#user-profile-form').yiiActiveForm('submitForm');
            }
        } else {
            if (haserror.length) {
                $('html, body').animate({
                    scrollTop: $('.has-error').first().offset().top
                }, 1000);
            }
        }
        return false;
    });
";

if ($model->id) {
    $this->registerJs($js, View::POS_READY);
    $workflowInitialStatusId = $model->getWorkflowSource()->getWorkflow(UserProfile::USERPROFILE_WORKFLOW)->getInitialStatusId();
    $validatorUserIds = $model->getValidatorUsersId();
    if (
        !$adminModule->disableUpdateChangeStatus &&
        (!Yii::$app->user->can('ADMIN') && !Yii::$app->user->can('AMMINISTRATORE_UTENTI')) &&
        !in_array(Yii::$app->user->id, $validatorUserIds) &&
        ($model->status == UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED) &&
        ($model->status != $workflowInitialStatusId)
    ) {
        $this->registerJs($jsIsProfileModified, View::POS_READY);
    }
}
$enableUserContacts = $adminModule->enableUserContacts;
$enableExternalFacilitator = $adminModule->enableExternalFacilitator;
$userCanChangeWorkflow = Yii::$app->user->can('CHANGE_USERPROFILE_WORKFLOW_STATUS');

?>
<?php
$js2 = <<<JS
$('form').on('beforeSubmit', function(){
    var val =$("input[name='UserProfile[privacy_2]']:checked").val();
         $('#error_preference_tag').html('');
         $('.field-userprofile-privacy_2 .help-block-error').html('');
        if (val == '1') {
            var selected = $('input[name="preferencesTags[]"]:checked').val();
            if(selected === undefined || selected === null || selected.length === 0){
                $('.field-userprofile-privacy_2 .help-block-error').html('E\' necessario selezionare almeno un tag di interesse informativo');
                $('#error_preference_tag').html('Tag di interesse informativo non può essere vuoto');
                $('button[type="submit"]').removeAttr('disabled');
                return false;
            }
            
        }
        return true;
});
JS;
$this->registerJs($js2);
?>

<?php
$form = ActiveForm::begin([
    'options' => [
        'id' => 'user-profile-form',
        'data-fid' => (isset($fid)) ? $fid : 0,
        'data-field' => ((isset($dataField)) ? $dataField : ''),
        'data-entity' => ((isset($dataEntity)) ? $dataEntity : ''),
//        'class' => 'default-form col-xs-12 nop',
        'enctype' => 'multipart/form-data' // important
    ]
]);
?>


<?php if ($userCanChangeWorkflow) { ?>
    <?= \open20\amos\workflow\widgets\WorkflowTransitionStateDescriptorWidget::widget([
        'form' => $form,
        'model' => $model,
        'workflowId' => UserProfile::USERPROFILE_WORKFLOW,
        'classDivMessage' => 'message',
        'viewWidgetOnNewRecord' => false
    ]); ?>
<?php } ?>

<div class="loading" id="loader" hidden></div>
<div class="user-form">
    <div id="card-section" class="row">
        <div class="col-xs-12">
            <?= Html::tag('h2', AmosAdmin::t('amosadmin', 'Informazioni anagrafiche'), ['class' => 'subtitle-form']) ?>
        </div>

        <div class="col-md-8 col-xs-12">
            <?php if ($adminModule->confManager->isVisibleBox('box_custom_general_information_begin', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_custom_general_information_begin', ['form' => $form, 'model' => $model, 'user' => $user, 'idTabInsights' => $idTabInsights]); ?>
            <?php endif; ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_informazioni_base', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_informazioni_base', ['form' => $form, 'model' => $model, 'user' => $user, 'idTabInsights' => $idTabInsights]); ?>
            <?php endif; ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_dati_contatto', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_dati_contatto', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
            <?php endif; ?>

            <?= $this->render('boxes/box_azienda', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
            <?php if (!$model->isNewRecord && \Yii::$app->authManager->checkAccess($model->user_id, 'SUPER_USER_EVENT')) { ?>
                <?php if (\Yii::$app->authManager->checkAccess($model->user_id, 'EVENT_SOCIAL')) {
                    $value_receive_pubblication_emails = true;
                } else {
                    $value_receive_pubblication_emails = false;
                } ?>
                <div class="col-xs-12">
                    <?php echo \yii\helpers\Html::checkbox('receive_pubblication_emails', $value_receive_pubblication_emails, [
                        'label' => \Yii::t('app', "Abilita per ricevere una notifica alla pubblicazione di un evento")
                    ]) ?>
                </div>
            <?php } ?>

            <?php if ($adminModule->confManager->isVisibleBox('box_custom_general_information_end', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_custom_general_information_end', ['form' => $form, 'model' => $model, 'user' => $user, 'idTabInsights' => $idTabInsights]); ?>
            <?php endif; ?>
        </div>
        <div class="col-md-4 col-xs-12">
            <?php if ($adminModule->confManager->isVisibleBox('box_foto', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_foto', [
                    'form' => $form,
                    'model' => $model,
                    'user' => $user
                ]); ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- BOX SAVE CONFIRM -->
    <!--    <div class="row m-t-15 m-b-5">-->
    <!--        <div class="col-xs-12">-->
    <!--            <div class="info-box-bordered">-->
    <!--                < ?= AmosAdmin::t('amosadmin', '#box_save', [-->
    <!--                    'link' => 'test'-->
    <!--                ]) ?>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

    <!-- FACILITATORE -->
    <div class="row">
        <div class="col-xs-12">
            <?php if ($adminModule->confManager->isVisibleBox('box_facilitatori', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?= $this->render('boxes/box_facilitatori', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php if ($enableExternalFacilitator) { ?>
                    <?= $this->render('boxes/box_external_facilitator', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php } ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- TABS -->
    <?= Html::tag('h2', AmosAdmin::t('amosadmin', '#settings_details_title'), ['class' => 'subtitle-form m-b-0']) ?>

    <div class="wrap-single-tabs row">
        <div class="col-xs-12">
            <!-- APPROFONDIMENTI -->
            <?php $this->beginBlock($idTabInsights); ?>
            <div>
                <?php if ($adminModule->confManager->isVisibleBox('box_custom_insights_begin', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                    <?= $this->render('boxes/box_custom_insights_begin', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php endif; ?>

                <?php if ($adminModule->confManager->isVisibleBox('box_presentazione_personale', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                    <?= $this->render('boxes/box_presentazione_personale', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php endif; ?>

                <?php if ($adminModule->confManager->isVisibleBox('box_role_and_area', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                    <?= $this->render('boxes/box_role_and_area', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php endif; ?>

                <?php if ($adminModule->confManager->isVisibleBox('box_ulteriori_informazioni', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                    <?= $this->render('boxes/box_ulteriori_informazioni', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php endif; ?>

                <?php if ($adminModule->confManager->isVisibleBox('box_dati_residenza', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                    <?= $this->render('boxes/box_dati_residenza', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php endif; ?>

                <?php if ($adminModule->confManager->isVisibleBox('box_dati_nascita', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                    <?= $this->render('boxes/box_dati_nascita', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php endif; ?>

                <?php if ($adminModule->confManager->isVisibleBox('box_dati_fiscali_amministrativi', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                    <?= $this->render('boxes/box_dati_fiscali_amministrativi', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php endif; ?>

                <?php if ($adminModule->confManager->isVisibleBox('box_custom_insights_end', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                    <?= $this->render('boxes/box_custom_insights_end', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php endif; ?>
            </div>
            <div class="col-md-4 col-xs-12">
                <?php if ($adminModule->confManager->isVisibleBox('box_prevalent_partnership', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                    <?= $this->render('boxes/box_prevalent_partnership', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php endif; ?>
            </div>
            <?php $this->endBlock(); ?>

            <?php
            $itemsTab[] = [
                'label' => AmosAdmin::t('amosadmin', 'Insights'),
                'content' => $this->blocks[$idTabInsights],
                'options' => ['id' => $idTabInsights],
                'linkOptions' => ['role' => 'tab', 'aria-controls' => $idTabInsights]

            ];
            ?>

            <!-- NETWORK -->
            <?php if ($model->isNewRecord || ($enableUserContacts && $model->validato_almeno_una_volta) || isset($moduleCwh)): ?>
                <?php $this->beginBlock($idTabNetwork); ?>
                <div>
                    <?php if ($model->isNewRecord): ?>
                        <?= Alert::widget([
                            'type' => Alert::TYPE_WARNING,
                            'body' => AmosAdmin::t('amosadmin', "Prima di poter gestire la rete &egrave; necessario salvare l'utente."),
                            'closeButton' => false
                        ]); ?>
                    <?php else: ?>
                        <?php
                        if ($enableUserContacts && $model->validato_almeno_una_volta) {
                            echo \open20\amos\admin\widgets\UserContacsWidget::widget([
                                'userId' => $model->user_id,
                                'isUpdate' => true
                            ]);
                        }
                        if (isset($moduleCwh)) {
                            echo \open20\amos\cwh\widgets\UserNetworkWidget::widget([
                                'userId' => $model->user_id,
                                'isUpdate' => true
                            ]);
                        }
                        ?>
                    <?php endif; ?>
                </div>
                <?php $this->endBlock(); ?>
                <?php
                /**
                 * $itemsTab[] = [
                 * 'label' => AmosAdmin::t('amosadmin', 'Network'),
                 * 'content' => $this->blocks[$idTabNetwork],
                 * 'options' => ['id' => $idTabNetwork]
                 * ];
                 **/ ?>
            <?php endif; ?>

            <!-- SETTINGS -->
            <?php if (Yii::$app->user->can('ADMIN') || Yii::$app->user->can('AMMINISTRATORE_UTENTI') || Yii::$app->user->can('UpdateOwnUserProfile', ['model' => $loggedUserProfile])): ?>
                <?php $this->beginBlock($idTabSettings); ?>
                <?php if ($model->isNewRecord): ?>
                    <div class="col-xs-12">
                        <?= Alert::widget([
                            'type' => Alert::TYPE_WARNING,
                            'body' => AmosAdmin::t('amosadmin', 'Prima di poter gestire le classi di utenza &egrave; necessario salvare l\'utente.'),
                            'closeButton' => false
                        ]); ?>
                    </div>
                <?php else: ?>
                    <div>
                        <?php if ($adminModule->confManager->isVisibleBox('box_dati_accesso', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                            <?= $this->render('boxes/box_access_data', ['form' => $form, 'model' => $model, 'user' => $user, 'spediscicredenzialienable' => isset($spediscicredenzialienable) ? $spediscicredenzialienable : true]); ?>
                        <?php endif; ?>

                        <?php /*if ($adminModule->confManager->isVisibleBox('box_dati_accesso', ConfigurationManager::VIEW_TYPE_FORM)): */ ?><!--
                            < ?= $this->render('boxes/box_dati_accesso', ['form' => $form, 'model' => $model, 'user' => $user, 'spediscicredenzialienable' => isset($spediscicredenzialienable) ? $spediscicredenzialienable : true]); ?>
                        --><?php /*endif; */ ?>

                        <?php /*if ($adminModule->confManager->isVisibleBox('box_account_data', ConfigurationManager::VIEW_TYPE_FORM)): */ ?><!--
                            < ?= $this->render('boxes/box_account_data', ['form' => $form, 'model' => $model, 'user' => $user, 'spediscicredenzialienable' => isset($spediscicredenzialienable) ? $spediscicredenzialienable : true]); ?>
                        --><?php /*endif; */ ?>

                        <?php if ($adminModule->confManager->isVisibleBox('box_questio', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                            <?= $this->render('boxes/box_questio', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                        <?php endif; ?>

                        <?php if (Yii::$app->user->can('ADMIN') && $model->isFacilitator() && $adminModule->confManager->isVisibleField('default_facilitatore', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                            <div class="col-xs-12 nop">
                                <?= $form->field($model, 'default_facilitatore')->checkbox()->label(AmosAdmin::t('amosadmin', 'Is default facilitator') . '?') ?>
                            </div>
                        <?php endif; ?>
                        <?php if (Yii::$app->user->can('ADMIN') && $adminModule->showFacilitatorForModuleSelect) : ?>
                            <?= $this->render('boxes/box_facilitator_roles', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                        <?php endif; ?>
<!--                        --><?php //if ($adminModule->confManager->isVisibleBox('box_social_account', ConfigurationManager::VIEW_TYPE_FORM)): ?>
<!--                            --><?php //$this->render('boxes/box_social_account', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
<!--                        --><?php //endif; ?>
                    </div>

                <?php endif; ?>
                <?php $this->endBlock(); ?>
                <?php
                $itemsTab[] = [
                    'label' => AmosAdmin::t('amosadmin', 'Settings'),
                    'content' => $this->blocks[$idTabSettings],
                    'options' => ['id' => $idTabSettings],
                    'linkOptions' => ['role' => 'tab', 'aria-controls' => $idTabSettings]

                ];
                ?>
            <?php endif; ?>

            <!-- NOTIFICATION -->
            <?php if ($adminModule->confManager->isVisibleBox('box_email_frequency', ConfigurationManager::VIEW_TYPE_FORM)): ?>
                <?php if (isset($moduleCwh) && isset($moduleNotify)) { ?>
                    <?php $this->beginBlock($idTabNotifications); ?>
                    <div>
                        <h2><?= \Yii::t('app', "Configura notifiche")?></h2>
                        <p class="m-b-20"><?=  \Yii::t('app', "Configura le notifiche che desideri ricevere relative alla pubblicazione di un contenuto nelle community di evento") ?></p>

                        <?php if ($model->isNewRecord) { ?>
                            <?= Alert::widget([
                                'type' => Alert::TYPE_WARNING,
                                'body' => AmosAdmin::t('amosadmin', "Prima di poter gestire le notifiche &egrave; necessario salvare l'utente."),
                                'closeButton' => false
                            ]); ?>
                        <?php } else { ?>
                            <?= $this->render('/user-profile/boxes/box_event_notification', ['form' => $form, 'model' => $model, 'user' => $user,'moduleCwh' => $moduleCwh]); ?>
                        <?php } ?>
                    </div>
                    <?php $this->endBlock(); ?>
                    <?php
                    $itemsTab[] = [
                        'label' => AmosAdmin::t('amosadmin', 'Notifications'),
                        'content' => $this->blocks[$idTabNotifications],
                        'options' => ['id' => $idTabNotifications],
                        'linkOptions' => ['role' => 'tab', 'aria-controls' => $idTabNotifications]

                    ];
                    ?>
                <?php } ?>
            <?php endif; ?>

            <?php $this->beginBlock($idTabPreferenceTags); ?>

            <?php echo $this->render('_preferences_tags', ['model' => $model, 'form' => $form]); ?>
            <?php $this->endBlock(); ?>
            <?php
            $itemsTab[] = [
                'label' => AmosAdmin::t('amosadmin', 'Tag di interesse informativo'),
                'content' => $this->blocks[$idTabPreferenceTags],
                'options' => ['id' => $idTabPreferenceTags],
                'linkOptions' => ['role' => 'tab', 'aria-controls' => $idTabPreferenceTags]
            ];
            ?>


            <!-- ADMIN -->
            <?php if (Yii::$app->user->can('PRIVILEGES_MANAGER')): ?>
                <?php $privilegesModule = Yii::$app->getModule('privileges'); ?>
                <?php if (!empty($privilegesModule)): ?>
                    <?php $this->beginBlock($idTabAdministration); ?>
                    <?php if ($model->isNewRecord): ?>
                        <?= Alert::widget([
                            'type' => Alert::TYPE_WARNING,
                            'body' => AmosAdmin::t('amosadmin', 'Prima di poter gestire le classi di utenza &egrave; necessario salvare l\'utente.'),
                            'closeButton' => false
                        ]) ?>
                    <?php else: ?>
                        <?= UserPrivilegesWidget::widget(['userId' => $model->user_id]) ?>
                    <?php endif; ?>
                    <?php $this->endBlock(); ?>
                    <?php
                    $itemsTab[] = [
                        'label' => AmosAdmin::t('amosadmin', 'Administration'),
                        'content' => $this->blocks[$idTabAdministration],
                        'options' => ['id' => $idTabAdministration,  'role' => 'tab', 'aria-controls' => $idTabAdministration],
                    ];
                    ?>
                <?php endif; ?>
            <?php endif; ?>

            <?php
            if (isset($tabActive)) {
                foreach ($itemsTab as $key => $tab) {
                    if ($tab['options']['id'] == $tabActive) {
                        $itemsTab[$key]['active'] = true;
                    }
                }
            }
            ?>
            <?= Tabs::widget([
                'encodeLabels' => false,
                'items' => $itemsTab,
//                'hideTagsTab' => !Yii::$app->user->can('FORM_TAG_TABS_PERMISSION')
                'hideTagsTab' => true
                /* 'clientEvents' => [
                  'shown.bs.tab' => new JsExpression('reloadGoogleMaps')
                  ] */
            ]); ?>
        </div>
    </div>


    <div class="row">
        <?php if ($adminModule->confManager->isVisibleBox('box_privacy', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <!-- ADMIN cannot check the privacy checkbox of other users -->
            <?php if ((\Yii::$app->user->can('ADMIN') || \Yii::$app->user->can('AMMINISTRATORE_UTENTI')) && ($user->id !== \Yii::$app->user->id)): ?>
                <div class='col-xs-12 form-group'><strong><?= $model->getAttributeLabel('privacy'); ?>
                        :
                    </strong><?= \Yii::$app->formatter->asBoolean($model->privacy); ?>
                    <br><strong><?= \Yii::t('app', "Privacy accettata il") . ': ' ?></strong><?= \Yii::$app->formatter->asDate($model->date_privacy) ?>
                </div>
            <?php else: ?>
                <?php if (!$model->privacy) { ?>
                    <?= $this->render('boxes/box_privacy', ['form' => $form, 'model' => $model, 'user' => $user]); ?>
                <?php } ?>
            <?php endif; ?>
        <?php endif; ?>

    </div>
    <div class="row">
        <?php if ((\Yii::$app->user->can('ADMIN') || \Yii::$app->user->can('AMMINISTRATORE_UTENTI')) && ($user->id !== \Yii::$app->user->id)) { ?>
            <div class='col-xs-12 form-group'><strong><?= \Yii::t('app', "Privacy 2") . ":" ?>
                </strong><?= \Yii::$app->formatter->asBoolean($model->privacy_2); ?>
            </div>
        <?php } else { ?>
            <div class="col-xs-12">
                <?php
                echo $form->field($model, 'privacy_2')->checkbox()->label(\backend\modules\eventsadmin\utility\EventsAdminUtility::getLabelPrivacy2());
                ?>
            </div>
        <?php } ?>
    </div>

    <div class="row">
        <?= RequiredFieldsTipWidget::widget(['containerClasses' => 'col-xs-12 note_asterisk']) ?>
        <?= $form->field($model, 'tipo_utente')->hiddenInput()->label('AmosHidden') ?>
        <!--        <div class="col-xs-12">< ?= CreatedUpdatedWidget::widget(['model' => $model]) ?></div>-->
        <?= $form->field($model, 'isProfileModified')->hiddenInput(['id' => 'isProfileModified', 'value' => '0'])->label(false) ?>
    </div>

    <?php if ($userCanChangeWorkflow) { ?>
        <?php

        $saveBtn = Html::submitButton(AmosAdmin::t('amosadmin', 'Salva'), ['class' => 'btn btn-workflow']);
        $draftButtons = [
            UserProfile::USERPROFILE_WORKFLOW_STATUS_TOVALIDATE => [
                'button' => $saveBtn,
                'description' => '',
            ],
            UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED => [
                'button' => $saveBtn,
                'description' => '',
            ],
        ];

        $statusToRender = [
            UserProfile::USERPROFILE_WORKFLOW_STATUS_DRAFT => '',
            UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED => ''
        ];
        if (Yii::$app->user->can('ADMIN') || Yii::$app->user->can('AMMINISTRATORE_UTENTI') || Yii::$app->user->can(ValidateUserProfileWorkflowRule::className(), ['model' => $model])) {
            $hideDraftStatuses = false;
            $hideDraftStatus = [];
            $draftButtons['default'] = [
                'button' => $saveBtn,
                'description' => '',
            ];
        } else {
            if ($model->status == UserProfile::USERPROFILE_WORKFLOW_STATUS_TOVALIDATE) {
                $hideDraftStatus[] = UserProfile::USERPROFILE_WORKFLOW_STATUS_TOVALIDATE;
            } else {
                if ($model->status == UserProfile::USERPROFILE_WORKFLOW_STATUS_DRAFT) {
                    $draftButtons['default'] = [
                        'button' => $saveBtn,
                        'description' => AmosAdmin::t('amosadmin', ''),
                    ];
                }
            }
            $statusToRender = ArrayHelper::merge($statusToRender, [
                UserProfile::USERPROFILE_WORKFLOW_STATUS_DRAFT => ''
            ]);
            $hideDraftStatuses = true;
            $hideDraftStatus[] = UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED;
        }

        ?>
        <div class="row">
            <?php

            if (Yii::$app->user->can('ADMIN')
                || Yii::$app->user->can('AMMINISTRATORE_UTENTI')
                || Yii::$app->user->can(ValidateUserProfileWorkflowRule::className(), ['model' => $model])
                || $adminModule->disableSendValidationRequestAuto
            ) {
                echo \open20\amos\workflow\widgets\WorkflowTransitionButtonsWidget::widget([
                    // parametri ereditati da verioni precedenti del widget WorkflowTransition
                    'form' => $form,
                    'model' => $model,
                    'workflowId' => UserProfile::USERPROFILE_WORKFLOW,
                    'viewWidgetOnNewRecord' => true,

                    'closeButton' => Html::a(AmosAdmin::t('amosadmin', 'Annulla'), !empty(\Yii::$app->request->referrer) ? \Yii::$app->request->referrer : '/admin/user-profile/index', ['class' => 'btn btn-secondary']),

                    // fisso lo stato iniziale per generazione pulsanti e comportamenti
                    // "fake" in fase di creazione (il record non e' ancora inserito nel db)
                    'initialStatusName' => explode('/', $model->getWorkflowSource()->getWorkflow(UserProfile::USERPROFILE_WORKFLOW)->getInitialStatusId())[1],
                    'initialStatus' => $model->getWorkflowSource()->getWorkflow(UserProfile::USERPROFILE_WORKFLOW)->getInitialStatusId(),
                    // Stati da renderizzare obbligatoriamente in fase di creazione (quando il record non e' ancora inserito nel db)
                    'statusToRender' => $statusToRender,

                    'hideSaveDraftStatus' => $hideDraftStatus,

                    'draftButtons' => $draftButtons
                ]);
            } else {
                echo \open20\amos\workflow\widgets\WorkflowTransitionSimplifiedButtonsWidget::widget([
                    'form' => $form,
                    'model' => $model,
                    'workflowId' => UserProfile::USERPROFILE_WORKFLOW,
                    'viewWidgetOnNewRecord' => true,

                    'closeButton' =>  Html::a(AmosAdmin::t('amosadmin', 'Annulla'), !empty(\Yii::$app->request->referrer) ? \Yii::$app->request->referrer : '/admin/user-profile/index', ['class' => 'btn btn-secondary']),
                    'transitionStatuses' => [
                        UserProfile::USERPROFILE_WORKFLOW_STATUS_DRAFT => UserProfile::USERPROFILE_WORKFLOW_STATUS_TOVALIDATE,
                        UserProfile::USERPROFILE_WORKFLOW_STATUS_NOTVALIDATED => UserProfile::USERPROFILE_WORKFLOW_STATUS_TOVALIDATE,
                    ],
                ]);
            }
            ?>
        </div>
    <?php } else {
        $closeSaveOptions = [
            'model' => $model,
            'buttonClassSave' => 'btn btn-navigation-primary saveBtn',
            'urlClose' => !empty(\Yii::$app->request->referrer) ? \Yii::$app->request->referrer : '/admin/user-profile/index',
        ];
        ?>
        <?= CloseSaveButtonWidget::widget($closeSaveOptions); ?>

    <?php } ?>

    <?php
    if (!$model->isNewRecord) {
        \yii\bootstrap\Modal::begin(['id' => 'modalId']);
        echo Html::tag('div', AmosAdmin::t('amosadmin', 'Salva le modifiche e rimani in attesa di essere validato'));
        echo Html::tag('div',
            Html::submitButton(AmosAdmin::t('amosadmin', 'Ok'),
                ['class' => 'btn btn-navigation-primary', 'id' => $model->getWorkflowStatus()->getId()])
            . Html::a(AmosAdmin::t('amosadmin', 'Annulla'), null, ['data-dismiss' => 'modal', 'class' => 'btn btn-secondary']),
            ['class' => 'pull-right m-15-0']
        );
        \yii\bootstrap\Modal::end();
    }
    ?>

</div>

<?php ActiveForm::end(); ?>
