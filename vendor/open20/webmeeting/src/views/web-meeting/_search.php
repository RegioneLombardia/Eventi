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

use open20\amos\core\helpers\Html;

use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\datecontrol\DateControl;

$enableAutoOpenSearchPanel = isset(\Yii::$app->params['enableAutoOpenSearchPanel']) 
    ? \Yii::$app->params['enableAutoOpenSearchPanel'] 
    : false;

$form = ActiveForm::begin([
    'action' => Yii::$app->controller->action->id,
    'method' => 'get',
    'options' => [
        'id' => 'webmeeting_form_search',
        'class' => 'form',
        'enctype' => 'multipart/form-data',
    ]
]);
?>

<div class="webmeeting-assets-search element-to-toggle" data-toggle-element="form-search">
    <div class="col-xs-12"><h2><?= WebMeetingModule::_t('#search_for') ?>:</h2></div>

    <div class="col-xs-12">
        <div class="webmeeting-form col-xs-12">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="webmeeting-form col-xs-12">
            <?= $form->field($model, 'agenda')->textInput(['maxlength' => true]); ?>
        </div>

        <div class="col-lg-4 col-sm-12">
        <?= $form->field($model, 'start')->widget(DateControl::class, [
            'type' => DateControl::FORMAT_DATETIME,
            'options' => [
                'id' => '_date_begin',
            ],
            'pluginOptions' => [
                'minDate' => '0',
                'startDate' => (string) date(DateControl::FORMAT_DATETIME, time()),
                'autoclose' => true,
            ]
        ])
        ?>
        </div>

        <div class="col-lg-4 col-sm-12">
        <?= $form->field($model, 'end')->widget(DateControl::class, [
            'type' => DateControl::FORMAT_DATETIME,
            'options' => [
                'id' => '_date_end',
            ],
            'pluginOptions' => [
                'minDate' => '0',
                'startDate' => (string) date(DateControl::FORMAT_DATETIME, time()),
                'autoclose' => true,
            ]
        ])
        ?>
        </div>

        <div class="col-sm-6 col-lg-4">
        <?= $form->field($model, 'created_by')->widget(Select2::class, [
            'data' => (!empty($model->created_by) ? [$model->created_by => $creator] : []),
            'options' => [
                'placeholder' => WebMeetingModule::_t('#choose_created_by'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['/admin/user-profile-ajax/ajax-user-list']),
                    'dataType' => 'json',
                    'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                ],
            ],
        ]);
        ?>
        </div>
    </div>
    
    <div class="col-xs-12">
        <div class="pull-right">
        <?= Html::a(
            WebMeetingModule::_t('#cancel'), 
            [
                Yii::$app->controller->action->id, 
                'currentView' => Yii::$app->request->getQueryParam('currentView')
            ],
            ['class' => 'btn btn-secondary'])
        ?>
        <?= Html::submitButton(
            WebMeetingModule::_t('#find'), 
            ['class' => 'btn btn-navigation-primary'])
        ?>
        </div>
    </div>
    
    <div class="clearfix"></div>
</div>
<?= Html::hiddenInput("enableSearch", $enableAutoOpenSearchPanel); ?>
<?= Html::hiddenInput("currentView", Yii::$app->request->getQueryParam('currentView')); ?>
<?php ActiveForm::end(); ?>
