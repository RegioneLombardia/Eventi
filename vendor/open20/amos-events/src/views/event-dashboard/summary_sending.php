<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events\views\event
 * @category   CategoryName
 */

use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\utilities\ModalUtility;
use open20\amos\core\views\DataProviderView;
use open20\amos\events\AmosEvents;
use yii\bootstrap4\ActiveForm;
use \open20\amos\events\models\EventInternalList;

use yii\web\View;

/**
 * @var yii\web\View $this
 * @var \open20\amos\events\models\Event $event
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\events\models\search\EventSearch $model
 * @var string $currentView
 */


/** @var AmosEvents $eventsModule */
$eventsModule = Yii::$app->getModule(AmosEvents::getModuleName());
$this->title = AmosEvents::t('amosevents', 'Gestione Invii');
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
    $('#time-schedule-type-id').change(function(){
        if($(this).val() == 2){
             $('#time-date-schedule').show();
        }else {
             $('#time-date-schedule').hide();
        }
    });

$('form').on('beforeSubmit', function(e){
     $('#time-date-schedule .invalid-feedback').text('');
    let schedule_type = $('#time-schedule-type-id').val();
    let schedule_date = $('#time-schedule-date-id').val();
    if(schedule_type == 2 && (schedule_date == '' || schedule_date == undefined)){
        $('#time-date-schedule .invalid-feedback').text('La data di invio è obbligatoria');
        return false;
    }
    return true;
});

$('select, input').on('change', function(){
    $('#time-date-schedule .invalid-feedback').text('');
});


JS;
$this->registerJs($js);


$wizardAsset = \open20\amos\events\assets\WizardEventAsset::register($this);
?>

<div class="alert alert-warning" role="alert">
    <p>
        <strong><?= AmosEvents::t('amosevents', "Prima di procedere con l'invio, verifica che il tuo evento sia configurato correttamente e controlla i contenuti visibili sulla landing page") ?></strong>
        <br><?= AmosEvents::t('amosevents', "Verifica la configurazione del tuo evento <a href='{link_info_event}'>qui</a>", [
                'link_info_event' => '/events/event-dashboard/info-event?id=' . $event->id
            ]
        ) ?>
        <br><?= AmosEvents::t('amosevents', "Controlla i contenuti della landing page <a href='{link_manage_content}'>qui</a>", [
            'link_manage_content' => '/events/event-dashboard/landing-manage-contents?id=' . $event->id

        ]) ?>
    </p>
</div>

<h4><?= AmosEvents::t('amosevents', 'Seleziona un modello email da inviare agli utenti della lista <strong>{name}</strong>', ['name' => $internalList->name]) ?></h4>
<p><?= AmosEvents::t('amosevents', 'Nella lista selezionata sono stati trovati <strong>{n}</strong> utenti', ['n' => $count]) ?></p>

<?php $form = ActiveForm::begin([
    'action' => '/events/event-dashboard/send-invitation?idInternalList=' . $internalList->id . '&eid=' . $event->id,
    'options' => ['class' => 'needs-validation',]
]); ?>

<div class="row">
    <div class="col-md-6">
        <?php $types = [
            'save_the_date' => AmosEvents::t('amosevent', 'Save the date'),
        ];
        if ($event->eventType->webmeeting_webex) {
            $types = [
                'webmeeting_webex' => AmosEvents::t('amosevents', 'Invito alla Registrazione Webex'),
                'webmeeting_webex_save_date' => AmosEvents::t('amosevents', 'Save the date Webex')
            ];
        } else if ($event->eventType->event_type != \open20\amos\events\models\EventType::TYPE_INFORMATIVE) {
            $types['registration_email'] = AmosEvents::t('amosevents', 'invito alla registrazione');
        } ?>
        <?php
        if(!empty($internalList->template)){
            $invitationSent->template = $internalList->template;
        }
        echo $form->field($invitationSent, 'template')
            ->label(AmosEvents::t('amosevents', 'Modello email predefinito'))->widget(\kartik\select2\Select2::className(), [
            'data' => $types
        ]); ?>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <?php echo $form->field($internalList, 'time_schedule_type')
            ->widget(\kartik\select2\Select2::className(), [
                'data' => [
                    EventInternalList::SENDING_TIME_IMMEDIATE => AmosEvents::t('amosevents', 'Invia immediatamente'),
                    EventInternalList::SENDING_TIME_SCHEDULED => AmosEvents::t('amosevents', 'Pianifica invio')
                ],
                'options' => ['id' => 'time-schedule-type-id']
            ])
            ->label(AmosEvents::t('amosevents', 'Tempistica di invio')) ?>
    </div>

    <?php $displayNone = 'display:none';
    if($internalList->time_schedule_type == EventInternalList::SENDING_TIME_SCHEDULED ){
        $displayNone  = '';
    }
    $startDateEvent = new \DateTime($event->begin_date_hour);
    if(empty($internalList->time_schedule_date) && empty($post)){
        $dateInterval = new \DateInterval('P3D');
        $scheduleDate = $startDateEvent->sub($dateInterval);
        $internalList->time_schedule_date = $scheduleDate->format('Y-m-d H:i:s');
        $today = new \DateTime();
        if($scheduleDate < new \DateTime()){
            $today->setTime(23,0);
            $internalList->time_schedule_date = $today->format('Y-m-d H:i:s');
        }
    }
    ?>
    <div id="time-date-schedule" style="<?=$displayNone?>" class="col-md-6">
        <?php echo $form->field($internalList, 'time_schedule_date')
            ->widget(\kartik\datecontrol\DateControl::className(), [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
                'options' => ['id' => 'time-schedule-date-id']

            ])
            ->label(AmosEvents::t('amosevents', 'Data di invio')); ?>
    </div>
</div>


<?php
echo Html::a(AmosEvents::t('amosevents', 'Back'), [
    '/events/event-dashboard/invite', 'id' => $event->id
],
    [
        'class' => 'btn btn-secondary mr-3'
    ]);
echo Html::submitButton(AmosEvents::t('amosevents', 'Invia email agli utenti selezionati'), [
    'data-confirm' => 'Sei sicuro di inviare gli inviti?',
    'class' => 'btn btn-primary'
])
?>
<?php ActiveForm::end(); ?>
