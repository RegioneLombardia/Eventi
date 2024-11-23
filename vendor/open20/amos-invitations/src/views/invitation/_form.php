<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\invitations
 * @category   CategoryName
 */

use open20\amos\core\forms\ActiveForm;
use open20\amos\core\forms\CloseSaveButtonWidget;
use open20\amos\core\forms\Tabs;
use open20\amos\core\forms\TextEditorWidget;
use open20\amos\invitations\Module;

/**
 * @var yii\web\View $this
 * @var open20\amos\invitations\models\Invitation $invitation
 * @var open20\amos\invitations\models\InvitationUser $invitationUser
 * @var yii\widgets\ActiveForm $form
 */

$email = $invitationUser->email;
$js = <<<JS

function checkEmail(email){
  $.get("/invitations/invitation/check-email-ajax", { email: email } )
    .done(function( data ) { 
        data = $.parseJSON(data);
        if(data.success) {
            $('#check-email').html(data.message);
            if (data.message == '') {
                $('#check-email').hide();
            } else {       
                $('#check-email').show();
            }
            $('#info-content').html(data.messageConfirm);
        }
    });
}
checkEmail('$email');

$("#send-invitation").prop("type", "button");
$('#email').change(function(e) {
    e.preventDefault();
    checkEmail( $(this).val());
    return true;
});

$( "#my-form" ).submit(function( event ) {
  $('#my-modal').modal('hide');
});

JS;
$this->registerJs($js);
?>


<div class="invitation-form col-xs-12 nop">
    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'my-form'
        ]
    ]); ?>

    <?php $this->beginBlock('default'); ?>

    <div class="col-lg-6 col-sm-6">
        <?= $form->field($invitation, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-6 col-sm-6">
        <?= $form->field($invitation, 'surname')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-12 col-sm-12">
        <?= $form->field($invitationUser, 'email')->textInput(['id' => 'email']) ?>
    </div>

    <div class="col-lg-12 col-sm-12 alert alert-warning" style="display: none;" id="check-email"></div>

    <div class="col-lg-12 col-sm-12">
    <?= $form->field($invitation, 'message')->widget(TextEditorWidget::className(), [
        'clientOptions' => [
            'placeholder' => Module::t('amosinvitations', '#message_field_placeholder'),
            'lang' => substr(Yii::$app->language, 0, 2)
        ]
    ]) ?>
    </div>

<div class="clearfix"></div>

<?php $this->endBlock(); ?>

<?php 
$itemsTab[] = [
    'label' => Yii::t('amosinvitations', 'Default'),
    'content' => $this->blocks['default'],
];

try {
    echo Tabs::widget(
        [
            'encodeLabels' => false,
            'items' => $itemsTab
        ]
    );
} catch (Exception $e) {
    ;
}
?>

<!-- Modal -->
<div class="modal fade" id="my-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="myModalLabel"><?= Yii::t('amosinvitations', 'Confirm send invitation') ?></h4>
            </div>
            <div class="modal-body" id="info-content"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary undo-edit"
                        data-dismiss="modal"><?= Yii::t('amoscore', 'Annulla') ?></button>
                <button type="submit"
                        class="btn btn-primary"><?= Yii::t('amosinvitations', 'Send invitation') ?></button>
            </div>
        </div>
    </div>
</div>

<?php
try {
    echo CloseSaveButtonWidget::widget([
        'model' => $invitation,
        'buttonNewSaveLabel' => Yii::t('amosinvitations', 'Send invitation'),
        'buttonSaveLabel' => Yii::t('amosinvitations', 'Send invitation'),
        'dataToggle' => 'modal',
        'dataTarget' => '#my-modal',
        'buttonId' => 'send-invitation',

    ]);
} catch (Exception $e) {
    ;
}
?>

<?php ActiveForm::end(); ?>
</div>
