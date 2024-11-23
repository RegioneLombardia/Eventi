<?php

use open20\amos\events\AmosEvents;
use open20\amos\core\forms\WizardPrevAndContinueButtonWidget;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use open20\amos\events\assets\WizardEventAsset;
use open20\amos\core\forms\bs4\ActiveForm;
use open20\amos\core\icons\AmosIcons;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = AmosEvents::t('amosevents', 'Crea un nuovo evento');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;

$wizardAsset = WizardEventAsset::register($this);
$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);

/** @var \open20\amos\events\models\search\EventTypeSearch $eventTypeSearchModel */
$eventTypeSearchModel = $moduleEvents->createModel('EventTypeSearch');
$types = $eventTypeSearchModel::searchGenericContextEventTypes()->all();
$moduleCwh = \Yii::$app->getModule('cwh');
$scope = null;
if (!empty($moduleCwh)) {
    $scope = $moduleCwh->getCwhScope();
}

$js = <<<JS
    $('#switch-settings').on('switchChange.bootstrapSwitch', function(){
        if($(this).is(':checked')){
            $('#settings-div').show();
        }
        else {
           $('#settings-div').hide();
        }
    });

    var invalid = $('.invalid-feedback');
    $(invalid).each(function(){
        if($(this).text() !== ''){
            $([document.documentElement, document.body]).animate({
            scrollTop: $($(this)).offset().top
            }, 2000);
        return true;
        }
    });

JS;
$this->registerJs($js);
//pr($model->getErrors());


?>

<div class="heading border-bottom mb-4 pb-2">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="mb-0 pr-2 text-muted"><?= (empty(trim($this->title)) ?: $this->title) ?></h3>
        </div>
        <div>
            <div class="step">1</div>
        </div>
    </div>
</div>

<?= \open20\amos\events\widgets\ChangeLanguage::widget(['model' => $model]) ?>
<?php
$form = ActiveForm::begin([
    'options' => ['class' => 'needs-validation']
]);
?>

<?= $form->errorSummary($model) ?>

<?php
$errorsLanding = $eventLanding->getErrors();
if (!empty($errorsLanding)) {
    echo $form->errorSummary($eventLanding);
}
$errorsLocation = $eventLocation->getErrors();
$errorsEntrance = $eventLocationEntrance->getErrors();
if (!empty($errorsLocation) || !empty($errorsEntrance)) {
    \Yii::$app->session->addFlash('danger', "E' necessario compilare la location");
}


?>

<?=
$this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/_base_info',
    [
        'model' => $model,
        'form' => $form,
        'moduleEvents' => $moduleEvents,
        'preferenceCenterTags' => $preferenceCenterTags,
        'wizardAsset' => $wizardAsset,
        'spriteAsset' => $spriteAsset,
    ])
?>

<?=
$this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/_where_and_when',
    [
        'model' => $model,
        'moduleEvents' => $moduleEvents,
        'locations' => $locations,
        'locationsForRadio' => $locationsForRadio,
        'form' => $form,
        'wizardAsset' => $wizardAsset,
        'spriteAsset' => $spriteAsset,
        'eventLocation' => $eventLocation,
        'eventLocationEntrance' => $eventLocationEntrance,
    ])
?>

<?=
$this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/_image',
    [
        'model' => $model,
        'moduleEvents' => $moduleEvents,
        'form' => $form,
        'wizardAsset' => $wizardAsset,
        'spriteAsset' => $spriteAsset,
    ])
?>


<hr id="hr-image" class="dashed">

<?= $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/_landing', [
    'model' => $model,
    'moduleEvents' => $moduleEvents,
    'arrayTemplates' => $arrayTemplates,
    'htmlTemplate' => $htmlTemplate,
    'eventLanding' => $eventLanding,
    'form' => $form
]); ?>


<div class="buttons">
    <?php
    echo WizardPrevAndContinueButtonWidget::widget([
        'model' => $model,
        'viewPreviousBtn' => false,
        'cancelUrl' => '',
    ])
    ?>

</div>
<?php ActiveForm::end(); ?>

<?php //echo $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/_modal_import',['model' => $model])?>

