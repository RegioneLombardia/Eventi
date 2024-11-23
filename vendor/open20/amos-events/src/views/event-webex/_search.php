<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\views\user-profile
 * @category   CategoryName
 */
use open20\amos\admin\AmosAdmin;
use open20\amos\core\helpers\Html;
use yii\widgets\ActiveForm;
use \open20\amos\events\AmosEvents;

/**
 * @var yii\web\View $this
 * @var open20\amos\admin\models\search\UserProfileSearch $model
 * @var yii\widgets\ActiveForm $form
 */
/** @var \open20\amos\admin\controllers\UserProfileController $appController */
$appController = Yii::$app->controller;

/** @var AmosAdmin $adminModule */
$adminModule = AmosAdmin::instance();

$enableAutoOpenSearchPanel = !isset(\Yii::$app->params['enableAutoOpenSearchPanel']) || \Yii::$app->params['enableAutoOpenSearchPanel'] === true;
?>

<div class="user-profile-search element-to-toggle" data-toggle-element="form-search">
    <p><?= AmosEvents::t('amosevents', 'Digita qui sotto il nome, cognome, email o il codice fiscale per filtrare i risultati, selzionali per inserirli nella lista degli invitati') ?></p>
<?php
$form = ActiveForm::begin([
            'action' => (['update', 'id' => $id]),
            'method' => 'get',
            'options' => [
                'class' => 'default-form'
            ]
        ]);
?>
    <div id="search-fields">
        <div class="row">
            <div class="col-md-8 col-xs-6">
<?=
        $form->field($modelSearch, 'name')->textInput([
            'id' => 'search-name-id',
            'aria-controls' => 'container-result-search',
            'placeholder' => AmosEvents::t('amosevents', 'Cerca')
                // 'type' => 'search'
        ])
        ->label(false);
?>
            </div>
            <div class="col-md-4 col-xs-6">
                <div class="pull-right">
                    <?=
                    Html::a(AmosEvents::t('amosevents', 'Tutti'), ['/events/event-webex/update', 'id' => $id], [
                        'class' => 'btn btn-secondary mr-2',
                        'data-key' => ''
                    ]);
                    ?>
<?= Html::submitButton(Yii::t('amoscore', 'Search'), ['class' => 'btn btn-primary']) ?>
                </div>
            </div>

        </div>



    </div>

<?php ActiveForm::end(); ?>
</div>