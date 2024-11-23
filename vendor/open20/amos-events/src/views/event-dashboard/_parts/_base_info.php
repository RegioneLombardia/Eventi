<?php

use open20\amos\events\AmosEvents;
use open20\amos\core\forms\WizardPrevAndContinueButtonWidget;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use open20\amos\events\assets\WizardEventAsset;
use open20\amos\core\forms\bs4\ActiveForm;
use open20\amos\core\icons\AmosIcons;
use open20\amos\events\models\Event;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use open20\amos\events\utility\EventsUtility;

/** @var \open20\amos\events\models\search\EventTypeSearch $eventTypeSearchModel */
$eventTypeSearchModel = $moduleEvents->createModel('EventTypeSearch');
$types = $eventTypeSearchModel::searchGenericContextEventTypes()->all();
$moduleCwh = \Yii::$app->getModule('cwh');
$scope = null;
if (!empty($moduleCwh)) {
    $scope = $moduleCwh->getCwhScope();
}
$eventType = $model->eventType;
$eventTypePresent = !is_null($eventType);
$eventTypeWithLimitedSeats = ($eventTypePresent && $eventType->limited_seats);

$limitedSeatsTypes = \open20\amos\events\models\Event::getEventsTypeWithLimitedSeats(true);
$informativeTypes = \open20\amos\events\models\Event::getEventsTypeWithInformativeType(true);
$privateTypes = \open20\amos\events\models\Event::getEventsTypeWithPrivate(true);
$patronageTypes = \open20\amos\events\models\Event::getEventsTypeWithPatronage(true);
$streamingTypes = \open20\amos\events\models\Event::getEventsTypeForStreaming(true);
$webmeetingTypes = \open20\amos\events\models\Event::getEventsTypeForWebmeetingWebex(true);

$limitedSeatsTypeIds = $limitedSeatsTypes->select('id')->column();
$informativeTypesIds = $informativeTypes->select('id')->column();
$privateTypesIds = $privateTypes->select('id')->column();
$patronageTypesIds = $patronageTypes->select('id')->column();
$streamingTypesIds = $streamingTypes->select('id')->column();
$webmeetingTypesIds = $webmeetingTypes->select('id')->column();

$implode = implode(',', $limitedSeatsTypeIds);
$implodeInformative = implode(',', $informativeTypesIds);
$implodePrivate = implode(',', $privateTypesIds);
$implodePatronage = implode(',', $patronageTypesIds);
$implodeStreaming = implode(',', $streamingTypesIds);
$implodeWebmeeting = implode(',', $webmeetingTypesIds);

$currentDg = EventsUtility::getCurrentDgId(true);
$currentDgId = null;
if ($currentDg) {
    $currentDgId = $currentDg;
}

$enableEventContainer = $moduleEvents->enableEventContainer;
$patronage_luya_nav_id = '';
if (!empty($moduleEvents->patronage_luya_nav_id)) {
    $patronage_luya_nav_id = $moduleEvents->patronage_luya_nav_id;
}

$locationStreaming = \open20\amos\events\models\EventLocation::find()->andWhere(['is_location_streaming' => true])->one();
if ($locationStreaming) {
    $idLocationStreaming = $locationStreaming->id;
} else {
    $idLocationStreaming = 99999999;
}
$currentType = $model->event_type_id ? $model->event_type_id : 9999999;
$js = <<<JS
    var error = "<span class='text-danger'>* (obbligatorio)</span>";
    var label = $('.field-event-event_location_id label[for="event-event_location_id"]').append(error);

    function hideShowTags(remove){
           var custom_or_default = $("input[name='Event[use_default_custom_tags]']:checked").val();
            if(custom_or_default == 2){
                $('#cont-customTagsDefault').hide();
                $('#cont-customTags').show();
                if(remove){
                    $('#custom-tags-default-id').val('');
                    $('#custom-tags-default-id').trigger('change');
                }
            } else if(custom_or_default == 3){
                $('#cont-customTagsDefault').show();
                $('#cont-customTags').show();
                if(remove){
                    $('#custom-tags-default-id').val('');
                    $('#custom-tags-default-id').trigger('change');
                }
            } else {
                 $('#cont-customTagsDefault').show();
                $('#cont-customTags').hide();
                if(remove){
                $('#custom-tags-id').tagit("removeAll");
                }
            }
    }
    
    // on page load if event type is private
    var privateIDS = '$implodePrivate';
    var privateIds = privateIDS.split(",");
    
    if(privateIds.indexOf($('#EventType').val()) >= 0){
        // poi integration
        $('#container-events-poi-integration').hide();
        $('#container-events-poi-integration-category').hide();
        // giovani platform integration
        $('#container-events-portale-giovani-integration').hide();
        $('#container-events-portale-giovani-integration-category').hide();
    }
    
    // on page load if type is webmeeting
    var webmeetingIDS = '$implodeWebmeeting';
    var webmeetingIds = webmeetingIDS.split(",");
    
    if(webmeetingIds.indexOf($('#EventType').val()) >= 0){
        // delegations
        $('#dg-appartenenza-row').hide();
        $('#delegation-options-container').hide();
        $('#delegating-dg-row').hide();
    }
    
    // on event type change
    $('#EventType').on('select2:select, change',function(){
        var ids = '$implode';
        var informativeIDS = '$implodeInformative';
        var privateIDS = '$implodePrivate';
        var patronageIDS = '$implodePatronage';
        var streamingIDS = '$implodeStreaming';
        var webmeetingIDS = '$implodeWebmeeting';
        var arrayInfIds = informativeIDS.split(",");
        var privateIds = privateIDS.split(",");
        var patronageIds = patronageIDS.split(",");
        var streamingIds = streamingIDS.split(",");
        var webmeetingIds = webmeetingIDS.split(",");
        var arrayIds = ids.split(",");



        // if type informative hide some ssections
       if(arrayInfIds.indexOf($(this).val()) >= 0){
            //console.log($('.informative-to-hide'));
            $('.informative-to-hide').each(function(){
                $(this).hide();
            });
            $('#companions-div').hide();
            $('#enable-companions-id').val(0);
        }else {
               $('.informative-to-hide').each(function(){
                $(this).show();
            });
               $('#companions-div').show();
        }

        // limited_seats
        if(arrayIds.indexOf($(this).val()) >= 0){
            $('#limited-seats-container').show();
            $('#container-has-tickets').show();
            $('#container-qr-code').show();
            $('#container-manage-list').show();
        }else {
             $('#limited-seats-container').hide();
             $('#container-has-tickets').hide();
             $('#container-qr-code').hide();
             $('#container-manage-list').hide();
        }

        // if webmeeting
        if(webmeetingIds.indexOf($(this).val()) >= 0){
            $('.hide-location').hide();
            $('.hide-time').hide();
            $('#has-ticket').val('0');
            $('#manage-waiting-list').val('0');
            $('#event-has_qr_code').val('0');
            //$('.informative-to-hide').hide();

            $('#has-ticket').trigger('change');
            $('#manage-waiting-list').trigger('change');
            $('#event-has_qr_code').trigger('change');
            $('#companions-div').hide();
            $('#enable-companions-id').val(0);
            
            //delegations
            $('#dg-appartenenza-row').hide();
            $('#delegation-options-container').hide();
            $('#delegating-dg-row').hide();
            $('#is-delegated-event').val(0);
            $("#delegating-dg").val($("#delegating-dg option:first").val());
            $('#delegating-dg').prop('disabled', true);
        } else {
            $('.hide-location').show();
            //$('.informative-to-hide').show();
            $('.hide-time').show();
            $('#dg-appartenenza-row').show();
            $('#delegation-options-container').show();
        }
        
        // if type private
        if(privateIds.indexOf($(this).val()) >= 0){
            // publish to prl
            $('#publish-to-prl-id').val(0);
            // poi integration
            $('#container-events-poi-integration').hide();
            $('#container-events-poi-integration-category').hide();
            $('#publish-on-poi-id').val(0);
            // giovani platform integration
            $('#container-events-portale-giovani-integration').hide();
            $('#container-events-portale-giovani-integration-category').hide();
            $('#publish-on-portale-giovani-id').val(0);
        } else {
            $('#container-events-poi-integration').show();
            $('#container-events-portale-giovani-integration').show();
        }

       if(patronageIds.indexOf($(this).val()) >= 0){
           $('#event-images').hide();
           $('#hr-image').hide();
           $('#luya-template-id .custom-radio').each(function(){
               var radio = $(this);
               var input = $(this).children('input');
              if($(input).val() !== '$patronage_luya_nav_id'){
                   $(radio).hide();
              }
              else {
                  $(input).trigger('click');
              }
           });
        }else{
            $('#luya-template-id .custom-radio').each(function(){
               var radio = $(this);
               $(radio).show();
           });
           $('#event-images').show();
           $('#hr-image').show();
        }

        var streaming_radio = $('#event-event_location_id input[value="$idLocationStreaming"]');
        if(streamingIds.indexOf($(this).val()) < 0){
             $(streaming_radio).parents('.custom-control.custom-radio').hide();
             $(streaming_radio).removeAttr('checked')
        }
        else{
            $(streaming_radio).parents('.custom-control.custom-radio').show();
        }


        // get description of an event
       $.ajax({
            url: '/events/event-dashboard/description-type-event',
           type: 'get',
           data: {event_type_id: $(this).val()},
           success: function (data) {
              $('#tooltip-type').html(data);
           }
      });
    });

    $('#default-tags').change(function(){
         hideShowTags(true);
    });



$('#is-father').on('switchChange.bootstrapSwitch', function(){
        if($(this).is(':checked')){
             $('#choose-father').hide();
             $('#event-father-id').val('');
             $('#event-father-id').trigger('change');
             $('#container-type-id').hide();
             $('#label-type-informative').show();
             $('#limited-seats-container').hide();
             $('#EventType').val(100);
             $('#EventType').trigger('change');


        }
        else {
             $('#container-type-id').show();
             $('#choose-father').show();
             $('#label-type-informative').hide();
        }
    });


    hideShowTags(false);

    //show hide radio evento streaming
       var streamingIDS_x = '$implodeStreaming';
       var streamingIds_x = streamingIDS_x.split(",");
       var streaming_radio_x = $('#event-event_location_id input[value="$idLocationStreaming"]');
        if(streamingIds_x.indexOf($('#EventType').val()) < 0){
            $(streaming_radio_x).parents('.custom-control.custom-radio').hide();
            $(streaming_radio_x).removeAttr('checked')
        }
        else{
            $(streaming_radio_x).parents('.custom-control.custom-radio').show();
        }

     // impedisce di deselezionare un tag se ce n'è uno solo
     // $('#event-preferencestags input[type="checkbox"]').click(function(e){
     //     if(!$(this).is(":checked")){
     //         if( $('#event-preferencestags input:checked').length === 0){
     //             alert('Non è possibile deselezionare il tag, ne deve essere selezionato almeno uno.');
     //              return false;
     //         }
     //     }
     // })
JS;

$this->registerJs($js);
?>

<?php
$tagitPlaceholderJs = <<< JS
$(document).ready(function(){
    $('.tagit-new > input').attr('placeholder','Inserisci una parola chiave');
})
JS;
$this->registerJs($tagitPlaceholderJs);
?>
<!-- TAGIT WIDGET STYLE -->
<style>
    ul.tagit > li.tagit-new {
        float: none;
    }

    ul.tagit > li.tagit-new > input {
        width: 100%;
    }
</style>

<div class="tipo-evento my-4">
    <div class="py-4 d-inline-flex w-100 title-substeps affix-top">
        <div class="mr-2">
            <svg class="icon">
                <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_event"></use>
            </svg>
        </div>

        <h5 class="font-weight-bold ">
            <?= AmosEvents::t('amosevents', "Che tipo di evento vuoi creare?") ?>
        </h5>
    </div>


    <div class="row variable-gutters">
        <div class="col-md-12">


            <?php
            $hidetype = '';
            if ($model->is_father) {
                $hidetype = 'display:none';
            }
            ?>

            <div class="col-md-12 mt-4" id="container-type-id" style="<?= $hidetype ?>">

                <?=
                $form->field($model, 'event_type_id')->widget(Select2::className(),
                    [
                        'data' => ArrayHelper::map($types, 'id', 'title'),
                        'language' => substr(Yii::$app->language, 0, 2),
                        'options' => [
                            'multiple' => false,
                            'id' => 'EventType',
                            'placeholder' => AmosEvents::t('amosevents', 'Seleziona il tipo di evento') . '...',
                            'class' => 'dynamicCreation',
                            'data-model' => 'event_type',
                            'data-field' => 'name',
                            'data-module' => 'events',
                            'data-entity' => 'event-type',
                            'data-toggle' => 'tooltip'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ])
                ?>
                <p>
                    <small id="tooltip-type"
                           class="form-text text-muted"><?= !empty($model->eventType) ? $model->eventType->description : '' ?></small>
                </p>
            </div>
            <?php
            if (!empty($hidetype)) {
                $showLabelType = '';
            } else {
                $showLabelType = 'display:none';
            }
            ?>
            <div id="label-type-informative" style="<?= $showLabelType ?>">
                <?php
                $informativeTypeModel = \open20\amos\events\models\EventType::find()->andWhere([
                    'id' => \open20\amos\events\models\EventType::TYPE_INFORMATIVE])->one()
                ?>
                <p>
                    <strong><?=
                        AmosEvents::t('amosevents', 'Tipo evento') . '</strong>: ' . ($informativeTypeModel ? $informativeTypeModel->title
                            : '')
                        ?>
                </p>
            </div>


            <?php
            if ($eventTypeWithLimitedSeats) {
                $diplayNone = '';
            } else {
                $diplayNone = 'display:none';
            }
            $disabled = false;
            if ($model->isSeatManagement()) {
                $disabled = true;
            }
            /** @var Event $eventModel */
            $eventModel = $moduleEvents->createModel('Event');
            ?>

            <div id="limited-seats-container" style="<?= $diplayNone ?>">
                <?= $form->field($model, 'seats_available')->textInput(['disabled' => $disabled, 'maxlength' => true]) ?>
            </div>
            <!-- Lasciare così la findOne perché deve prendere sempre il valore da db e non quello caricato nel model (tipo quando si salva e ci sono errori). Forse basta fare getOldAttribute ma è da testare -->
            <?php
            $modelFromDb = open20\amos\events\models\Event::findOne($model->id);
            if ($modelFromDb && $modelFromDb->isSeatManagement() && \Yii::$app->controller->id !== 'wizard') {
                ?>
                <div class="mt-4">
                    <?php
                    echo Html::button(
                        AmosEvents::t('amosevents', "Importa posti"),
                        [
                            'class' => 'btn btn-primary pull-left',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalImport',
                        ]
                    );
                    ?>
                </div>
            <?php } ?>


        </div>


    </div>


</div>
<hr class="dashed">

<div class="info-base my-4">
    <div class="py-4 d-inline-flex w-100 title-substeps affix-top">
        <div class="mr-2">
            <svg class="icon">
                <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_info"></use>
            </svg>
        </div>

        <h5 class="font-weight-bold">
            <?= AmosEvents::t('amosevents', "Informazioni di base") ?>
            <a href="#" data-toggle="tooltip" data-html="true"
               title="<?= AmosEvents::t('amosevents', "Il titolo verrà visualizzato nella landing page del tuo evento") ?>">
                <svg class="icon icon-xs icon-secondary mb-2">
                    <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_help"></use>
                </svg>
            </a>

        </h5>
    </div>

    <div class="row variable-gutters my-6">
        <div class="col-md-6">

            <?=
            $form->field($model, 'title')->textInput(['id' => 'event-title', 'placeholder' => AmosEvents::t('amosevents',
                'Inserisci il titolo del tuo evento')])
                ->hint(AmosEvents::t('amosevents', "Il titolo verrà visualizzato nella landing page del tuo evento"))
            ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($model, 'event_category_id')->label(AmosEvents::t('amosevents', 'Tipologia'))->hint(AmosEvents::t('amosevents',
                "La Tipologia specifica l'evento in base alle sue caratteristiche di contenuto e modalità di svolgimento"))
                ->widget(Select2::className(),
                    [
                        'data' => ArrayHelper::map(\open20\amos\events\models\EventCategory::find()->all(), 'id',
                            'name'),
                        'options' => ['placeholder' => AmosEvents::t('amosevents', 'Seleziona la tipologia di evento...')]
                    ])
            ?>
        </div>
    </div>

    <div class="row variable-gutters my-5">
        <div class="col-md-6">
            <?=
            $form->field($model, 'summary')->textarea(['rows' => 5, 'placeholder' => AmosEvents::t('amosevents',
                'Scrivi un Sottotitolo per il tuo evento. Usa un massimo di 50 caratteri.')])->label(AmosEvents::t('amosevents',
                'Sottotitolo'))
                ->hint(AmosEvents::t('amosevents', "Il Sottotitolo precisa il titolo del tuo evento."))
            ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($model, 'description')->textarea(['rows' => 5, 'placeholder' => AmosEvents::t('amosevents',
                'Scrivi una Descrizione per il tuo evento e aggiungi dettagli come l\'argomento, gli sponsor o gli ospiti in evidenza. Usa un massimo di 250 caratteri.')])
                ->hint(AmosEvents::t('amosevents',
                    "La Descrizione è visibile su <a target='_blank' href='https://www.regione.lombardia.it'>www.regione.lombardia.it</a>, può essere inserita nelle email di invito e può essere usata come criterio per la ricerca interna alla piattaforma."))
            ?>
        </div>

    </div>
</div>
<hr class="dashed">
<div class="preferences-center">
    <div class="py-4 d-inline-flex w-100 title-substeps affix-top">
        <div class="mr-2">
            <svg class="icon">
                <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_star"></use>
            </svg>
        </div>
        <h5 class="font-weight-bold ">
            <?=
            AmosEvents::t('amosevents',
                "Spunta 1 o più argomenti del Preference Centre per assegnare un Tag di Preference Centre al tuo evento.")
            ?>
        </h5>
    </div>


    <div class="row variable-gutters my-5">
        <div class="col-md-12">
            <div>
                <?=
                $form->field($model, 'preferencesTags')
                    ->checkboxList(EventsUtility::getLabelPreference(),
                        [
                            'encode' => false,
                            'id' => 'event-preferencestags'
                        ])->label(false)
                ?>
            </div>
        </div>

    </div>
    <div class="row variable-gutters my-5">
        <div class="col-md-3 mr-auto">
            <p class="text-muted">
                <?= AmosEvents::t('amosevents', "I Tag di Lista Evento sono etichette che codificano l’evento all’interno del portale e servono a facilitarne la ricerca. Vi sono tre diverse tipologie di scelta e inserimento dei Tag di Lista Evento.") ?>
                <br>
            </p>
        </div>

        <div class="col-md-8">
            <?php
            if (empty($model->use_default_custom_tags)) {
                $model->use_default_custom_tags = 1;
            }
            ?>

            <?=
            $form->field($model, 'use_default_custom_tags')
                ->radioList(
                    [
                        1 => 'Default' . Event::tooltipTags(1),
                        2 => AmosEvents::t('amosevents', 'Manuals') .Event::tooltipTags(2),
                        3 => AmosEvents::t('amosevents', 'Combined').Event::tooltipTags(3)
                    ], [
                    'id' => 'default-tags',
                    'encode' => false
                ])->label(false)
            ?>
            <div id="cont-customTagsDefault" style="display:none">
                <?=
                $form->field($model, 'customTagsDefault')->widget(Select2::className(),
                    [
                        'data' => ArrayHelper::map(\open20\amos\events\models\Event::getTagCustomDefault(), 'id',
                            'nome'),
                        'options' => [
                            'id' => 'custom-tags-default-id',
                            'placeholder' => AmosEvents::t('amosevents', 'Select...')
                        ],
                        'pluginOptions' => ['multiple' => true]
                    ])->label(AmosEvents::t('amosevents', 'Tag di Lista Evento'))
                ?>
            </div>
            <div id="cont-customTags" style="display:none">
                <?=
                $form->field($model, 'customTags')->widget(\xj\tagit\Tagit::className(),
                    [
                        'options' => [
                            'id' => 'custom-tags-id',
                            'placeholder' => AmosEvents::t('amosevents', 'Inserisci una parola chiave')
                        ],
                    ])->label(AmosEvents::t('amosevents', ''))
                ?>
            </div>
        </div>
    </div>
</div>
<hr class="dashed">
<div class="impostazioni-avanzate my-5">
    <div class="row">
        <div class="col-md-3 mr-auto pb-3">
            <div class="text-muted d-flex">
                <div class="mr-2">
                    <svg class="icon icon-secondary">
                        <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_settings"></use>
                    </svg>
                </div>

                <h5 class="font-weight-bold"><?= AmosEvents::t('amosevents', "Impostazioni avanzate Info evento") ?></h5>
            </div>
        </div>

        <div class="col-md-8 pb-3">
            <?=
            $form->field($model, 'advanced_options_event')->widget(\kartik\widgets\SwitchInput::className(),
                [
                    'options' => ['id' => 'switch-settings'],
                    'pluginOptions' => [
                        //'size' => 'mini',
                        //                    'onText' => AmosIcons::show('check-circle'),
                        //                    'offText' => AmosIcons::show('circle-o')
                        'onText' => '+',
                        'offText' => '-'
                    ],
                ])->label(false)
            ?>
        </div>
    </div>


    <?php
    $hideAdvanced = 'display:none';
    if ($model->advanced_options_event) {
        $hideAdvanced = '';
    }
    ?>
    <div id="settings-div" style="<?= $hideAdvanced ?>" class="p-2 p-sm-5 neutral-1-bg-a1">
        <?=
        $this->render('@vendor/open20/amos-events/src/views/wizard/_advanced_settings',
            [
                'model' => $model,
                'form' => $form,
                'moduleEvents' => $moduleEvents,
                'currentDgId' => $currentDgId
            ])
        ?>
    </div>


    <div class="row" hidden>
        <div>
            <div class="receiver-section">
                <?php
                echo
                \open20\amos\cwh\widgets\DestinatariPlusTagWidget::widget([
                    'model' => $model,
                    'moduleCwh' => $moduleCwh,
                    'scope' => $scope
                ]);
                ?>

                <?php
                $moduleSeo = \Yii::$app->getModule('seo');
                if (isset($moduleSeo)) {
                    ?>
                    <div class="row">
                        <div>
                            <?=
                            Html::tag(
                                'h2', AmosEvents::t('amosevents', '#settings_seo_title'), ['class' => 'subtitle-form']
                            )
                            ?>
                            <div class="col-12 receiver-section">
                                <?=
                                \open20\amos\seo\widgets\SeoWidget::widget([
                                    'contentModel' => $model,
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div>


        </div>
    </div>
</div>