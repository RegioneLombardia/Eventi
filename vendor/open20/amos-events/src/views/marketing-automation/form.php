<?php
/**
 * @var $number_communication_type integer
 */

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

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;
$wizardAsset = WizardEventAsset::register($this);
$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);

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



$js = <<<JS
$(document).on('click', '#btn-send-email', function(e) {
    // console.log(this);
    e.preventDefault();
    var textemail = $('#text-email').val();
    console.log(textemail);
    $('#text-email').trigger('change');
      $.ajax({
            url: '/events/event-dashboard/send-communication-test?',
            data: $('form').serialize(),
            method: 'post',
            success: function(data){
                alert('messaggio spedito a '+ data)
            }
      });
});

$(document).on('click', '#btn-search-users', function(e) {
    // console.log(this);
    e.preventDefault();
    var searchInterval = $('#search-interval-id').val();
      $.ajax({
            url: '/events/marketing-automation/calculate-users-found-ajax',
            method: 'get',
            //TYPE INACTIVE
            data: {type: $number_communication_type, interval: searchInterval},
            success: function(data){
                $('#count-found').text(data);
                $('#results-container').show();
                $('#submit-button').removeAttr('disabled');
            }
      });
});


  
JS;
$this->registerJs($js);


?>

    <p><?= $descriptionMarketingAutomation ?></p>

    <div class="utenti">
<?php $form = ActiveForm::begin([
    'options' => ['id' => 'form-search']
]); ?>
    <div>


    <div class="row variable-gutters">

        <div class="col-md-8">
            <div class="my-4 d-flex">
                <div class="mr-2">
                    <svg class="icon">
                        <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_contact_mail"></use>
                    </svg>
                </div>

                <h5 class="font-weight-bold ">
                    <?php if($eventCommunication->isNewRecord){?>
                        <?= AmosEvents::t('amosevents', "Crea nuova comunicazione").' '.$title ?>
                    <?php }  else { ?>
                    <?= AmosEvents::t('amosevents', "Modifica comunicazione").' '.$title ?>
                    <?php } ?>
                </h5>
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
                    <?php echo $form->field($eventCommunication, 'search_inverval')
                        ->widget(Select2::className(),[
                                'data' => EventCommunication::getOptionsFilterMarketingAutomation(),
                            'options' => ['id' => 'search-interval-id']
                        ])
                        ->label(AmosEvents::t('amosevents', "Intervallo ricerca"));
                    ?>
                </div>
            </div>

            <div class="row" style="display:none">
                <div class="col-md-12">
                    <?= $form->field($eventCommunication, 'communication_type')->hiddenInput() ?>
                </div>
            </div>

<!--                        <div class="row">-->
<!--                            <div class="col-md-12">-->
<!--                                --><?php //echo $form->field($eventCommunication, 'email')->label(AmosEvents::t('amosevents', "Vuoi vedere l'aspetto della mail in anteprima?
//                                Inserisci un indirizzo email per effettuare  l'invio di test")); ?>
<!--                                --><?php //echo Html::a(AmosEvents::t('amosevents', 'Invio di test'), '', [
//                                    'id' => 'btn-send-email',
//                                    'class' => 'btn btn-sm btn-secondary btn-send-test',
//                                ]) ?>
<!--                            </div>-->
<!--                        </div>-->
        </div>
    </div>

    <?= Html::a(AmosEvents::t('amosevents', "Ricerca tra gli utenti"), [], ['id' => 'btn-search-users', 'class' => 'btn btn-sm btn-primary']) ?>

    <?php
    $hideCount = '';
    if($eventCommunication->isNewRecord){
        $hideCount = 'display:none';
    } ?>
    <div class="mt-4" id="results-container" style="<?=$hideCount?>">
        <?= $this->render('_result_search', ['count' => $n_users]); ?>
    </div>

    <div class="buttons d-flex mt-5 pt-4">
        <?= Html::a(AmosEvents::t('amosevents', 'Back'), ['/events/marketing-automation/index'], [
            'class' => 'btn btn-secondary mr-auto',
        ]) ?>
        <?= Html::submitButton(AmosEvents::t('amosevents', "Salva"), [
            'disabled' => $eventCommunication->isNewRecord,
            'id' => 'submit-button',
            'class' => 'btn btn-primary'
        ]) ?>
    </div>

<?php ActiveForm::end(); ?>