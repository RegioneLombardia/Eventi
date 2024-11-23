<?php

use open20\amos\events\AmosEvents;
use yii\bootstrap4\ActiveForm;
//use open20\amos\core\forms\ActiveForm;
use open20\amos\core\icons\AmosIcons;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = AmosEvents::t('amosevents', 'Seleziona partecipanti per il meeting');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;

$eventType = $model->eventType;
$eventTypePresent = !is_null($eventType);
$eventTypeWithLimitedSeats = ($eventTypePresent && $eventType->limited_seats);
$js = <<<JS
$(document).on('click', '.btn-send-test', function(e) {
    // console.log(this);
    e.preventDefault();
    var text = $(this).attr('data-attr_text');
    var subject = $(this).attr('data-attr_subject');
    var email = $(this).attr('data-key');
    var event = $('#event-id').val();
      $.ajax({
            url: '/events/event-dashboard/send-email-test?attr_text='+text+'&attr_subject='+subject+'&field_email='+email+'&eid='+event,
            data: $('form').serialize(),
            method: 'post',
            success: function(data){
                alert('messaggio spedito a '+ data)
            }
      });
});


  
JS;
$this->registerJs($js);
?>

<?php if ($readOnly) {
    echo $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/webmeeting/_webmeeting_participant_view', [
        'model' => $model,
        'dataProviderInvitationUsers' => $dataProviderInvitationUsers,
    ]);
} else { ?>
    <div class="dove-evento my-4">

        <?php $form = ActiveForm::begin(); ?>
        <?= $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/webmeeting/_form_webmeeting_participant', [
            'form' => $form,
            'model' => $model,
            'modelSearch' => $modelSearch,
            'dataProvider' => $dataProvider,
            'participants_user_ids' => $participants_user_ids,
            'countInvitationUsers' => $countInvitationUsers,
            'dataProviderInvitationUsers' => $dataProviderInvitationUsers,
        ]) ?>

        <div class="buttons">
            <?php echo Html::submitButton(AmosEvents::t('amosevents', 'Salva le modifiche'), ['class' => 'btn btn-primary ml-auto float-right']) ?>
        </div>
        <?php $form = ActiveForm::end(); ?>
    </div>
<?php } ?>
