<?php
/**
 * @var $model \open20\amos\events\models\Event
 */

use open20\amos\events\AmosEvents;
use open20\amos\core\forms\WizardPrevAndContinueButtonWidget;
use open20\amos\events\assets\WizardEventAsset;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use yii\bootstrap4\ActiveForm;
use open20\amos\core\icons\AmosIcons;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use open20\webmeeting\WebMeetingModule;
use open20\webmeeting\utility\WebMeetingUtility;


$beginDate = new \DateTime($model->begin_date_hour);
$endDate = new \DateTime($model->end_date_hour);
$endDateRegister = $model->registration_date_end ? new \DateTime($model->registration_date_end) : null;
?>

<div class="variable-gutters">
    <div class="row mb-1">
        <div class="col-md-6">
            <p><?= AmosEvents::t('amosevents', 'Informazioni evento') ?>
                <br><strong><?= AmosEvents::t('amosevents', 'Data inizio evento') . ': ' ?></strong><?= $beginDate->format('d/m/y H:i') ?>
                <br>
                <strong><?= AmosEvents::t('amosevents', 'Data fine evento') . ': ' ?></strong><?= $endDate->format('d/m/y H:i') ?>
            </p>
        </div>
        <?php if ($endDateRegister) { ?>
            <div class="col-md-6">
                <p><?= ' ' ?>
                    <br><strong><?= AmosEvents::t('amosevents', 'Data chiusura registrazioni evento') . ': ' ?></strong><?= $endDateRegister->format('d/m/y H:i') ?>
                </p>
            </div>
        <?php } ?>
    </div>

    <hr>

    <div class="row">

        <div class="col-md-6">
            <?= $form->field($webmeeting, 'title')
                ->label(AmosEvents::t('amosevents', 'Titolo')); ?>
        </div>
    </div>


    <?php if($webmeeting->isNewRecord){
        $webmeeting->start_hour = date('H:i');
        $webmeeting->end_hour = date('H:i');
    }?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($webmeeting, 'start_date')->widget(\kartik\datecontrol\DateControl::className(), [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,

            ])->label(AmosEvents::t('amosevents', 'Data inizio')); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($webmeeting, 'start_hour')->widget(\kartik\datecontrol\DateControl::className(), [
                'type' => \kartik\datecontrol\DateControl::FORMAT_TIME,
                'options' => [
                    'id' => 'start-hour-id',
                    'layout' => '{input} {picker} ' . (($model->beginDate == '') ? '' : '{remove}')
                ]

            ])->label(AmosEvents::t('amosevents', 'Ora inizio')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($webmeeting, 'end_date')->widget(\kartik\datecontrol\DateControl::className(), [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,

            ])->label(AmosEvents::t('amosevents', 'Data fine')); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($webmeeting, 'end_hour')->widget(\kartik\datecontrol\DateControl::className(), [
                'type' => \kartik\datecontrol\DateControl::FORMAT_TIME,
                'options' => [
                    'id' => 'end-hour-id',
                    'layout' => '{input} {picker} ' . (($model->beginDate == '') ? '' : '{remove}')
                ]
            ])->label(AmosEvents::t('amosevents', 'Ora fine')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($webmeeting, 'agenda')
                ->textarea(['rows' => 5])
                ->label(AmosEvents::t('amosevents', 'Argomenti')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($webmeeting, 'enabled_auto_record_meeting')->widget(\kartik\widgets\SwitchInput::className(), [
                'pluginOptions' => [
                    'onText' => WebMeetingModule::_t('#yes'),
                    'offText' => WebMeetingModule::_t('#no'),
                ]
            ])->label(WebMeetingModule::_t('#record_meeting'))
            ?>
        </div>
    </div>

    <hr style="border:1px dashed #ccc; margin:20px 0">


    <div class="col-xs-12">
        <?php
        $timezoneList = ArrayHelper::map(
            WebMeetingUtility::getMeetingTimezone(),
            'id',
            'value'
        );
        if(empty($webmeeting->timezone)){
            $webmeeting->timezone = \open20\webmeeting\WebMeetingModule::WEBMEETING_DEFAULT_TIMEZONE;
        }
        ?>
        <?= $form->field($webmeeting, 'timezone')->widget(Select2::className(), [
            'data' => $timezoneList,
            'options' => [
                'placeholder' => WebMeetingModule::_t('#choose_timezone'),
                'multiple' => false,
                'autocomplete' => 'off',
            ],
        ])
        ?>
    </div>


</div>
