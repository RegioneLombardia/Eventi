<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/open20/amos-events/src/views
 */

use open20\amos\core\helpers\Html;
use open20\amos\core\forms\ActiveForm;
use kartik\datecontrol\DateControl;
use open20\amos\core\forms\Tabs;
use open20\amos\core\forms\CloseSaveButtonWidget;
use open20\amos\core\forms\RequiredFieldsTipWidget;
use yii\helpers\Url;
use open20\amos\core\forms\editors\Select;
use yii\helpers\ArrayHelper;
use open20\amos\core\icons\AmosIcons;
use yii\bootstrap\Modal;
use yii\redactor\widgets\Redactor;
use yii\helpers\Inflector;

/**
 * @var yii\web\View $this
 * @var open20\amos\events\models\EventLocationEntrances $model
 * @var yii\widgets\ActiveForm $form
 */


?>
<div class="event-location-entrances-form col-xs-12 nop">

    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'event-location-entrances_' . ((isset($fid)) ? $fid : 0),
            'data-fid' => (isset($fid)) ? $fid : 0,
            'data-field' => ((isset($dataField)) ? $dataField : ''),
            'data-entity' => ((isset($dataEntity)) ? $dataEntity : ''),
            'class' => ((isset($class)) ? $class : '')
        ]
    ]);
    ?>
    <?php // $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>

    <div class="row">
        <div class="col-xs-12">
            <div class="col-md-8 col xs-12"><!-- name string -->
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?><!-- description string -->
                <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
                <?php echo $form->field($model, 'event_location_id')->hiddenInput()->label(false) ?>

                <?= RequiredFieldsTipWidget::widget(); ?>

                <?= CloseSaveButtonWidget::widget(['model' => $model]); ?>

                <?php ActiveForm::end(); ?></div>
            <div class="col-md-4 col xs-12"></div>
        </div>
        <div class="clearfix"></div>

    </div>
</div>
