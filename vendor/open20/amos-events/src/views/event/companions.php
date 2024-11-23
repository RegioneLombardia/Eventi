<?php
/**
 * @var $model \open20\amos\events\models\Event
 */

use open20\amos\core\forms\ActiveForm;
use yii\helpers\Html;
use open20\amos\events\AmosEvents;



$this->title = AmosEvents::t('amosevents', "Gestisci partecipanti aggiuntivi");
$value = $nCompanions;

?>
<div>
    <?= AmosEvents::t('amosevents', "Attualmente hai selezionato <strong>{x}</strong> partecipanti aggiuntivi per l'evento {event}, modifica il numero degli partecipanti aggiuntivi.", [
        'x' => $nCompanions,
        'event' => $model->title
    ]) ?>

    <?php $form = ActiveForm::begin() ?>
    <div class="row m-t-10">
        <?php
        $dataCompanions =['delete_companions' => \Yii::t('amosevents',"0 - Cancella tutti i partecipanti aggiuntivi")];
        $dataCompanions = \yii\helpers\ArrayHelper::merge($dataCompanions, $model->getListNcompanions());
        ?>
        <div id="container-n-companions" class="col-md-6 control-group">
            <label class="control-label"><?= AmosEvents::t('amosevents', "Numero di partecipanti aggiuntivi") ?></label>

            <?= \kartik\select2\Select2::widget([
                'name' => 'n_companions',
                'value' => $value,
                'data' => $dataCompanions,
                'options' => [
                    'id' => 'n-companions-id',
                    'placeholder' => AmosEvents::t('amosevents', "Seleziona...")
                ]
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 m-t-20">
            <?= Html::a(AmosEvents::t('amosevents', "Torna alla landing"), \open20\amos\events\utility\EventsUtility::getUrlLanding($model), [
                'class' => 'btn btn-secondary pull-left',
                'title' => AmosEvents::t('amosevents', "Torna alla landing")
            ]) ?>
            <?= Html::submitButton(AmosEvents::t('amosevents', "Salva"), [
                'class' => 'btn btn-primary pull-right',
                'title' => AmosEvents::t('amosevents', "Salva")
            ]) ?>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>
