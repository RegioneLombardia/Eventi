<?php 
use open20\amos\events\assets\WizardEventAsset;
$wizardAsset = WizardEventAsset::register($this);

$webmeeting = $model->webMeetingWebex;

$textError = \open20\amos\events\AmosEvents::t('amosevents', "E' possibile modificare gli invitati solamente fino ad un'ora prima dell'inizio del meeting");

if (!empty($webmeeting)) {
    $error = \open20\amos\events\utility\EventsUtility::errorsUpdateWebmeeting($webmeeting);
    if(!empty($error)){
        $textError = $error;
    }
}else {
    $textError = \open20\amos\events\AmosEvents::t('amosevents', "Attezione! E' necessario creare il meeting prima di procedere con la selezione degli invitati.");
}


if($model->status != \open20\amos\events\models\Event::EVENTS_WORKFLOW_STATUS_PUBLISHED){
    $textError  ='';
}
?>

<?php if(!empty($textError)) {?>
    <div class="alert alert-warning" role="alert">
        <?= $textError?>
    </div>
<?php } ?>

<?php $dataProviderInvitationUsers->pagination = false;?>
<h4><?= \open20\amos\events\AmosEvents::t('amosevents', "N. di invitati: {x}",[
        'x' => $dataProviderInvitationUsers->count
    ])?>
</h4>
<br>
<div id="container-all-users" class="container-partecipants">
    <?= $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/webmeeting/_result_search', [
        'dataProvider' => $dataProviderInvitationUsers,
        'participants' => true,
        'readOnly' => true,
    ]);
    ?>
</div>