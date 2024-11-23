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

$enableAutoOpenSearchPanel = isset(\Yii::$app->params['enableAutoOpenSearchPanel']) 
    ? \Yii::$app->params['enableAutoOpenSearchPanel'] 
    : false;

$form = ActiveForm::begin([
    'action' => Yii::$app->controller->action->id,
    'method' => 'get',
    'options' => [
        'id' => 'assets_form',
        'class' => 'form',
        'enctype' => 'multipart/form-data',
    ]
]);
?>

<div class="webmeeting-assets-search element-to-toggle" data-toggle-element="form-search">
    <div class="col-xs-12"><h2><?= WebMeetingModule::_t('#search_for') ?>:</h2></div>

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
