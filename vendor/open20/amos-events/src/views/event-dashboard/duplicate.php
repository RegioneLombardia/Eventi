<?php

/**
 * @var $model \open20\amos\events\models\Event
 * @var $modelForm \open20\amos\events\models\DuplicateRecursiveForm
 */

use open20\amos\events\AmosEvents;
use yii\bootstrap4\ActiveForm;

use open20\amos\layout\utility\AmosIconsBi;

$this->title = AmosEvents::t('amosevents', "Duplica evento");
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss(".custom-checkbox,checkbox{
float:left
}");

$tmp = $modelForm->timePeriodStart;
end($tmp);         // move the internal pointer to the end of the array
$lastkey = key($tmp);
if (empty($lastkey)) {
    $lastkey = 1;
}

$js = <<<JS
   
    var i = $lastkey;
    function initValues(type_recursive){
        if(type_recursive == 'recursive_type_week'){
            $('#container-day-of-week').show();
        } else {
            $('#container-day-of-week').hide();
        }
        
         if(type_recursive == 'recursive_type_month'){
            $('#container-day-of-month').show();
        } else {
            $('#container-day-of-month').hide();
        }
        
        
    }
    
     $('#repeat_every_number-id').on('change', function(){
        initValues($(this).val());
    });
    
    initValues($('#repeat_every_number-id').val());
    
    
    $('.delete-btn').click(function(e){
        e.preventDefault();
        var key = $(this).attr('data-key');
        $("#recursive-summary tr[data-key="+key+"]").remove();
        $('input[name="date['+key+']"]').remove();

    });
    
    $('#add-time-period').click(function(e){
        e.preventDefault(); 
        i++;
        $.ajax({
           url:  '/events/event-dashboard/time',
           type: 'post',
           data: {n:i},
           success: function (data) {
              $('#container-time-period').append(data);

           }
        });
    });

    $('.deleteTime').click(function(){ alert('ffff');});
    $(document).on('click','.deleteTime', function(e){
            e.preventDefault(); 
            $(this).parents('.row-time-period').remove();

    });
    
    $('#confirm').click(function(){
        $('.loading').show();
    });
    
      $('#switch-time-period').on('switchChange.bootstrapSwitch', function(){
        if($(this).is(':checked')){
            $('#all-time-period').show();
            $('#container-add-period').show();
        }
        else {
           $('#all-time-period').hide();
           $('#container-add-period').hide();
        }
    });
      
  
      
         $('#btn-duplica-evento').on('click', function(){
          var type = $('#repeat_every_number-id').val();
          if(type == 'recursive_type_week'){
              var values = $('input[name="DuplicateRecursiveForm[day_of_the_week][]"]:checked');
              if(values.length == 0){
                  alert("E' obbligatorio selezionare il giorno della settimana");
                                return false;

              }
          }
          return true;
      });
JS;

$this->registerJs($js);
$beginDate = new \DateTime($model->begin_date_hour);
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

<div class="col-md-12">
    <?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3"> <?= AmosEvents::t('amosevents', "Stai duplicando l'evento <strong>{title}</strong>", [
                                    'title' => $model->title
                                ]) ?>
            </h5>
            <p><?= AmosEvents::t('amosevents', "Tipologia") . ': ' ?><strong><?= $model->eventType->title ?></strong></p>
            <p><?= AmosEvents::t('amosevents', "Luogo di svolgimento") . ': ' . "<strong>" . (!empty($model->eventLocation) ? $model->eventLocation->name : "") . "</strong>" ?></p>
            <p><?= AmosEvents::t('amosevents', "Entrata") . ': ' . "<strong>" . (!empty($model->eventLocationEntrance) ? $model->eventLocationEntrance->name : "") . "</strong>" ?></p>
            <p><?= AmosEvents::t('amosevents', "Data e ora di inizio") . ': ' . "<strong>" . \Yii::$app->formatter->asDatetime($model->begin_date_hour) . "</strong>" ?></p>

            <?php if (!empty($model->event_id)) {
                $eventFather = \open20\amos\events\models\Event::findOne($model->event_id);
                if (!empty($eventFather)) { ?>
                    <p><?= AmosEvents::t('amosevents', "Evento padre") . ': ' . "<strong>" . $eventFather->title . "</strong>" ?></p>
            <?php }
            } ?>

        </div>
    </div>
    <hr class="dashed">

    <div class="form-row mt-5">
        <div class="form-group col-md-2">
            <?= AmosEvents::t('amosevents', "Ripeti ogni") ?>
        </div>
        <div class="form-group col-md-2">
            <?= $form->field($modelForm, 'repeat_every_number')->label(false) ?>
        </div>
        <div class="form-group col-md-6">
            <?= $form->field($modelForm, 'repeat_every_type', ['options' => ['class' => '']])->widget(\kartik\select2\Select2::className(), [
                'data' => $modelForm->getRecursiveTypes(),
                'options' => [
                    'class' => 'pr-5',
                    'id' => 'repeat_every_number-id',
                    'placeholder' => AmosEvents::t('amosevents', 'Select...')
                ]
            ])->label(false) ?>
        </div>
    </div>
    <!--      ----------------      -->
    <div class="form-row">
        <div class="form-group col-md-12">
            <div id="container-day-of-week" style="display:none">

                <?= $form->field($modelForm, "day_of_the_week")->checkboxList($modelForm->getDaysOfTheWeek())
                    ->inline(true)
                    ->label(AmosEvents::t('amosevents', "Ripeti il"))
                ?>
                <?php $i = \open20\amos\events\models\DuplicateRecursiveForm::getDayOfTheWeekMonth(date('d-m-Y H:i:s'))
                ?>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">


            <div id="container-day-of-month"  style="display:none">

                <?= $form->field($modelForm, "day_of_month")->widget(\kartik\select2\Select2::className(), [
                    'data' => [
                        1 => AmosEvents::t('amosevents', 'Ogni mese il giorno') . ' ' . $beginDate->format('d'),
                        2 => AmosEvents::t('amosevents', 'Ogni mese il') . ' ' . $i . '° ' . \Yii::t('amosevents', $beginDate->format('l'))
                    ]
                ])->label(AmosEvents::t('amosevents', "Ripeti il")) ?>

            </div>
        </div>
    </div>

    <!--     --------------------       -->
    <p><?= AmosEvents::t('amosevents', "Fine") ?></p>

    <?php
    $checkedEndDate = false;
    $checkedEndAfter = false;
    if(!empty( $modelForm->end[1])){
        $checkedEndDate = true;
    }else if(!empty($modelForm->end[2])){
        $checkedEndAfter = true;
    }else {
        $checkedEndDate = true;
    }?>

    <div class="form-row">
        <div class="form-group col-md-2">
            <?= $form->field($modelForm, "end[]")->radio(['checked' => $checkedEndDate, 'id' => 'end-2', 'value' => 1])->label('Data') ?>
        </div>
        <div class="form-group col-md-6">
            <?php if (empty($modelForm->end_date)) {
                $modelForm->end_date = date('Y-m-d');
            } ?>
            <?= $form->field($modelForm, "end_date")->widget(\kartik\datecontrol\DateControl::className(), [
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
                'options' => []
            ])->label(false) ?>
        </div>
    </div>

    <div class="form-row">
        <?php $disabled = false; ?>
        <div class="form-group col-md-2">
            <?= $form->field($modelForm, "end[]")
                ->radio(['checked' => $checkedEndAfter, 'id' => 'end-3', 'value' => 2])->label('Dopo') ?>
        </div>
        <div class="form-group col-md-2">
            <?= $form->field($modelForm, "end_after", ['options' => ['class' => '']])
                ->textInput(['disabled' => $disabled])->label(false) ?>
        </div>
        <div class="form-group col-md-8">
        <div ><?= AmosEvents::t('amosevents', "Occorrenze") ?></div>
        </div>
    </div>

    <?php if (!empty($model->event_id)) { ?>
        <div class="row mt-3">
            <div class="col-md-3">
                <?php echo $form->field($modelForm, 'enableTimePeriod')->widget(
                    \kartik\widgets\SwitchInput::className(),
                    [
                        'options' => ['id' => 'switch-time-period'],

                        'pluginOptions' => [
                            'onText' => 'Si',
                            'offText' => 'No'
                        ],
                    ]
                )->label(AmosEvents::t('amosevents', "Fasce orarie")) ?>
            </div>
            <?php
            $hideTimePeriod = 'display:none';
            if ($modelForm->enableTimePeriod) {
                $hideTimePeriod = '';
            }
            ?>
            <div id="container-add-period" class="col-md-6 mt-4" style="<?= $hideTimePeriod ?>">
                <?php echo \yii\helpers\Html::a('+ Aggiungi fascia', '#', [
                    'class' => 'btn btn-primary btn-xs mt-2',
                    'id' => 'add-time-period'
                ]) ?>
            </div>
        </div>

        <div id="all-time-period" style="<?= $hideTimePeriod ?>">

            <div id="container-time-period">
                <?= $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/_time_period', [
                    'form' => $form,
                    'modelForm' => $modelForm,
                    'n' => 1,
                    'timePeriodStart' => $modelForm->timePeriodStart
                ]); ?>
            </div>
        </div>
    <?php } ?>


    <?php
    $form->field($modelForm, 'event_id')->hiddenInput() ?>

    <div class="row">
        <div class="col-md-12">

            <?php echo \yii\helpers\Html::submitButton(AmosEvents::t('amosevents', "Duplica evento"), [
                'class' => 'btn btn-primary float-right',
                'id' => 'btn-duplica-evento'
            ]) ?>
        </div>
    </div>

    <?php if (!empty($dataProvider)) {
        $dataProvider->pagination = false;
    ?>
        <hr class="dashed">
        <div class="row">
            <div class="col-md-12">
                <h4><?= AmosEvents::t('amosevents', "Riepilogo eventi generati") ?></h4>
                <?php
                $group = false;
                echo \kartik\grid\GridView::widget([
                    'options' => ['id' => 'recursive-summary'],
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [
                            'label' => 'Evento',
                            'attribute' => 'event',
                            'group' => $group,
                        ],
                        [
                            'label' => 'Giorno',
                            'attribute' => 'day_of_the_week',
                            'group' => $group,

                        ],
                        [
                            'label' => 'Data inizio',
                            'attribute' => 'date_begin',
                            'group' => $group,

                        ],
                        [
                            'label' => 'Ore inizio',
                            'attribute' => 'hour_begin'
                        ],
                        [
                            'label' => 'Data fine',
                            'attribute' => 'date_end'
                        ],
                        [
                            'label' => 'Ore fine',
                            'attribute' => 'hour_end'
                        ],
                        [
                            'class' => \yii\grid\ActionColumn::className(),
                            'template' => '{delete}',
                            'buttons' => [
                                'delete' => function ($url, $model, $key) {
                                    return \yii\helpers\Html::a(AmosIconsBi::show('ic_close', 'danger'), '#', [
                                        'class' => 'delete-btn btn btn-xs btn-icon',
                                        'data-key' => $key
                                    ]);
                                }

                            ]
                        ]

                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php echo \yii\helpers\Html::a(AmosEvents::t('amosevents', "Annulla"), ['/events/event/manage-all'], [
                    'class' => 'btn btn-secondary '
                ]) ?>
                <?php echo \yii\helpers\Html::submitButton(AmosEvents::t('amosevents', "Conferma"), [
                    'class' => 'btn btn-primary float-right',
                    'name' => 'confirm',
                    'id' => 'confirm',
                    'value' => 1
                ]) ?>
            </div>
        </div>
        <?php foreach ($dataProvider->models as $key => $date) {
            echo \yii\helpers\Html::hiddenInput("date[$key]", $date['date_begin_complete']);
            echo \yii\helpers\Html::hiddenInput("date-end[$key]", $date['date_end_complete']);
        } ?>
    <?php } ?>

    <?php ActiveForm::end() ?>


</div>