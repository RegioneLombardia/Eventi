<?php

use open20\amos\core\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var \open20\amos\groups\models\search\GroupsSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div id="search-form-users" data-toggle-element="form-search" class="element-to-toggle">
    <div class="col-xs-12"><h3>Cerca per:</h3></div>

    <div class="col-sm-6 col-lg-3">
        <?= $form->field($model, 'nome') ?></div>
    <div class="col-sm-6 col-lg-3">
        <?= $form->field($model, 'cognome') ?></div>
    <div class="col-sm-6 col-lg-3">
        <?= $form->field($model, 'codice_fiscale') ?></div>
    <div class="col-sm-6 col-lg-3">
        <?= $form->field($model, 'email') ?>
    </div>

    <div class="col-xs-12">
        <div class="pull-right">
            <?= Html::resetButton(Yii::t('amosgroups', 'Reset'), ['class' => 'btn btn-secondary']) ?>
            <?= Html::submitButton(Yii::t('amosgroups', 'Search'), ['class' => 'btn btn-navigation-primary search-button']) ?>
        </div>
    </div>

    <div class="clearfix"></div>

</div>
