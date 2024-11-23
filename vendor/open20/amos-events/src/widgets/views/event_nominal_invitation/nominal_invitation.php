<?php

use open20\amos\events\AmosEvents;
use yii\bootstrap4\ActiveForm;

//use open20\amos\core\forms\ActiveForm;
use open20\amos\core\icons\AmosIcons;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

//$js = <<<JS
//    $('#submit-btn-nominal-invitation').click(function(event){
//        event.preventDefault();
//
//    });
//JS;
//
//if($communication) {
//    $this->registerJs($js);
//}
?>


<div class="dove-evento my-4">
    <?php $form = ActiveForm::begin(); ?>
    <?= $this->render('@vendor/open20/amos-events/src/widgets/views/event_nominal_invitation/_form_participant', [
        'form' => $form,
        'model' => $model,
        'internalList' => $internalList,
        'communication' => $communication,
        'modelSearch' => $modelSearch,
        'dataProvider' => $dataProvider,
        'participants_user_ids' => $participants_user_ids,
        'countInvitationUsers' => $countInvitationUsers,
        'dataProviderInvitationUsers' => $dataProviderInvitationUsers,
    ]) ?>

    <div class="buttons">
        <?php
        $controller = \Yii::$app->controller->id;
        $urlBack = ["/events/$controller/invite", 'id' => $model->id];
        if ($communication) {
            $urlBack = [
                '/events/event-dashboard/communications-update',
                'idCommunication' => $communication->id,
                'eid' => $communication->event_id
            ];
        }
        ?>
        <?= Html::a(AmosEvents::t('amosevents', 'Annulla'), $urlBack, [
            'class' => 'btn btn-secondary',
            'title' => AmosEvents::t('amosevents', 'Annulla')
        ]) ?>
        <?php echo Html::submitButton(AmosEvents::t('amosevents', 'Seleziona'), ['id' => 'submit-btn-nominal-invitation', 'class' => 'btn btn-primary ml-auto float-right']) ?>
    </div>
    <?php $form = ActiveForm::end(); ?>
</div>
