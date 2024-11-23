<?php

use open20\amos\events\AmosEvents;
use open20\amos\core\forms\WizardPrevAndContinueButtonWidget;
use open20\amos\events\assets\WizardEventAsset;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use open20\amos\core\icons\AmosIcons;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use \yii\bootstrap4\ActiveForm;

// use open20\amos\core\forms\ActiveForm;
use open20\amos\events\models\search\UserEventSearch;
use yii\helpers\Html;
use open20\amos\events\models\EventCommunication;
use open20\amos\layout\utility\AmosIconsBi;


$this->title = AmosEvents::t('amosevents', 'Communications');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;
$wizardAsset = WizardEventAsset::register($this);
$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);

$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
$iconModifica = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_edit></use>";
$svgIconModifica = Html::tag('svg', $iconModifica, ['class' => 'icon icon-white']);
$spanSvgIconModifica = Html::tag('span', $svgIconModifica, ['class' => 'rounded-icon rounded-secondary p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Modifica'), ['class' => 'sr-only']);

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

        //iterate the user array and create the options with text and 
        //onclick event to insert the content on click to the editor
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
$statuses = \open20\amos\events\models\EventCommunication::getStatusSending($dataProviderLists->keys);

?>
<h5 class="font-weight-bold my-2">
    <svg class="icon">
        <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_contact_mail"></use>
    </svg>
    <?= AmosEvents::t('amosevents', "Comunicazioni salvate") ?>
</h5>
<div class="row">

    <div class="col-md-12">
        <?php $event = $model; ?>
        <?= \open20\amos\core\views\AmosGridView::widget([
            'dataProvider' => $dataProviderLists,
            'columns' => [
                'title',
                [
                    'attribute' => 'communication_type',
                    'label' => AmosEvents::t('amosevents', "Destinatari della comunicazione"),
                    'value' => function ($model) {
                        return EventCommunication::getCommunicationTypeLabels()[$model->communication_type];
                    }
                ],
                [
                    'attribute' => 'created_by',
                    'value' => function ($model) {
                        $profile = \open20\amos\admin\models\UserProfile::find()->andWhere(['user_id' => $model->created_by])->one();
                        if ($profile) {
                            return $profile->nomeCognome;
                        }
                    }

                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'date'
                ],
                [
                    'label' => AmosEvents::t('amosevents', 'Tempistica invio'),
                    'value' => function ($model) {
                        if (empty($model->time_schedule_type)) {
                            return ' - ';
                        } else if ($model->time_schedule_type == \open20\amos\events\models\EventCommunication::SENDING_TIME_IMMEDIATE) {
                            return AmosEvents::t('amosevents', 'Immediato');
                        } else {
                            $date = new \DateTime($model->time_schedule_date);
                            return AmosEvents::t('amosevents', 'Pianificato') . "<br>" . $date->format('d/m/Y H:i:s');

                        }
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'n_users',
                    'label' => AmosEvents::t('amosevents', "Utenti raggiunti")
                ],
                [
                    'class' => \open20\amos\core\views\grid\ActionColumn::className(),
                    'template' => '{stop_sending}{statistics}{update}{delete}{send-communication}',
                    'buttons' => [
                        'update' => function ($url, $model) use ($event, $spanSvgIconModifica) {
//                            if ($model->communication_type == EventCommunication::TYPE_NOMINAL) {
//                                $url = ['/events/event-dashboard/nominal-invitation',
//                                    'id' => $model->event_id,
//                                    'list_id' => $model->event_internal_list_id,
//                                    'communication_id' => $model->id];
//                                return Html::a($spanSvgIconModifica, $url, ['class' => 'btn btn-xs btn-icon', 'data-toggle' => 'tooltip', 'title' => 'Modifica'], true);
//                            }

                            if (!$model->is_automatic && empty($model->status)) {
                                $url = ['communications-update', 'idCommunication' => $model->id, 'eid' => $event->id];
                                return Html::a($spanSvgIconModifica, $url, ['class' => 'btn btn-xs btn-icon', 'data-toggle' => 'tooltip', 'title' => 'Modifica'], true);
                            }
                        },

                        'delete' => function ($url, $model) use ($event) {
                            if (!$model->is_automatic) {
                                $url = ['/events/event-dashboard/delete-communication', 'idCommunication' => $model->id, 'eid' => $event->id];
                                $spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
                                $iconDelete = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_close></use>";
                                $svgIconDelete = Html::tag('svg', $iconDelete, ['class' => 'icon icon-white']);
                                $spanSvgIconDelete = Html::tag('span', $svgIconDelete, ['class' => 'rounded-icon rounded-danger p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Elimina'), ['class' => 'sr-only']);
                                return Html::a($spanSvgIconDelete, $url, [
                                    'class' => 'btn btn-xs btn-icon',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Elimina',
                                    'data-confirm' => AmosEvents::t('amosevents', "Sei sicuro di eliminare la comunicazione?")
                                ], true);
                            }
                        },
                        'statistics' => function ($url, $model) use ($event) {
                            $url = ['statistics-communication', 'communicationId' => $model->id, 'eid' => $event->id];
                            if ($model->mailup_message_id) {
                                return Html::a(AmosIconsBi::show('ic_show_chart'), $url, ['class' => 'btn btn-xs btn-icon', 'data-toggle' => 'tooltip', 'title' => AmosEvents::t('amosevents', 'Statistics')], true);
                            }
                        },
                        'send-communication' => function ($url, $model) use ($event, $statuses) {
                            if (!empty($model->status)) {
                                $status = $model->getSingleStatusSending($statuses);
                                return $status;
                            };
                            $url = ['send-communication', 'idCommunication' => $model->id, 'eid' => $event->id];
                            $spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
                            $iconSend = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_send></use>";
                            $svgIconSend = Html::tag('svg', $iconSend, ['class' => 'icon icon-white']);
                            $spanSvgIconSend = Html::tag('span', $svgIconSend, ['class' => 'rounded-icon rounded-primary  p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Invia'), ['class' => 'ml-1']);
                            return Html::a($spanSvgIconSend, $url, ['class' => 'ml-2 btn btn-xs btn-icon btn-primary', 'data-toggle' => 'tooltip', 'title' => 'Invia inviti'], true);
                        },
                        'stop_sending' => function ($url, $model) use ($event) {
                            $url = ['stop-sending-communication', 'communicationId' => $model->id, 'eid' => $event->id];
                            if (!in_array($model->status, [EventCommunication::STATUS_SENT, EventCommunication::STATUS_NO_USER_TO_IMPORT]) && $model->status != null) {
                                return Html::a(AmosIconsBi::show('ic_block', 'danger'), $url, [
                                    'class' => 'btn btn-xs btn-icon',
                                    'data-toggle' => 'tooltip',
                                    'title' => AmosEvents::t('amosevents', 'Annulla invio'),
                                    'data-confirm' => AmosEvents::t('amosevents', 'Sei sicuro di annullare l\'invio?')
                                ], true);
                            }
                        },

                    ]
                ]
            ],

        ])
        ?>
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
                <?= AmosEvents::t('amosevents', "Crea nuova comunicazione") ?>
            </h5>
            <a href="#" data-toggle="tooltip" data-html="true"
               title=" <?= AmosEvents::t('amosevents', "Comunica agli iscritti eventuali variazioni dei dettagli dell'evento.") ?>">
                <svg class="icon icon-xs icon-secondary mb-2">
                    <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_help"></use>
                </svg>
            </a>
        </div>


        <div class="row variable-gutters">

            <div class="col-md-12">

                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($eventCommunication, 'subject_email')
                            ->textInput(['id' => 'subject_email-id'])
                            ->label(AmosEvents::t('amosevents', "Oggetto della comunicazione"));; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $form->field($eventCommunication, 'text_email')->widget(\open20\amos\core\forms\TextEditorWidget::className(), [
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
                        <?php echo $form->field($eventCommunication, 'title')->label(AmosEvents::t('amosevents', "Titolo della comunicazione"))->hint(AmosEvents::t('amosevents', "Per salvare la comunicazione, assegnale un titolo")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($eventCommunication, 'communication_type')
                            ->radioList($eventCommunication->getCommunicationTypes($model))
                            ->label(AmosEvents::t('amosevents', 'Invia comunicazione a'));;
                        ?>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($eventCommunication, 'email')->textInput(['placeholder' => AmosEvents::t('amosevents', 'Inserisci la tua email per un invio di prova')])
                            ->label(AmosEvents::t('amosevents', "Per vedere in anteprima l'aspetto della comunicazione, inserisci la tua email ed effettua un invio di prova")); ?>
                        <?= Html::a(AmosEvents::t('amosevents', 'Invio di prova'), '', [
                            'id' => 'btn-send-email',
                            'class' => 'btn btn-sm btn-secondary btn-send-test',
                        ]) ?>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <?php $eventCommunication->event_id = $model->id; ?>
    <?= $form->field($eventCommunication, 'event_id')->hiddenInput(['id' => 'event-id'])->label(false) ?>
    <div class="buttons float-right">
        <?= Html::submitButton(AmosEvents::t('amosevents', "Salva la comunicazione"), [
            'class' => 'btn btn-primary',
            'id' => 'submit_button',

        ]) ?>
        <?= Html::submitButton(AmosEvents::t('amosevents', "Seleziona utenti"), [
            'id' => 'select_user_btn',
            'name' => 'select_user_btn',
            'class' => 'btn btn-primary',
            'style' => 'display:none',
            'title' => AmosEvents::t('amosevents', 'Seleziona utenti')
        ]) ?>

    </div>
    <?php ActiveForm::end(); ?>

