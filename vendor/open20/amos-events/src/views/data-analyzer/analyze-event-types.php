<?php

use open20\amos\events\AmosEvents;
use kartik\select2\Select2;
use open20\amos\events\models\search\EventTypeSearch;
use yii\helpers\Html;

// \open20\amos\layout\assets\SpinnerWaitAsset::register($this);
\open20\amos\events\assets\WizardEventAsset::register($this);

$this->title = AmosEvents::t('amosevents', "Analisi dati");
$this->params['breadcrumbs'][] = $this->title;
if(empty($resultTotEventi)){
    $resultTotEventi = null;
}


$customDate = $model->date_interval == 6 ? 'true' : 'false';

$js = <<<JS
	let customDate = $customDate;
	if (customDate) {
	    $('#personalized-date-id').show();
	} else {
	    $('#personalized-date-id').hide();
	}
	
	$("#date-interval-id").on('change', function(){
		if ($(this).val() == 6) {
			$('#personalized-date-id').show();
		} else {
		    $('#personalized-date-id').hide();
		}
	});
	
	$('form').on('beforeSubmit', function(){
	    $('#start-date-container .invalid-feedback').html('');
	    $('#end-date-container .invalid-feedback').html('');
	    if($('#date-interval-id').val() == 6){
	        let ok = true;
	        if($('#start-date-id').val() == '' || $('#start-date-id').val() == undefined){
	            $('#start-date-container .invalid-feedback').html("La data non può essere vuota");
	            ok = false;
	        }
	        if($('#end-date-id').val() == '' || $('#end-date-id').val() == undefined){
	            $('#end-date-container .invalid-feedback').html("La data non può essere vuota");
	            ok = false;
	        }
	        
	        //validate dates
	        let startDate = new Date($('#start-date-id').val());
	        let endDate = new Date($('#end-date-id').val());
	        if(endDate < startDate){
	            $('#end-date-container .invalid-feedback').html("La data di fine deve essere successiva alla data di inizio");
	            ok = false;
	        }
	        
	        if(ok){
	             $('.loading').show();
	        }
	        return ok;
	    }
	    
	    $('.loading').show();
	});

JS;

$this->registerJs($js);
?>
<div class="dimmable position-fixed loader loading" style="display:none">
    <div class="dimmer d-flex align-items-center" id="dimmer1">
        <div class="dimmer-inner">
            <div class="dimmer-icon">
                <div class="progress-spinner progress-spinner-active loading m-auto">
                    <span class="sr-only">Caricamento...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="container-analyze">
    <?php $form = \yii\bootstrap4\ActiveForm::begin([
            'action' => '/events/data-analyzer/'.\Yii::$app->controller->action->id
    ]) ?>
    <div class="row variable-gutters">
        <div class="col-md-4">
            <?= $this->render('first-select-analyze', [
                'form' => $form,
                'model' => $model,
                'actionType' => $actionType
            ]) ?>
        </div>

        <?php
        if (empty($model->date_interval) && $actionType == 'analyze-user-access'){
            $model->date_interval = \open20\amos\events\models\DataAnalyzerForm::INTERVAL_TODAY;
        } else if (empty($model->date_interval) && $actionType == 'analyze-event-participants') {
            $model->date_interval = \open20\amos\events\models\DataAnalyzerForm::INTERVAL_LAST_MONTH;
        } else if (empty($model->date_interval)) {
            $model->date_interval = \open20\amos\events\models\DataAnalyzerForm::INTERVAL_CURRENT_MONTH;
        } ?>
        <?php
        $typeInterval = 'standard';
        if ($actionType == 'analyze-event-participants' || $actionType == 'registration-users') {
            $typeInterval = 'past';
        }else if($actionType == 'analyze-user-access'){
            $typeInterval  = 'user_access';
        }?>

        <div class="col-md-4">
            <?= $form->field($model, 'date_interval')->widget(Select2::className(), [
                'data' => \open20\amos\events\models\DataAnalyzerForm::getIntervalsList($typeInterval),
                'language' => substr(Yii::$app->language, 0, 2),
                'options' => [
                    'id' => 'date-interval-id',
                    'placeholder' => AmosEvents::t('amosevents', 'Select/Choose') . '...',
                    'data-toggle' => 'tooltip'
                ],
            ])->label(AmosEvents::t('amosevents', "Intervallo temporale"))
            ?>
        </div>
        <div class="col-md-4 text-right">
            <?= Html::submitButton(AmosEvents::t('amosevents', "Mostra statistiche"), [
                'class' => 'btn btn-primary m-t-30 mt-4'
            ]) ?>
        </div>
    </div>

    <?php
    $showHide = "display:none;";
    if ($model->date_interval == \open20\amos\events\models\DataAnalyzerForm::INTERVAL_PERSONALIZED) {
        $showHide = '';
    } ?>
    <div id="personalized-date-id" style="<?= $showHide ?>">
        <div class="row variable-gutters">


            <div class="col-md-4" id="start-date-container">
                <?= $form->field($model, 'start_date')->widget(\kartik\datecontrol\DateControl::className(), [
                    'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
                    'options' => ['id' => 'start-date-id']
                ])->label(AmosEvents::t('amosevents', "Data inizio")) ?>
            </div>
            <div class="col-md-4" id="end-date-container">
                <?= $form->field($model, 'end_date')->widget(\kartik\datecontrol\DateControl::className(), [
                    'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
                    'options' => ['id' => 'end-date-id']
                ])->label(AmosEvents::t('amosevents', "Data fine")) ?>
            </div>
        </div>
    </div>

    <?php \yii\bootstrap4\ActiveForm::end() ?>


    <?php if (!empty($dataProvider)) { ?>
        <div class="row mt-4 variable-gutters">
            <div class="col-md-5">
                <?= $this->render('grid-analyze', [
                    'dataProvider' => $dataProvider,
                    'tot' => $tot,
                    'actionType' => $actionType,
                    'model' => $model,
                    'resultTotEventi' => $resultTotEventi
                ]); ?>
            </div>

            <div class="col-md-7">
                <?php if ($actionType == 'analyze-event-participants') {
                    $height = 600;
                    if (count($statistics) == 2) {
                        $height = 200;
                    }
                    ?>
                    <?php if (count($statistics) >= 2) { ?>
                        <?=
                        \open20\amos\events\components\GoogleCharts::widget([
                            'visualization' => 'BarChart',
                            'data' => $statistics,
                            'packages' => 'corechart',
                            'options' => [
                                'width' => '100%',
                                'height' => $height,
                                'legend' => ['position' => "bottom"],
                                'title' => 'Tipologie di eventi',
                                'hAxis' => [
                                    'format' => '0'
                                ]
                            ],
                        ]);
                        ?>
                    <?php } else { ?>
                        <p><?= \Yii::t('amosevents', "Nessun dato trovato per il periodo selezionato") ?></p>
                    <?php } ?>
                <?php } else {
                    $hAxis = [
                        'title' => 'Intervallo di tempo',
                        'gridlines' => [
                            'color' => null, //set grid line transparent
                        ]
                    ];
                    if($model->isMonthly){
                        $hAxis['format'] ='MMMM';
                        if($model->nMonth >= 12){
                            $hAxis['format'] ='MMMM/Y';
                        }
                    }
                    echo \open20\amos\events\components\GoogleCharts::widget([
                        'useDate' => true,
                        'visualization' => 'LineChart',
                        'data' => $statistics,
                        'options' => [
                            'title' => $titleChart,
                            'height' => 400,

                            //                        'curveType' => 'function',
                            'legend' => ['position' => 'top'],
                            'hAxis' => $hAxis,
                            'pointSize'=> 5,
                            'vAxis' => [
                                'title' => 'Numero di eventi',
                                'slantedText' => true,
                                'format' => '0'
                            ],
                        ]
                    ]);
                } ?>
            </div>
        </div>
    <?php } ?>

</div>
