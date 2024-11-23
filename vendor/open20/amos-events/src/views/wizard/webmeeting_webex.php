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
<div class="heading border-bottom mb-4 pb-2">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="mb-0 pr-2 text-muted"><?= (empty(trim($this->title)) ?: $this->title) ?></h3>
        </div>
        <div>
            <div class="step">3</div>
        </div>

    </div>
</div>
<?php $form = ActiveForm::begin(); ?>
<div class="configura-email">

    <div>
        <div class="py-4 d-flex title-substeps affix-top">
            <div class="mr-2">
                <svg class="icon">
                    <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_play_circle_outline"></use>
                </svg>
            </div>

            <h5 class="font-weight-bold ">
                <?= AmosEvents::t('amosevents', "Informazioni Webex Meeting") ?>
<!--                <a href="#" data-toggle="tooltip" data-html="true"-->
<!--                   title=" --><?php //echo AmosEvents::t('amosevents', "Puoi scegliere il <strong>colore</strong> da abbinare alla grafica e alla foto di copertina che hai scelto, per personalizzare al meglio il tuo evento.") ?><!--">-->
<!--                    <svg class="icon icon-xs icon-secondary mb-2">-->
<!--                        <use xlink:href="--><?php //echo $spriteAsset->baseUrl ?><!--/material-sprite.svg#ic_help"></use>-->
<!--                    </svg>-->
<!--                </a>-->
            </h5>
        </div>

        <?= $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/webmeeting/_form_webmeeting_config', [
            'form' => $form,
            'model' => $model,
            'webmeeting' => $webmeeting,
        ]) ?>

    </div>


    <div class="buttons row">

        <div class="bk-btnFormContainer col-xs-12 nop">
            <div class="col-sm-6 nop">
                <a class="btn btn-action-primary pull-left wizard-link-btn"
                   href="<?= Yii::$app->getUrlManager()->createUrl(['/events/wizard/emails', 'id' => $model->id]) ?>">Indietro</a>
            </div>
            <div class="col-sm-6 nop">
                <button type="submit" class="btn btn-primary pull-right wizard-widget-continue-btn">
                    <?= AmosEvents::t('events', "Prosegui") ?>
                </button>
<!--                --><?php //echo Html::a(AmosEvents::t('landing', "Vai al riepilogo"), ['/events/wizard/finish', 'id' => $model->id], [
//                    'class' => 'btn btn-outline-primary pull-right ml-2'
//                ]) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>