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
use kartik\datecontrol\DateControl;
use kartik\depdrop\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var open20\amos\core\forms\ActiveForm $form
 * @var open20\amos\admin\models\UserProfile $model
 * @var open20\amos\core\user\User $user
 */

/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;
$moduleEvents  =\Yii::$app->getModule('events');
$showOnlyRegionInWizard = null;
if($moduleEvents){
    if(!empty($moduleEvents->showOnlyRegionInWizard)) {
        $showOnlyRegionInWizard = $moduleEvents->showOnlyRegionInWizard;
    }
}


?>
<section>
    <!--    <h2>-->
    <!--        < ?= AmosIcons::show('cake'); ?>-->
    <!--        < ?= AmosAdmin::tHtml('amosadmin', 'Dati di Nascita') ?>-->
    <!--    </h2>-->
    <div class="row">
        <?php if ($adminModule->confManager->isVisibleField('nascita_nazioni_id', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-6 col-sm-6" style="display:none;">
                <div class="select">
                    <?php $model->nascita_nazioni_id = 1;?>
                    <?= $form->field($model, 'nascita_nazioni_id')->widget(Select2::classname(), [
                        'options' => [
                            'placeholder' => AmosAdmin::t('amosadmin', 'Digita il nome della nazione'),
                            'disabled' => false,
                            'id' => 'nascita_nazioni_id'],
                        'data' => ArrayHelper::map(AmosAdmin::instance()->createModel('IstatNazioni')->find()->orderBy('nome')->asArray()->all(), 'id', 'nome')
                    ])->label(\Yii::t('app', 'Nazione')); ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($adminModule->confManager->isVisibleField('nascita_province_id', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-6 col-sm-6">
                <div class="select">
                    <?php
                    $provinceQuery = AmosAdmin::instance()->createModel('IstatProvince')->find()->orderBy('nome');
                    if (!empty($moduleEvents->showOnlyRegionInWizard)) {
                        $provinceQuery->andWhere(['istat_regioni_id' => $moduleEvents->showOnlyRegionInWizard]);
                    }
                    ?>
                    <?= $form->field($model, 'nascita_province_id')->widget(Select2::classname(), [
                        'options' => [
                            'placeholder' => AmosAdmin::t('amosadmin', 'Digita il nome della provincia'),
                            'id' => 'nascita_province_id-id',
                            'disabled' => false
                        ],
                        'data' => ArrayHelper::map($provinceQuery->asArray()->all(), 'id', 'nome')
                    ])->label(\Yii::t('app', 'Provincia')); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="row">
        <?php if (
            ($adminModule->confManager->isVisibleField('nascita_province_id', ConfigurationManager::VIEW_TYPE_FORM)) &&
            ($adminModule->confManager->isVisibleField('nascita_comuni_id', ConfigurationManager::VIEW_TYPE_FORM))
        ): ?>
            <div class="col-lg-6 col-sm-6">
                <div class="select">
                    <?php $comuniQuery = \open20\amos\comuni\models\IstatComuni::find()->andWhere(['istat_province_id' => $model->nascita_province_id]);
                    if (!empty($moduleEvents->showOnlyRegionInWizard)) {
                        $comuniQuery->andWhere(['istat_regioni_id' => $moduleEvents->showOnlyRegionInWizard])
                            ->orderBy('istat_comuni.nome ASC');
                    }
                    $comuni = $comuniQuery->all();
                    ?>
                    <?= $form->field($model, 'nascita_comuni_id')->widget(DepDrop::classname(), [
                        'type' => DepDrop::TYPE_SELECT2,
                        'data' => $model->nascita_province_id ? ArrayHelper::map($comuni, 'id', 'nome') : [],
                        'options' => ['id' => 'nascita_comuni_id-id', 'disabled' => false,
                            'placeholder' => \Yii::t('amosadmin', "Seleziona...")
                        ],
                        'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                        'pluginOptions' => [
                            'depends' => [(false) ?: 'nascita_province_id-id'],
                            'placeholder' => ['Seleziona ...'],
                            'url' => Url::to(['/comuni/default/comuni-by-provincia']),
                            'params' => ['nascita_comuni_id-id'],
                        ],
                    ])->label(\Yii::t('app', 'Comune')); ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($adminModule->confManager->isVisibleField('nascita_data', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-6 col-sm-6" style="display:none">
                <?= $form->field($model, 'nascita_data')->widget(DateControl::classname(), [
                    'type' => DateControl::FORMAT_DATE,
                    'options' => [
                        'disabled' => false,
                    ],
                    'autoWidget' => false,
                    'saveOptions' => [
                        'type' => 'text',
                        'class' => 'sr-only',
                        'label' => '<label for="nascita_data-disp" class="sr-only">' . AmosAdmin::t('amosadmin', 'Born Date') . '</label>'
                    ],
                    'widgetOptions' => [
                        'mask' => '99-99-9999',
                    ]
                ]) ?>
            </div>
        <?php endif; ?>
    </div>
</section>
