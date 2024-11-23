<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting View
 */

use open20\webmeeting\WebMeetingModule;
use open20\webmeeting\assets\WebMeetingAssets;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'action' => Yii::$app->controller->action->id,
    'method' => 'get',
    'options' => [
        'class' => 'default-form'
    ]
]);
?>

<div class="webmeeting-assets-order element-to-toggle" data-toggle-element="form-order">
    <div class="col-xs-12">
        <h2><?= WebMeetingModule::_t('#order_by') ?>:</h2>
    </div>
    
    <div class="col-sm-6 col-lg-4">
        <?= $form->field($model, 'orderAttribute')->dropDownList($model->getOrderAttributesLabels()) ?>
    </div>
    
    <div class="col-sm-6 col-lg-4">
        <?= $form->field($model, 'orderType')->dropDownList(
            [
                SORT_ASC => WebMeetingModule::_t('#ascending order'),
                SORT_DESC => WebMeetingModule::_t('#descending order'),
            ]
        )
        ?>
    </div>

    <div class="col-xs-12">
        <div class="pull-right">
            <?= Html::a(
                WebMeetingModule::_t('#cancel'),
                [
                    Yii::$app->controller->action->id, 
                    'currentView' => Yii::$app->request->getQueryParam('currentView')
                ],
                ['class'=>'btn btn-secondary']) 
            ?>
            <?= Html::submitButton(
                WebMeetingModule::_t('#ordering'),
                ['class' => 'btn btn-navigation-primary']
            ) ?>
        </div>
    </div>

    <div class="clearfix"></div>
</div>
<?php ActiveForm::end(); ?>
