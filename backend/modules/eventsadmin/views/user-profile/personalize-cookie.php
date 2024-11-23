<?php

use \yii\helpers\Html;
use open20\amos\core\forms\ActiveForm;

$urlAcceptAll = \Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/accept-all-cookie?urlRedirect=' . urlencode(\Yii::$app->request->get('urlRedirect'));
$urlRejectAll = \Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/reject-all-cookie?urlRedirect=' . urlencode(\Yii::$app->request->get('urlRedirect'));

$js = <<<JS
      $('#switch-analytics, #switch-profilazione').on('switchChange.bootstrapSwitch', function(){
          $('#btn-accept-all').removeAttr('disabled');
          $('#btn-accept-all').attr('href', '$urlAcceptAll');
          $('#btn-reject-all').removeAttr('disabled');
          $('#btn-reject-all').attr('href', '$urlRejectAll');
    });
JS;
$this->registerJs($js);
$this->title = \Yii::t('app', 'Gestisci le preferenze sui cookie');
?>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h2><?= $this->title ?></h2>
        </div>
        <div class="col-md-4 text-right">

            <div class="m-b-10">
            <?= Html::a(\Yii::t('app', "Visualizza cookie policy completa"), \Yii::$app->params['platform']['frontendUrl'] . '/note-legali-e-cookie-policy', [
                'class' => 'btn btn-default btn-block ',
                'target' => 'blank',
            ]) ?>
            </div>

            <?php
            $paramsAcceptAll = [
                'class' => 'btn btn-primary btn-block',
                'id' => 'btn-accept-all'
            ];
            if ($model->cookieAcceptAll) {
                $paramsAcceptAll['disabled'] = true;
                $urlAcceptAll = '#';
            }
            ?>
            <?= Html::a(\Yii::t('app', "Accetta tutti"),
                $urlAcceptAll,
                $paramsAcceptAll
            ) ?>


        </div>
    </div>
    <?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-md-12">
            <?= \Yii::t('app', 'Queso sito utilizza le seguenti categorie di cookie') ?>
        </div>
        <div class="col-md-12">
            <h4><strong><?= \Yii::t('app', 'Cookie tecnici') ?></strong></h4>
            <p><?= \Yii::t('app', 'I Cookie tecnici sono utilizzati per gestire gli aspetti tecnici necessari a erogare un servizio richiesto dall’utente, come ad esempio la navigazione di un sito internet, la gestione della sessione di un utente o il riconoscimento automatico della lingua preferita dall’utente.') ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h4><strong><?= \Yii::t('app', 'Cookie Analytics') ?></strong></h4>
            <?= $form->field($model, 'cookieAnalytics')
                ->widget(\kartik\widgets\SwitchInput::className(), [
                    'options' => ['id' => 'switch-analytics'],
                    'pluginOptions' => [
                        //'size' => 'mini',
                        //                    'onText' => AmosIcons::show('check-circle'),
                        //                    'offText' => AmosIcons::show('circle-o')
                        'onText' => 'Si',
                        'offText' => 'No'
                    ],
                ])
                ->label(\Yii::t('app', "I Cookie analytics sono utilizzati per raccogliere informazioni sul numero degli utenti e su come questi visitano il sito internet e quindi elaborare statistiche generali sul servizio e sul suo utilizzo")); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h4><strong><?= \Yii::t('app', 'Cookie di profilazione') ?></strong></h4>
            <?= $form->field($model, 'cookieProfilazione')
                ->widget(\kartik\widgets\SwitchInput::className(), [
                    'options' => ['id' => 'switch-profilazione'],
                    'pluginOptions' => [
                        //'size' => 'mini',
                        //                    'onText' => AmosIcons::show('check-circle'),
                        //                    'offText' => AmosIcons::show('circle-o')
                        'onText' => 'Si',
                        'offText' => 'No'
                    ],
                ])
                ->label(\Yii::t('app', "I Cookie di profilazione sono utilizzati per analizzare le caratteristiche della navigazione dell’utente e creare profili in base al suo comportamento sul sito.")); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-right">
            <?php
            $paramsRejectAll = [
                'class' => 'btn btn-default',
                'id' => 'btn-reject-all'
            ];
            if ($model->cookieRejectAll) {
                $paramsRejectAll['disabled'] = true;
                $urlRejectAll = '#';
            }
            ?>
            <?php $isRejectAllDisabled = $model->cookieRejectAll; ?>
            <?= Html::a(\Yii::t('app', "Rifiuta tutti"),
                $urlRejectAll,
                $paramsRejectAll
            ) ?>
            <?= Html::submitButton(\Yii::t('app', 'Conferma selezione'), [
                'class' => 'btn btn-primary'
            ]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
