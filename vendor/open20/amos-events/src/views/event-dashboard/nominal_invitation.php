<?php

use open20\amos\events\AmosEvents;
use open20\amos\events\assets\WizardEventAsset;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use open20\amos\events\widgets\EventNominalInvitationWidget;


$this->title = AmosEvents::t('amosevents', 'Selezione nominale');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;

$wizardAsset = WizardEventAsset::register($this);
$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
$isOperatorAdminDg = \open20\amos\events\utility\EventsUtility::isUserOperatorAdministratorDg();
$showAllUsers = true;
if(!$isOperatorAdminDg){
    $showAllUsers  = false;
}
?>


<?= EventNominalInvitationWidget::widget([
    'model' => $model,
    'internalList' => $internalList,
    'communication' => $communication,
    'showAllUsers' => $showAllUsers

]) ?>
