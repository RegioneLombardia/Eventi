<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\views\user-profile\boxes
 * @category   CategoryName
 */

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\base\ConfigurationManager;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;

/**
 * @var yii\web\View $this
 * @var open20\amos\core\forms\ActiveForm $form
 * @var open20\amos\admin\models\UserProfile $model
 * @var open20\amos\core\user\User $user
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

?>

<section class="section-data">

    <!--
    <div class="bk-testoBoxInfo">
        <p>< ?= AmosAdmin::tHtml('amosadmin', "I dati amministrativi consentono la fatturazione e il pagamento delle parcelle, assicurarsi che i dati inseriti siano corretti."); ?></p>
    </div>-->

    <div class="row">
        <?php if ($adminModule->confManager->isVisibleField('codice_fiscale', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <?php if (\Yii::$app->user->can('ADMIN') || \Yii::$app->user->can('EVENT_DG') || \Yii::$app->user->can('SUPER_USER') ) {?>
                <div class="col-lg-6 col-sm-6">
                    <?php echo $form->field($model, 'codice_fiscale')->textInput(['maxlength' => true, 'data-message' => Html::error($model, 'codice_fiscale')]) ?>
                </div>
            <?php }?>
<!--            <div class="col-lg-6 col-sm-6 nop">-->
<!--                <div class="form-group">-->
<!--                    <label class="control-label">--><?php //echo AmosAdmin::t('amosadmin', "Codice fiscale") ?><!--</label>-->
<!--                    <p class="m-l-5 m-t-10">--><?php //echo !empty($model->codice_fiscale) ? $model->codice_fiscale : AmosAdmin::t('amosadmin', "Non inserito") ?><!--</p>-->
<!--                </div>-->
<!--            </div>-->

        <?php endif; ?>
        <?php if ($adminModule->confManager->isVisibleField('partita_iva', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-6 col-sm-6">
                <?= $form->field($model, 'partita_iva')->textInput(['maxlength' => true, 'data-message' => Html::error($model, 'partita_iva')]) ?>
            </div>
        <?php endif; ?>
        <?php if ($adminModule->confManager->isVisibleField('iban', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-6 col-sm-6">
                <?= $form->field($model, 'iban')->textInput(['maxlength' => true, 'data-message' => Html::error($model, 'iban')]) ?>
            </div>
        <?php endif; ?>
    </div>
</section>
