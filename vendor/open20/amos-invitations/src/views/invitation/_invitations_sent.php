<?php

use open20\amos\core\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var $model \open20\amos\invitations\models\Invitation
 **/

$moduleName = null;
$contextModelId = null;

$moduleName = $model->module_name;
$contextModelId = $model->context_model_id ;
 ?>

<?php $form = \open20\amos\core\forms\ActiveForm::begin()?>
    <div class="col-lg-12">
        <?php echo "<strong>" . \open20\amos\invitations\Module::t('amosinvitations', 'Name and surname:') . '</strong> ' . $model->nameSurname ?>
    </div>
    <div class="col-lg-12">
        <?php echo "<strong>" . \open20\amos\invitations\Module::t('amosinvitations', 'Invitations sent:') . '</strong> ' . $model->invitationUser->numberNotificationSended;?>
    </div>
    <div class="col-lg-12">
        <?php
        /** @var  $lastInvitationUser \open20\amos\invitations\models\InvitationUser */
        $invitationUser = open20\amos\invitations\models\InvitationUser:: getInvitationUserFromEmail($model->invitationUser->email);
        $invitation = $invitationUser->getInvitations()->orderBy('send_time DESC')->one()
        ?>
        <?php echo "<strong>" . \open20\amos\invitations\Module::t('amosinvitations', 'Last invitation sent at:') . '</strong> ' . \Yii::$app->formatter->asDatetime($invitation->send_time) ?>
    </div>
    <div>
        <?= Html::a( \open20\amos\invitations\Module::t('amosinvitations', 'Re-send') , [
            '/invitations/invitation/re-send',
            'id' => $model->id,
            'moduleName' => $moduleName,
            'contextModelId' => $contextModelId
        ],['class' => 'btn btn-primary pull-right'])?>
        <?php \open20\amos\core\forms\CloseSaveButtonWidget::widget(['model' => $model, 'buttonId' => 're-send-button', 'buttonSaveLabel' => \open20\amos\invitations\Module::t('amosinvitations', 'Send')]); ?>
    </div>
<?php \open20\amos\core\forms\ActiveForm::end(); ?>
