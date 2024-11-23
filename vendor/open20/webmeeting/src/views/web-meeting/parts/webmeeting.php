<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting View
 */

use open20\webmeeting\WebMeetingModule;

use open20\amos\core\helpers\Html;

use kartik\datecontrol\DateControl;
use kartik\widgets\SwitchInput;
use kartik\widgets\Select2;

$this->beginBlock('tab_details');
?>

<div class="col-xs-12 ">
    <div class="row">
        <h2 class="subtitle-form col-xs-12"><?= WebMeetingModule::_t('#base_configurations') ?></h2>
        <div class="col-md-6 col-xs-12">
            <div class="row">
                <div class="webmeeting-form col-xs-12">
                    <?= $form->field($model, 'title')
                        ->textInput([
                            'maxlength' => true,
                            'placeholder' => WebMeetingModule::_t('#title_placeholder')
                        ])
                        ->label(WebMeetingModule::_t('#title_meeting')); ?>
                </div>
                <div class="webmeeting-form col-xs-12">
                    <?= $form->field($model, 'agenda')->limitedCharsTextArea([
                        'rows' => 6,
                        'readonly' => false,
                        'placeholder' => WebMeetingModule::_t('#description_placeholder', [
                            'maxChar' => WebMeetingModule::WEBMEETING_AGENDA_TEXT_1000
                        ]),
                        'maxlength' => WebMeetingModule::WEBMEETING_AGENDA_TEXT_1000,
                    ])->label(WebMeetingModule::_t('#description')); ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="row date-row">
                <div class="col-md-6">
                   <?= $form->field($model, 'start_date')->widget(DateControl::class, [
                       'type' => DateControl::FORMAT_DATE,
                   ]);
                   ?>
               </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'start_hour')->widget(DateControl::class, [
                        'type' => DateControl::FORMAT_TIME,
                    ])->label(false);
                    ?>
                </div>
            </div>    
            <div class="row date-row">
                <div class="col-md-6">
                    <?= $form->field($model, 'end_date')->widget(DateControl::class, [
                        'type' => DateControl::FORMAT_DATE,
                    ]);
                    ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'end_hour')->widget(DateControl::class, [
                        'type' => DateControl::FORMAT_TIME,
                    ])->label(false);
                    ?>
                </div>
            </div>
            <div class="row">
                <!-- TODO - inserire il campo di notifica dell'inizio al posto dell'ora fine -->
                <div class="col-md-6">
                    <?= $form->field($model, 'reminder_time')->widget(DateControl::class, [
                        'type' => DateControl::FORMAT_TIME,
                    ]);
                    ?>
                </div>
                <!--  -->
                <div class="col-md-6">
                    <?= $form->field($model, 'enabled_auto_record_meeting')->widget(SwitchInput::class, [
                            'pluginOptions' => [
                                'onText' => WebMeetingModule::_t('#yes'),
                                'offText' => WebMeetingModule::_t('#no'),
                            ]
                        ])->label(WebMeetingModule::_t('#record_meeting'))
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->render('advanced-configurations', [
    'form' => $form,
    'model' => $model,
    'coHostUserEmail' => $coHostUserEmail,
    'timezone' => $timezone,
]);
?>

<?php $this->endBlock('tab_details'); ?>