<?php

use open20\amos\events\AmosEvents;
use open20\amos\core\forms\ActiveForm;
use open20\amos\core\icons\AmosIcons;
use open20\amos\events\models\EventGroupReferent;
use open20\amos\events\models\EventGroupReferentMm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use open20\amos\core\helpers\Html;
use kartik\datecontrol\DateControl;
use open20\amos\events\utility\EventsUtility;
use open20\amos\events\models\Event;

$eventType = $model->eventType;
$eventTypePresent = !is_null($eventType);
$eventTypeWithLimitedSeats = ($eventTypePresent && $eventType->limited_seats);
$enableEventContainer = $moduleEvents->enableEventContainer;
$locationStreaming = \open20\amos\events\models\EventLocation::find()->andWhere(['is_location_streaming' => true])->one();
$informativeTypes = \open20\amos\events\models\Event::getEventsTypeWithInformativeType();
$totRegisteredUser = EventsUtility::getTotRegisteredUser($model);
$informativeTypesIds = [];
foreach ($informativeTypes as $type) {
    $informativeTypesIds[] = $type->id;
}

$jsDelegation = <<<JS

    if ($('#is-delegated-event').val() == true) {
        $('#delegating-dg-row').show();
        $('#delegating-dg').prop('disabled', false);
    }
    
    $('#is-delegated-event').on('change', function () {
        if ($('#is-delegated-event').val() == true) {
            $('#delegating-dg-row').show();
            $('#delegating-dg').prop('disabled', false);
        } else {
            $('#delegating-dg-row').hide();
            $('#delegating-dg').prop('disabled', true);
        }
    });

JS;

$this->registerJs($jsDelegation);

$jsPublishOnPoi = <<<JS

    if($('#publish-on-poi-id').val() == 1){
        $('#poi-category-id').prop('disabled', false);
        $('#container-events-poi-integration-category').show();
    }
        
    $('#publish-on-poi-id').change(function (){
        if($('#publish-on-poi-id').val() == 1){
            $('#poi-category-id').prop('disabled', false);
            $('#container-events-poi-integration-category').show();
        }else{
            $('#poi-category-id').prop('disabled', true);
            $('#container-events-poi-integration-category').hide();
        }
    });

JS;

$this->registerJs($jsPublishOnPoi);

$jsPublishOnPortaleGiovani = <<<JS

    if($('#publish-on-portale-giovani-id').val() == 1){
        $('#portale-giovani-category-id').prop('disabled', false);
        $('#container-events-portale-giovani-integration-category').show();
    }
        
    $('#publish-on-portale-giovani-id').change(function (){
        if($('#publish-on-portale-giovani-id').val() == 1){
            $('#portale-giovani-category-id').prop('disabled', false);
            $('#container-events-portale-giovani-integration-category').show();
        }else{
            $('#portale-giovani-category-id').prop('disabled', true);
            $('#container-events-portale-giovani-integration-category').hide();
        }
    });

JS;

$this->registerJs($jsPublishOnPortaleGiovani);

$jsTicket = <<<JS
    $('#has-ticket').click(function(){
        var value = $(this).val();
        if(value == 1){
            $("#container-seats-management").show();
        }else {
            $("#container-seats-management").hide();
            $('#event-seats_management').val(0)
        }
    });

$('#event-seats_management').click(function(){
     var value = $(this).val();
     if(value == 1){
         $('#event-seats_available').val(0);
         $('#event-seats_available').attr('readonly','true')
     }
});

 $('#enable-companions-id').click(function(){
        var value = $(this).val();
        if(value == 1){
            $("#container-max-companions").show();
             $('#max-companions-id').val(1)
        }else {
            $("#container-max-companions").hide();
            $('#max-companions-id').val(0)
        }
    });
 
 $('form').on('beforeSubmit',function(){
        var value = $('#max-companions-id').val();
        var available_seats = $('#event-seats_available').val();
        $('#container-max-companions .invalid-feedback').text('');
        $('#event-seats_available').removeClass('is-invalid');

        if(parseInt(value) == '0' && $('#enable-companions-id').val() == '1'){
                $('#container-max-companions .invalid-feedback').text('Il numero massimo degli partecipanti aggiuntivi deve essere maggiore di 0'); $([document.documentElement, document.body]).animate({
                    scrollTop: $("#container-max-companions").offset().top
                }, 2000);
                return false;
        }
       
        if(parseInt(value) >= parseInt(available_seats) && available_seats!= '' && $('#enable-companions-id').val() == '1'){
                $('#event-seats_available').addClass('is-invalid');
                $('#event-seats_available').removeClass('is-valid');
                $('#container-max-companions .invalid-feedback').text('Il numero massimo degli partecipanti aggiuntivi deve essere minore di '+available_seats);
                $([document.documentElement, document.body]).animate({
                    scrollTop: $("#container-max-companions").offset().top
                }, 2000);
            return false;
        }
        return true;
    });
JS;

$this->registerJs($jsTicket);
?>
    <div class="row">


        <div class="col-12">
            <h6><?= AmosEvents::t('amosevents', '#form_section_advanced') ?></h6>
        </div>
        <div class="col-md-3 mr-auto">
            <p><?= AmosEvents::t('amosevents', "Informazioni aggiuntive") ?></p>
        </div>

        <div class="col-md-8">
            <!--        <div>-->
            <!--            --><?php
            //            $form->field($model, 'conference_call')
            //
            ?>
            <!--        </div>-->
            <div>
                <!--            --><?php
                //            $form->field($model, 'dial_code')
                //
                ?>
            </div>

            <?php if ($enableEventContainer) { ?>
                <div class="row">
                    <?php if ($totRegisteredUser == 0) { ?>
                        <div class="col-md-6">
                            <?= $form->field($model, 'is_father')->widget(\kartik\widgets\SwitchInput::className(), [
                                'options' => ['id' => 'is-father'],
                                'pluginOptions' => [
                                    //'size' => 'mini',
                                    //                    'onText' => AmosIcons::show('check-circle'),
                                    //                    'offText' => AmosIcons::show('circle-o')
                                    'onText' => 'Si',
                                    'offText' => 'No'
                                ],
                            ])->label(AmosEvents::t('amosevents', 'Is an event father')) ?>
                        </div>
                    <?php } ?>

                    <?php
                    $hideFather = '';
                    if ($model->is_father) {
                        $hideFather = 'display:none;';
                    } ?>
                    <div id="choose-father" class="col-md-6" style="<?= $hideFather ?>">
                        <?php 
                        $eventFatherObj = EventsUtility::eventFathersQuery($currentDgId)->asArray()->all();
                        if (count($eventFatherObj) == 0) { ?>
                            <label><?= AmosEvents::t('amosevents', 'Evento padre') ?></label>
                            <p><?= AmosEvents::t('amosevents', 'Non è presente nessun evento padre') ?></p>
                        <?php } else { ?>
                            <?= $form->field($model, 'event_id')->widget(Select2::className(), [
                                'data' => ArrayHelper::map($eventFatherObj, 'id', 'title'),
                                'options' => [
                                    'placeholder' => AmosEvents::t('amosevents', 'Select...'),
                                    'id' => 'event-father-id'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'placeholder' => AmosEvents::t('amosevents', 'Select...'),
                                ],
                            ])->label(AmosEvents::t('amosevents', 'Evento padre')) ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-md-6">
                    <?=
                    $form->field($model, 'multilanguage')->dropDownList(
                        Html::getBooleanFieldsValues(),
                        [
                            'prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'),
                            'disabled' => false,
                        ]
                    )->label(AmosEvents::t('amosevents', "Abilita multilingua")) ?>
                </div>
                <div class="col-md-6">
                    <?=
                    $form->field($model, 'enable_cache')->dropDownList(
                        Html::getBooleanFieldsValues(),
                        [
                            'prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'),
                            'disabled' => false,
                        ]
                    )->label(AmosEvents::t('amosevents', "Abilita cache")) ?>
                </div>
            </div>

            <div style="display:none" class="row">
                <?php
                if ($moduleNotify && !empty($moduleNotify->enableNotificationContentLanguage) && $moduleNotify->enableNotificationContentLanguage) { ?>
                    <div class="col-md-6">
                        <?php echo
                        \open20\amos\notificationmanager\widgets\NotifyContentLanguageWidget::widget(['model' => $model]);
                        ?>
                    </div>
                <?php } ?>
            </div>

            <div style="" class="row">
                <div class="col-lg-12" style="display:none">
                    <?=
                    $form->field($model, 'event_commentable')->dropDownList(
                        Html::getBooleanFieldsValues(),
                        [
                            'options' => $model->isNewRecord ? [$moduleEvents->forceEventCommentable => ['Selected' => true]] : null,
                            'prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'), 'disabled' => false,
                        ]
                    )
                    ?>
                </div>

                <?php
                $strhideType = '';
                if (!$eventTypeWithLimitedSeats) {
                    $strhideType = "display:none";
                }
                if ($model->eventType->webmeeting_webex) {
                    $strhideType = "display:none";
                }
                ?>
                <div class="informative-to-hide col-lg-12" style="<?= $strhideType ?>">
                    <div class="row">
                        <div class="col-lg-12" style="display:none">
                            <?php echo
                            $form->field($model, 'abilita_codice_fiscale_in_form')->dropDownList(
                                Html::getBooleanFieldsValues(),
                                ['prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'), 'disabled' => false,]
                            )
                            ?>
                        </div>


                        <div id="container-has-tickets" class="col-lg-12 informative-to-hide"
                             style="<?= $strhideType ?>">
                            <?php echo
                            $form->field($model, 'has_tickets')->dropDownList(
                                Html::getBooleanFieldsValues(),
                                [
                                    'prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'),
                                    'disabled' => false,
                                    'id' => 'has-ticket'
                                ]
                            )
                            ?>
                        </div>

                        <div id="container-manage-list" class="col-lg-12 informative-to-hide"
                             style="<?= $strhideType ?>">
                            <?php
                            if ($model->isNewRecord) {
                                $model->manage_waiting_list = true;
                            }
                            echo
                            $form->field($model, 'manage_waiting_list')->dropDownList(
                                Html::getBooleanFieldsValues(),
                                [
                                    'prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'),
                                    'disabled' => false,
                                    'id' => 'manage-waiting-list'
                                ]
                            )->label(AmosEvents::t('amosevents', 'Gestisci lista di attesa'))
                                ->hint(AmosEvents::t('amosevents', "Permette di far registrare gli utenti anche dopo il raggiungimento del numero massimo di posti, inserendoli in lista di attesa."));
                            ?>
                        </div>


                        <?php
                        $strhide = '';
                        if (!$model->has_tickets || !$eventTypeWithLimitedSeats) {
                            $strhide = "display:none";
                        }
                        ?>
                        <?php if ($moduleEvents->enableSeatsManagement) : ?>
                            <div id="container-seats-management" class="col-lg-12" style="<?= $strhide ?>">
                                <?php echo
                                $form->field($model, 'seats_management')->dropDownList(
                                    Html::getBooleanFieldsValues(),
                                    ['prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'), 'disabled' => false,]
                                )->hint(AmosEvents::t(
                                    'amosevents',
                                    "Con gestione posti uguale a 'si' occorre effettuare l'importazione dei posti da file excel"
                                ))
                                ?>
                            </div>
                        <?php endif; ?>


                        <div id="container-qr-code" class="col-lg-12" style="<?= $strhideType ?>">
                            <?php echo
                            $form->field($model, 'has_qr_code')->dropDownList(
                                Html::getBooleanFieldsValues(),
                                ['prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'), 'disabled' => false,]
                            )
                            ?>
                        </div>

                        <?php if ($moduleEvents->enableCalendarsManagement) : ?>
                            <div id="container-seats-management" class="col-lg-6">
                                <?php echo
                                $form->field($model, 'slots_calendar_management')->dropDownList(
                                    Html::getBooleanFieldsValues(),
                                    ['prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'), 'disabled' => false,]
                                )
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>


                <?php if ($moduleEvents->enableEventContainer) : ?>
                    <div id="container-publish-on-prl" class="col-lg-12">
                        <?php
                        if ($model->isNewRecord) {
                            $model->publish_on_prl = true;
                        }
                        echo
                        $form->field($model, 'publish_on_prl')->dropDownList(
                            Html::getBooleanFieldsValues(),
                            [
                                'id' => 'publish-to-prl-id',
                                'prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'),
                                'disabled' => false,
                            ]
                        )
                            ->label(AmosEvents::t('amosevents', 'Pubblica anche su www.regione.lombardia.it'))
                        ?>
                    </div>
                <?php endif; ?>

            </div>

            <?php
            //   COMPANIONS
            $hideShowCompanions = '';
            if ($locationStreaming->id == $model->event_location_id || in_array($model->event_type_id, $informativeTypesIds)) {
                $hideShowCompanions = 'display:none';
            }

            if (($model->isNewRecord) && empty($model->enable_companions) && empty($model->numero_max_accompagnatori)) {
                $model->numero_max_accompagnatori = 1;
                $model->enable_companions = 0;
            }
            ?>
            <div id="companions-div" style="<?= $hideShowCompanions ?>">
                <div class="row">
                    <div id="container-compainions" class="col-md-7">
                        <?php echo
                        $form->field($model, 'enable_companions')->dropDownList(
                            Html::getBooleanFieldsValues(),
                            [
                                'id' => 'enable-companions-id',
                                'prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'),
                                'disabled' => false,
                            ]
                        )
                        ?>
                    </div>
                    <?php
                    $hideMaxCompanions = '';
                    if (!$model->enable_companions) {
                        $hideMaxCompanions = 'display:none;';
                    }
                    ?>

                    <div style="<?= $hideMaxCompanions ?>" id="container-max-companions" class="col-md-5">
                        <?= $form->field($model, 'numero_max_accompagnatori')->textInput([
                            'type' => 'number',
                            'id' => 'max-companions-id',
                            'min' => 0
                        ]); ?>
                    </div>
                </div>
            </div>

            <?php
            // DELEGATION
            $delegatedDGName = null;
            if ($model->isNewRecord) {
                if ($currentDgId) {
                    $currentDg = EventGroupReferent::findOne($currentDgId);
                    if($currentDg) {
                        $delegatedDGName = $currentDg->denominazione;
                    }
                }
            } else {
                if ($model->is_delegated_event == true) {
                    $administrationDg = EventGroupReferent::findOne(['id' => $moduleEvents->groupReferentAdministration['id']]);
                    if($administrationDg) {
                        $delegatedDGName = $administrationDg->denominazione;
                    }
                } else {
                    $delegatedDg = EventGroupReferent::findOne(['id' => $model->event_group_referent_id]);
                    if($delegatedDg) {
                        $delegatedDGName = $delegatedDg->denominazione;
                    }
                }
            } ?>

            <div class="row" id="dg-appartenenza-row">
                <div class="col-lg-7">
                    <?php
                    // DG appartenenza operatore
                    echo $form->field($model, 'event_group_referent_id')->textInput([
                        'value' => $delegatedDGName,
                        'disabled' => true
                    ])->label(AmosEvents::t('amosevents', 'DG appartenenza operatore'));
                    ?>
                </div>
            </div>

            <?php
            if ($currentDg->id == $moduleEvents->groupReferentAdministration['id']) { ?>
                <div class="row" id="delegation-options-container">

                    <div class="col-lg-5">
                        <?php
                        // Creazione evento in delega
                        echo $form->field($model, 'is_delegated_event')->dropDownList(
                            Html::getBooleanFieldsValues(),
                            ['id' => 'is-delegated-event']
                        ); ?>
                    </div>

                    <div class="col-lg-7" id="delegating-dg-row" style="display: none">
                        <?php
                        // DG delegante
                        echo $form->field($model, 'event_group_referent_id')->dropDownList(
                            ArrayHelper::map(
                                EventGroupReferent::find()->andWhere(['!=', 'id', $currentDg->id])->all(),
                                'id',
                                'denominazione'
                            ),
                            [
                                'id' => 'delegating-dg',
                                'disabled' => true
                            ]
                        )->label(AmosEvents::t('amosevents', 'DG delegante')); ?>
                    </div>
                </div>
            <?php } ?>

            <?php
            // EVENTS - POI INTEGRATION
            if ($moduleEvents->enableEventsPoiIntegration) {
                if ($moduleEvents->enabled_dg_configurations['poi_integration']) {
                    if (in_array($currentDgId, $moduleEvents->enabled_dg_configurations['poi_integration']) || Yii::$app->user->can('ADMIN')) { ?>
                        <div class="row">
                            <div id="container-events-poi-integration" class="col-lg-7">
                                <?php if ($model->isNewRecord) {
                                    $model->publish_on_poi = 0;
                                }
                                echo $form->field($model, 'publish_on_poi')->dropDownList(
                                    Html::getBooleanFieldsValues(),
                                    [
                                        'id' => 'publish-on-poi-id',
                                        //'prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'),
                                        'disabled' => false,
                                    ]
                                )->label(AmosEvents::t('amosevents', 'Pubblica anche su Open Innovation')); ?>
                            </div>

                            <div id="container-events-poi-integration-category" class="col-lg-5" style="display: none">
                                <?= $form->field($model, 'poi_category')->dropDownList(
                                    Event::getPoiCategoryLabel(),
                                    [
                                        'id' => 'poi-category-id',
                                        //'prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'),
                                        'disabled' => true,
                                        'value' => $model->isNewRecord ? Event::POI_CATEGORY_FESR : $model->poi_category
                                    ]
                                )->label(AmosEvents::t('amosevents', 'Categoria Open Innovation')); ?>
                            </div>
                        </div>
                    <?php }
                }
            } ?>

            <?php
            // EVENTS - PORTALE GIOVANI INTEGRATION
            if ($moduleEvents->enableEventsGiovaniPlatformIntegration) {
                if ($moduleEvents->enabled_dg_configurations['giovani_platform_integration']) {
                    if (in_array($currentDgId, $moduleEvents->enabled_dg_configurations['giovani_platform_integration']) || Yii::$app->user->can('ADMIN')) { ?>
                        <div class="row">

                            <div id="container-events-portale-giovani-integration" class="col-lg-7">
                                <?php if ($model->isNewRecord) {
                                    $model->publish_on_portale_giovani = 0;
                                }
                                echo $form->field($model, 'publish_on_portale_giovani')->dropDownList(
                                    Html::getBooleanFieldsValues(),
                                    [
                                        'id' => 'publish-on-portale-giovani-id',
                                        //'prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'),
                                        'disabled' => false,
                                    ]
                                )->label(AmosEvents::t('amosevents', 'Pubblica anche su Piattaforma Giovani')); ?>
                            </div>

                            <div id="container-events-portale-giovani-integration-category" class="col-lg-5" style="display: none">
                                <?= $form->field($model, 'portale_giovani_category')->dropDownList(
                                    Event::getGiovaniPlatformCategoryLabel(),
                                    [
                                        'id' => 'portale-giovani-category-id',
                                        //'prompt' => AmosEvents::t('amosevents', 'Select/Choose' . '...'),
                                        'disabled' => true,
                                        'value' => $model->isNewRecord ? Event::PIATTAFORMA_GIOVANI_CATEGORY_LAVORO : $model->portale_giovani_category
                                    ]
                                )->label(AmosEvents::t('amosevents', 'Categoria Piattaforma Giovani')); ?>
                            </div>

                        </div>
                    <?php }
                }
            } ?>
        </div>

    </div>
<?php // $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/_modal_import', ['model' => $model]); 
?>