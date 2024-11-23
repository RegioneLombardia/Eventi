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
use open20\amos\admin\models\UserProfileAgeGroup;
use open20\amos\core\forms\editors\Select;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var open20\amos\core\forms\ActiveForm $form
 * @var open20\amos\admin\models\UserProfile $model
 * @var open20\amos\core\user\User $user
 * @var string $idTabInsights
 */
/** @var AmosAdmin $adminModule */
$adminModule = Yii::$app->controller->module;

$js = "
$('#extended-presentation-link').click(function(event) {
    event.preventDefault();
    $('a[href=\"' + $(this).attr('href') + '\"]').tab('show');
});
";
$this->registerJs($js, View::POS_READY);
?>
<section>
    <div class="row">
        <?php if ($adminModule->confManager->isVisibleField('nome', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'nome')->textInput(['maxlength' => 255, 'readonly' => false]) ?>
            </div>
        <?php endif; ?>
        <?php if ($adminModule->confManager->isVisibleField('cognome', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'cognome')->textInput(['maxlength' => 255, 'readonly' => false]) ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    if (
        ($adminModule->confManager->isVisibleField('sesso', ConfigurationManager::VIEW_TYPE_FORM)) ||
        ($adminModule->confManager->isVisibleField('user_profile_age_group_id', ConfigurationManager::VIEW_TYPE_FORM))
    ):
        ?>
    <?php endif; ?>
    <div class="row">
        <?php if ($adminModule->confManager->isVisibleField('sesso', ConfigurationManager::VIEW_TYPE_FORM)): ?>
            <div class="col-lg-3 col-sm-6">
                <?=
                $form->field($model, 'sesso',
                    [
                        'template' => "{label}\n{hint}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}",
                    ])->widget(Select::classname(),
                    [
                        'options' => ['placeholder' => AmosAdmin::t('amosadmin', 'Select/Choose') . '...', 'disabled' => false],
                        'data' => [
                            'None' => AmosAdmin::t('amosadmin', '#undefinded'),
                            'Maschio' => AmosAdmin::t('amosadmin', '#man'),
                            'Femmina' => AmosAdmin::t('amosadmin', '#women')
                        ]
                    ])->label($model->getAttributeLabel(AmosAdmin::t('amosadmin', 'Genere')));
                ?>
            </div>
        <?php endif; ?>
        <?php
        if ($adminModule->confManager->isVisibleField('user_profile_age_group_id', ConfigurationManager::VIEW_TYPE_FORM)):
            ?>
            <div class="col-lg-3 col-sm-6">
                <?=
                $form->field($model, 'user_profile_age_group_id',
                    [
                        'template' => "{label}\n{hint}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}",
                    ])->widget(Select::classname(),
                    [
                        'options' => ['placeholder' => AmosAdmin::t('amosadmin', '#select') . '...', 'disabled' => false],
                        'data' => ArrayHelper::map(UserProfileAgeGroup::find()->orderBy(['id' => SORT_ASC])->asArray()->all(),
                            'id', 'age_group')
                    ])->label($model->getAttributeLabel('age_group'));
                ?>
            </div>
        <?php endif; ?>
        <?php
        if ($adminModule->tightCoupling == true && !empty($adminModule->tightCouplingMethod) && is_array($adminModule->tightCouplingMethod)):
            $class = null;
            $method = null;
            foreach ($adminModule->tightCouplingMethod as $k => $v) {
                $class = $k;
                $method = $v;
            }

            $hideGroup = 'display:none';
            $isHidden = true;
            if (\Yii::$app->user->can('SUPER_USER_EVENT') || \Yii::$app->user->can('EVENT_DG') || \Yii::$app->user->can('ADMIN')) {
                $hideGroup = '';
                $isHidden = false;
            }
            if (!empty($class) && !empty($method) && !empty($adminModule->tightCouplingMethodField)):
                ?>
                <?php
//            DIREZIONE GENERALE APPARTENENZA
                $valuesGroups = $class::$method();
                $savedValues = \open20\amos\events\models\EventGroupReferent::find()
                    ->andWhere(['id' => $model->tightCouplingField])
                    ->all();
                $valuesGroups = ArrayHelper::merge($valuesGroups, $savedValues);
                $hide = '';
                if (!empty($_GET['group_id']) && \Yii::$app->controller->action->id == 'create') {
                    $hide = 'display:none;';
                }
                ?>
                <div class="col-lg-6 col-sm-6" style="<?= $hide . ' ' . $hideGroup ?>">
                    <?=
                    $form->field($model, 'tightCouplingField')->widget(Select2::classname(),
                        [
                            'options' => [
                                'placeholder' => AmosAdmin::t('amosadmin', 'Digita il nome del gruppo'),
                                'id' => 'tightCouplingField-id',
                                'disabled' => false,
                                'multiple' => true,
                            ],
                            'data' => ArrayHelper::map($valuesGroups, 'id', $adminModule->tightCouplingMethodField)
                        ])->label(\Yii::t('app', "Direzioni generali di appartenenza"));
                    ?>
                </div>


                <?php
//            DIREZIONE GENERALE OPERATORE
                $operatorDg = \open20\amos\events\models\EventGroupReferent::find()
                    ->innerJoin('event_group_referent_mm', 'event_group_referent_mm.event_group_referent_id = event_group_referent.id')
                    ->andWhere(['event_group_referent_mm.user_id' => $model->id])
                    ->andWhere(['event_group_referent_mm.exclude_from_query' => true])->one();
                if (empty($operatorDg)) {
                    $operatorDg = \open20\amos\events\models\EventGroupReferent::findOne(\Yii::$app->request->get('group_id'));
                }
                if ($operatorDg) { ?>
                    <div class="col-lg-6 col-sm-6" style="<?= $hideGroup ?>">
                        <div class="form-group">
                            <label class="control-label"><?php echo \Yii::t('app', "Direzione generale operatore") ?></label>
                            <p class="m-l-10 m-t-10"><?php echo $operatorDg->denominazione ?></p>
                        </div>
                    </div>
                <?php } ?>


                <?php
//            DIREZIONE GENERALE REGISTRAZIONE
                $dgRegister = \open20\amos\events\models\EventGroupReferent::findOne($model->dg_of_registration);
                $dgRegisteredName = '';
                if ($dgRegister) {
                    $dgRegisteredName = $dgRegister->denominazione;
                } else {
                    $dgRegisteredName = \Yii::t('site', "Nessuna DG");
                }
                if (!empty($dgRegisteredName)) { ?>
                    <div class="col-lg-6 col-sm-6" style="<?= $hideGroup ?>">
                        <div class="form-group">
                            <label class="control-label"><?php echo \Yii::t('app', "Direzione generale di registrazione") ?></label>
                            <p class="m-l-10 m-t-10"><?php echo $dgRegisteredName ?></p>
                        </div>
                    </div>
                <?php } ?>

                <?php if ($isHidden) {
//                $groups = \open20\amos\events\models\EventGroupReferent::find()->andWhere(['id' => $model->tightCouplingField])->all();
//                $groupsNames = [];
//                foreach ($groups as $group) {
//                    $groupsNames [] = $group->denominazione;
//                }
                ?>
                <!--                <div class="col-lg-6 col-sm-6">-->
                <!--                    <div class="form-group">-->
                <!--                        <label class="control-label">--><?php //echo \Yii::t('app', "Direzioni generali di appartenenza") ?><!--</label>-->
                <!--                        <p class="m-l-10 m-t-10">--><?php //echo implode(',', $groupsNames) ?><!--</p>-->
                <!--                    </div>-->
                <!--                </div>-->
            <?php } ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
    if ($adminModule->confManager->isVisibleField('presentazione_breve', ConfigurationManager::VIEW_TYPE_FORM)):
        ?>
        <div class="row">
            <div class="col-xs-12 m-b-20">

                <?php
                /*
                  pr($model->presentazione_breve);
                  die();
                 */
                /* Pulizia del campo di input "presentazione_breve" da tags potenzialmente pericolosi */
                //$presentazione_breve = strip_tags($model->presentazione_breve);
                ?>

                <?= AmosAdmin::t('amosadmin', 'Presentazione Breve') ?>

                <?=
                Html::input('text', 'UserProfile[presentazione_breve]', $model->presentazione_breve,
                    [
                        'id' => 'search-users-share',
                        'class' => 'form-control pull-left',
                        'placeholder' => AmosAdmin::t('amosadmin', '#short_presentation_placeholder'),
                        'maxlength' => 140,
                    ]);


                /*
                  $form->field($model, 'presentazione_breve')->limitedCharsTextArea([
                  'rows' => 6,
                  'readonly' => false,
                  'placeholder' => AmosAdmin::t('amosadmin', '#short_presentation_placeholder'),
                  'maxlength' => 140
                  ]);
                 */
                ?>


                <!--                < ?= Html::a(AmosAdmin::t('amosadmin', 'Do you want to include a more complete professional presentation') . '?', '#' . $idTabInsights, [-->
                <!--                    'data-toggle' => 'tab',-->
                <!--                    'class' => 'pull-right',-->
                <!--                    'id' => 'extended-presentation-link'-->
                <!--                ]) ?>-->
            </div>
        </div>
    <?php endif; ?>
    <!--    <?php /* if ($adminModule->confManager->isVisibleField('note', ConfigurationManager::VIEW_TYPE_FORM)): */ ?>
            <hr>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    < ?= $form->field($model, 'note')->textarea(['rows' => 6, 'readonly' => false, 'maxlength' => 500]) ?>
                </div>
            </div>
    --><?php /* endif; */ ?>
</section>
