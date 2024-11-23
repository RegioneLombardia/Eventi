<?php

use open20\amos\events\AmosEvents;
use open20\amos\core\forms\WizardPrevAndContinueButtonWidget;
use open20\amos\events\assets\WizardEventAsset;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use open20\amos\core\icons\AmosIcons;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use open20\amos\core\forms\bs4\ActiveForm;

// use open20\amos\core\forms\ActiveForm;
use open20\amos\events\models\search\UserEventSearch;
use yii\helpers\Html;
use open20\amos\events\models\EventCommunication;

$this->title = AmosEvents::t('amosevents', 'Modifica comuncazione');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;
$wizardAsset = WizardEventAsset::register($this);
$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);

$type_com_text = [
    EventCommunication::TYPE_REGISTERED_AND_ATTENDED => EventCommunication::defaultTextCommunication(EventCommunication::TYPE_REGISTERED_AND_ATTENDED),
    EventCommunication::TYPE_REGISTERED_AND_NOT_ATTENDED => EventCommunication::defaultTextCommunication(EventCommunication::TYPE_REGISTERED_AND_NOT_ATTENDED),
];
$type_com_subject = [
    EventCommunication::TYPE_REGISTERED_AND_ATTENDED => EventCommunication::defaultSubjectCommunication(EventCommunication::TYPE_REGISTERED_AND_ATTENDED),
    EventCommunication::TYPE_REGISTERED_AND_NOT_ATTENDED => EventCommunication::defaultSubjectCommunication(EventCommunication::TYPE_REGISTERED_AND_NOT_ATTENDED),
];


$this->registerJsVar('type_com',$type_com_text);
$this->registerJsVar('type_com_default',EventCommunication::defaultTextCommunication());
$this->registerJsVar('type_com_subject',$type_com_subject);
$this->registerJsVar('type_com_subject_default',EventCommunication::defaultSubjectCommunication());


$variables = \open20\amos\events\utility\EventMailUtility::getVariablesEmail();
$variablesJson = \yii\helpers\Json::encode($variables);
$tinyMCECallback = <<< JS
    function (editor) {

        let usersList = $variablesJson;
        let options = [];

        editor.on('change', function () {
                tinymce.triggerSave();
        });
        $.each(usersList, function(label, mapping) {
            options.push({
                text: label, 
                onclick: function() { 
                    editor.insertContent(label); 
                }
            });
        });

        //add the dropdown button to the editor 
        editor.addButton('keyvalues', {
            type: 'menubutton',
            text: 'Variabili',
            icon: false,
            menu: options
        });
    }
JS;

$event_id = $model->id;

$js = <<<JS
$(document).on('click', '#btn-send-email', function(e) {
    // console.log(this);
    e.preventDefault();
    var textemail = $('#text-email').val();
    console.log(textemail);
    $('#text-email').trigger('change');
      $.ajax({
            url: '/events/event-dashboard/send-communication-test?eid=$event_id',
            data: $('form').serialize(),
            method: 'post',
            success: function(data){
                alert('messaggio spedito a '+ data)
            }
      });
});

 $('input[name="EventCommunication[communication_type]"]').click(function(){
          var value = $("input[name='EventCommunication[communication_type]']:checked").val();
          if(value === '4'){
              $('#select_user_btn').show();
              $('#submit_button').hide();
          } else {
              $('#select_user_btn').hide();
              $('#submit_button').show();         
          }
     
          let subject = type_com_subject_default;

          //set text tinymce dinamcally
                var editor = tinymce.get('eventcommunication-text_email'); // use your own editor id here - equals the id of your textarea
                var content = editor.getContent();
                if(value == '5' || value== '6'){
                     content =  type_com[value];
                     subject =  type_com_subject[value];
                } else {
                   content =  type_com_default;
                }
                editor.setContent('');
                editor.execCommand('mceInsertContent', !1, content);
                $('#subject_email-id').val(subject);
     });

  
JS;
$this->registerJs($js);


?>

    <div class="row">
        <div class="col-md-12">
            <?= \open20\amos\events\widgets\ChangeLanguage::widget(['model' => $eventCommunication->event]); ?>
        </div>
    </div>
    <div class="utenti">
<?php $form = ActiveForm::begin([
    'options' => ['id' => 'form-search']
]); ?>
    <div>

    <div class="my-4 d-flex">
        <div class="mr-2">
            <svg class="icon">
                <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_contact_mail"></use>
            </svg>
        </div>

        <h5 class="font-weight-bold ">
            <?= AmosEvents::t('amosevents', "Modifica Comunicazione") ?>
        </h5>
    </div>


    <div class="row variable-gutters">
        <div class="col-md-3 mr-auto">
            <p class="text-muted">
                <?= AmosEvents::t('amosevents', "Comunica agli iscritti eventuali variazioni nei dettagli dell'evento") ?>
            </p>
            <div class="d-none d-md-block">
                <img src="<?= $wizardAsset->baseUrl ?>/img/utenti-evento.png" class="img-fluid pt-5">
            </div>
        </div>

        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12">
                    <p><strong><?= AmosEvents::t('amosevents', 'N. utenti trovati: ') . $n_users ?></strong></p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($eventCommunication, 'subject_email')
                        ->textInput(['id' => 'subject-email'])
                        ->label(AmosEvents::t('amosevents', "Oggetto della comunicazione"));; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php echo $form->field($eventCommunication, 'text_email')->widget(\open20\amos\core\forms\TextEditorWidget::className(), [
                        'options' => ['id' => 'text-email'],
                        'clientOptions' => [
                            'toolbar' => \open20\amos\events\utility\EventsUtility::getToolbarTextEditor(),
                            'setup' => new \yii\web\JsExpression($tinyMCECallback)
                        ]
                    ])->label(AmosEvents::t('amosevents', "Testo della comunicazione"));
                    ?>
                </div>
            </div>

            <hr class="dashed">


            <div class="row">
                <div class="col-md-12">
                    <?php echo $form->field($eventCommunication, 'title')->label(AmosEvents::t('amosevents', "Titolo comunicazione"));
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($eventCommunication, 'communication_type')
                        ->radioList($eventCommunication->getCommunicationTypes($model))
                        ->label(AmosEvents::t('amosevents', 'Invia comunicazione a'));
                    ?>
                </div>
            </div>
            <?php
            $displayNoneNominal = 'display:none';
            $displayNoneDynamicSearch = '';
            if ($eventCommunication->communication_type == EventCommunication::TYPE_NOMINAL) {
                $displayNoneNominal = '';
                $displayNoneDynamicSearch = 'display:none;';

            } ?>
            <div id="search-nominal-selection" style="<?= $displayNoneNominal.' margin-bottom:20px'?>">
                <?= Html::a(AmosEvents::t('amosevents', 'Modifica selezione utenti'), [
                    "/events/event-dashboard/nominal-invitation",
                    'id' => $model->id,
                    'list_id' => $eventCommunication->event_internal_list_id,
                    'communication_id' => $eventCommunication->id
                ], [
                    'class' => 'btn btn-primary m-t-20',
                    'title' => AmosEvents::t('amosevents', 'Seleziona utenti')
                ]) ?>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($eventCommunication, 'email')->label(AmosEvents::t('amosevents', "Vuoi vedere l'aspetto della mail in anteprima? 
                    Inserisci un indirizzo email per effettuare  l'invio di test")); ?>
                    <?= Html::a(AmosEvents::t('amosevents', 'Invio di test'), '', [
                        'id' => 'btn-send-email',
                        'class' => 'btn btn-sm btn-secondary btn-send-test',
                    ]) ?>
                </div>


            </div>
        </div>
    </div>
    <div class="buttons d-flex mt-5 pt-4">
        <?= Html::a(AmosEvents::t('amosevents', 'Back'), ['/events/event-dashboard/communications', 'id' => $model->id], [
            'class' => 'btn btn-secondary mr-auto',
        ]) ?>
        <?= Html::submitButton(AmosEvents::t('amosevents', "Salva"), ['class' => 'btn btn-primary']) ?>
    </div>

<?php $eventCommunication->event_id = $model->id; ?>
<?= $form->field($eventCommunication, 'event_id')->hiddenInput()->label(false) ?>

<?php ActiveForm::end(); ?>