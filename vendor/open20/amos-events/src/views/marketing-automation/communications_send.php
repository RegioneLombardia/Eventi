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
use open20\amos\events\models\EventCommunication;

use yii\web\View;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\events\models\search\EventSearch $model
 * @var string $currentView
 */


/** @var AmosEvents $eventsModule */
$eventsModule = Yii::$app->getModule(AmosEvents::getModuleName());
$this->title = AmosEvents::t('amosevents', 'Invia Comunicazione');
$this->params['breadcrumbs'][] = $this->title;

$wizardAsset = \open20\amos\events\assets\WizardEventAsset::register($this);

?>
<h4><?= AmosEvents::t('amosevents', 'Invia Comunicazione "{title}"',[
        'title' => $eventCommunication->title,
    ]) ?>
</h4>

<p><?= AmosEvents::t('amosevent', 'Sono stati trovati <strong>{n}</strong> utenti', ['n' => $n_users]) ?></p>

<hr class="dashed">

<p><?= AmosEvents::t('amosevent', 'Stai per inviare la seguente comunicazione').':' ?></p>

<?php $form = ActiveForm::begin([
    'action' => '/events/marketing-automation/send-communication?id=' . $eventCommunication->id,
    'options' => ['class' => 'needs-validation',]
]); ?>

<div class="row">
    <div class="col-md-3">
        <p>
            <strong><?= AmosEvents::t('amosevents', 'Oggetto comunicazione') .': '?></strong>
        </p>
    </div>
    <div class="col-md-9">
        <?= $eventCommunication->subject_email  ?>
    </div>
</div>


<div class="row">
    <div class="col-md-3">
        <p>
            <strong><?=  AmosEvents::t('amosevents', 'Testo comunicazione').': ' ?></strong>
        </p>
    </div>
    <div class="col-md-9">
        <p><?=$eventCommunication->text_email ?></p>
    </div>
</div>

<?php
$eventCommunication->time_schedule_type =  EventCommunication::SENDING_TIME_IMMEDIATE;
echo $form->field($eventCommunication, 'time_schedule_type')->hiddenInput()->label(false);
?>

<!--<div class="row mt-4">-->
<!--    <div class="col-md-6">-->
<!--        --><?php //echo $form->field($eventCommunication, 'time_schedule_type')
//            ->widget(\kartik\select2\Select2::className(), [
//                'data' => [
//                    EventCommunication::SENDING_TIME_IMMEDIATE => AmosEvents::t('amosevents', 'Invia immediatamente'),
//                    EventCommunication::SENDING_TIME_SCHEDULED => AmosEvents::t('amosevents', 'Pianifica invio')
//                ],
//                'options' => ['id' => 'time-schedule-type-id']
//            ])
//            ->label(AmosEvents::t('amosevents', 'Tempistica di invio')) ?>
<!--    </div>-->
<!---->
<!--    --><?php //$displayNone = 'display:none';
//    if($eventCommunication->time_schedule_type == EventCommunication::SENDING_TIME_SCHEDULED ){
//        $displayNone  = '';
//    }
//    $startDateEvent = new \DateTime($eventCommunication->event->begin_date_hour);
//    if(empty($internalList->time_schedule_date)){
//        $dateInterval = new \DateInterval('P3D');
//        $scheduleDate = $startDateEvent->sub($dateInterval);
//        $eventCommunication->time_schedule_date = $scheduleDate->format('Y-m-d H:i:s');
//    }
//    ?>
<!--    <div id="time-date-schedule" style="--><?php //echo $displayNone?><!--" class="col-md-6">-->
<!--        --><?php //echo $form->field($eventCommunication, 'time_schedule_date')
//            ->widget(\kartik\datecontrol\DateControl::className(), [
//                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
//                'options' => ['id' => 'time-schedule-date-id']
//
//            ])
//            ->label(AmosEvents::t('amosevents', 'Data di invio')); ?>
<!--    </div>-->
<!--</div>-->


<div class="row">
    <div class="col-md-6">
        <?= \yii\helpers\Html::hiddenInput('send_com') ?>
    </div>
</div>
<div class="mt-5 d-flex">

<?php
echo Html::a(AmosEvents::t('amosevents', 'Back'), ['/events/marketing-automation/index'],
    [
        'class' => 'btn btn-secondary mr-auto'
    ]);
echo Html::submitButton(AmosEvents::t('amosevents', 'Invia Comunicazione'), [
    'data-confirm' => 'Sei sicuro di inviare la comunicazione?',
    'class' => 'btn btn-primary'
])
?>
</div>
<?php ActiveForm::end(); ?>
