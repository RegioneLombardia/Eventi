<?php

use open20\amos\events\AmosEvents;
use open20\amos\events\assets\WizardEventAsset;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use open20\amos\events\widgets\EventNominalInvitationWidget;

$this->title = AmosEvents::t('amosevents', 'Crea un nuovo evento');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;

$wizardAsset = WizardEventAsset::register($this);
$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
$isOperatorAdminDg = \open20\amos\events\utility\EventsUtility::isUserOperatorAdministratorDg();
$showAllUsers = true;
if($isOperatorAdminDg){
    $showAllUsers  = false;
}

?>

<div class="heading border-bottom mb-4 pb-2">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="mb-0 pr-2 text-muted"><?= (empty(trim($this->title)) ?: $this->title) ?></h3>
        </div>
        <div>
            <div class="step">3</div>
        </div>

    </div>
</div>
<?= EventNominalInvitationWidget::widget([
    'model' => $model,
    'internalList' => $internalList,
    'showAllUsers' => $showAllUsers
]) ?>
