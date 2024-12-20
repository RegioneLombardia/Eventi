<?php

use open20\amos\core\forms\CreatedUpdatedWidget;
use open20\amos\sondaggi\AmosSondaggi;
use kartik\widgets\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var open20\amos\sondaggi\models\SondaggiRisposteSessioni $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="sondaggi-risposte-sessioni-form">
    
    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL]); ?>
    
    <?php $this->beginBlock('generale'); ?>

    <div class="col-lg-6 col-sm-6">
        
        <?= $form->field($model, 'begin_date')->textInput() ?>
    </div>

    <div class="col-lg-6 col-sm-6">
        
        <?= $form->field($model, 'end_date')->textInput() ?>
    </div>

    <div class="col-lg-6 col-sm-6">
        
        <?= $form->field($model, 'unique_id')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-6 col-sm-6">
        
        <?= $form->field($model, 'session_tmp')->textarea(['rows' => 6]) ?>
    </div>

    <div class="col-lg-6 col-sm-6">
        
        <?= $form->field($model, 'session_id')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-6 col-sm-6">
        
        <?=
        // generated by schmunk42\giiant\generators\crud\providers\RelationProvider::activeField
        $form->field($model, 'user_profile_id')->dropDownList(
            \yii\helpers\ArrayHelper::map(\open20\amos\admin\models\UserProfile::find()->all(), 'id', 'id'), ['prompt' => AmosSondaggi::t('amossondaggi', 'Select')]
        );
        ?>
    </div>

    <div class="col-lg-6 col-sm-6">
        
        <?=
        // generated by schmunk42\giiant\generators\crud\providers\RelationProvider::activeField
        $form->field($model, 'sondaggi_id')->dropDownList(
            \yii\helpers\ArrayHelper::map(open20\amos\sondaggi\models\Sondaggi::find()->all(), 'id', 'id'), ['prompt' => AmosSondaggi::t('amossondaggi', 'Select')]
        );
        ?>
    </div>
    <div class="clearfix"></div>
    <?php $this->endBlock(); ?>
    
    <?php
    $itemsTab[] = [
        'label' => AmosSondaggi::t('amossondaggi', 'generale '),
        'content' => $this->blocks['generale'],
    ];
    ?>
    
    <?=
    Tabs::widget(
        [
            'encodeLabels' => false,
            'items' => $itemsTab
        ]
    );
    ?>
    <?= CreatedUpdatedWidget::widget(['model' => $model]) ?>
    <div id="form-actions" class="bk-btnFormContainer">
        
        <?= Html::a(AmosSondaggi::tHtml('amossondaggi', 'Chiudi'), Url::previous(), [
            'class' => 'btn btn-warning'
        ]); ?>
        <?= Html::submitButton($model->isNewRecord ?
            AmosSondaggi::tHtml('amossondaggi', 'Inserisci') :
            AmosSondaggi::tHtml('amossondaggi', 'Salva'), [
            'class' => $model->isNewRecord ?
                'btn btn-success' :
                'btn btn-primary'
        ]); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
