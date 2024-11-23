<?php

use open20\amos\events\AmosEvents;
use open20\amos\core\forms\WizardPrevAndContinueButtonWidget;
use open20\amos\events\assets\WizardEventAsset;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use yii\bootstrap4\ActiveForm;
use open20\amos\core\icons\AmosIcons;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use open20\webmeeting\WebMeetingModule;


$this->title = AmosEvents::t('amosevents', 'Crea un nuovo evento');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;

$wizardAsset = WizardEventAsset::register($this);
$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
//pr($model->getErrors());

?>
<style>


</style>
<div class="heading border-bottom mb-4 pb-2">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="mb-0 pr-2 text-muted"><?= (empty(trim($this->title)) ?: $this->title) ?></h3>
        </div>
        <div>
            <div class="step">4</div>
        </div>

    </div>
</div>

<div class="py-4 d-flex title-substeps affix-top">
    <div class="mr-2">
        <svg class="icon">
            <use xlink:href="<?php echo $spriteAsset->baseUrl ?>/material-sprite.svg#ic_play_circle_outline"></use>
        </svg>
    </div>

    <h5 class="font-weight-bold ">
        <?= AmosEvents::t('amosevents', "Invita partecipanti") ?>
<!--        <a href="#" data-toggle="tooltip" data-html="true" title=" --><?php //echo AmosEvents::t('amosevents', "Puoi scegliere il <strong>colore</strong> da abbinare alla grafica e alla foto di copertina che hai scelto, per personalizzare al meglio il tuo evento.") ?><!--">-->
<!--            <svg class="icon icon-xs icon-secondary mb-2">-->
<!--                <use xlink:href="--><?php //echo $spriteAsset->baseUrl ?><!--/material-sprite.svg#ic_help"></use>-->
<!--            </svg>-->
<!--        </a>-->
    </h5>
</div>

<?php $form = ActiveForm::begin(); ?>
<?php if ($readOnly) {
    echo $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/webmeeting/_webmeeting_participant_view', [
        'model' => $model,
        'dataProviderInvitationUsers' => $dataProviderInvitationUsers,
    ]);
} else { ?>
    <?= $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/webmeeting/_form_webmeeting_participant', [
        'form' => $form,
        'model' => $model,
        'modelSearch' => $modelSearch,
        'dataProvider' => $dataProvider,
        'participants_user_ids' => $participants_user_ids,
        'countInvitationUsers' => $countInvitationUsers,
        'dataProviderInvitationUsers' => $dataProviderInvitationUsers,
    ]) ?>
<?php } ?>

<div class="buttons row">

    <div class="bk-btnFormContainer col-xs-12 nop">
        <div class="col-sm-6 nop">
            <a class="btn btn-action-primary pull-left wizard-link-btn"
               href="<?= Yii::$app->getUrlManager()->createUrl(['/events/wizard/web-meeting-webex', 'id' => $model->id]) ?>">Indietro</a>
        </div>
        <div class="col-sm-6 nop">
            <button type="submit" class="btn btn-primary pull-right wizard-widget-continue-btn">
                <?= AmosEvents::t('events', "Prosegui") ?>
            </button>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


