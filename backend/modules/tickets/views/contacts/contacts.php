<?php

use open20\amos\core\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<div class="content">
    <div class="wrap-modules">
        <div id="layoutsection-77d" class="hero-banner detail-banner uk-section- uk-section-overlap uk-visible@xl">
            <div style="background-image: url('/img/img_default.jpg');"
                 class="uk-background-norepeat uk-background-cover uk-background-center-center uk-section">


                <div class="uk-container">
                </div>


            </div>
        </div>
        <div id="layoutsection-dfc" class="internal-footer-page-section uk-section- uk-visible@xl uk-section">

            <div class="uk-container">
                <div class="container pb-5">
                    <div class="row variable-gutters">
                        <div class="col-lg-8 mt-5">
                            <div class="pr-lg-5">


                                <h1> <?= \Yii::t('app', 'Assistenza tecnica') ?></h1>
                                <p>
                                    <strong><?= \Yii::t('app', 'Se hai domande tecniche o difficoltà nell’utilizzo di questo sito, puoi compilare il modulo in questa pagina.') ?></strong>
                                    <br><a href="https://www.regione.lombardia.it/wps/portal/istituzionale/HP/DettaglioRedazionale/servizi-e-informazioni/cittadini/diritti-e-tutele/identita-digitale-accesso-servizi-online"
                                           target="_blank"><?= \Yii::t('app', 'Informazioni su accesso ai servizi regionali con le credenziali SPID, Carta Nazionale dei Servizi (CNS), Carta di Identità Elettronica (CIE) ed eIDAS (Identificazione Elettronica Europea)') ?></a>
                                </p>

                                <?php
                                if (!is_null($ok)) :
                                    if ($ok) :
                                        ?>
                                        <div class="alert alert-success alert-dismissible show" role="alert">
                                            <?= \Yii::t('app', 'Gentile utente, la tua segnalazione è stata inviata. Ti risponderemo quanto prima.') ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php
                                    else :
                                        ?>
                                        <div class="alert alert-danger alert-dismissible show" role="alert">
                                            <?= \Yii::t('app', "Errore nell'invio della comunicazione...") ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php
                                    endif;
                                endif;
                                ?>

                                <?php
                                // echo $ok;

                                $form = ActiveForm::begin([
                                    'enableClientScript' => true,
                                    'options' => [
                                        'id' => 'personal-data-form',
                                        'class' => 'needs-validation form-rounded',
                                        'enctype' => 'multipart/form-data',
                                    ],
                                ]);
                                ?>
                                <div class="row variable-gutters pt-5">
                                    <div class="col-md-12 nop">

                                        <div class="col-md-6">
                                            <?= $form->field($model, 'nome')->textInput(
                                                [
                                                    'placeholder' => \Yii::t('app', 'inserisci il nome')
                                                ]
                                            )->label(\Yii::t('app', 'Nome') . '*') ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $form->field($model, 'cognome')->textInput(
                                                [
                                                    'placeholder' => \Yii::t('app', 'inserisci il cognome')
                                                ]
                                            )->label(\Yii::t('app', 'Cognome') . '*') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12 nop">

                                        <div class="col-md-6">
                                            <?= $form->field($model, 'email')->textInput(
                                                [
                                                    'placeholder' => \Yii::t('app', 'inserisci il tuo indirizzo email')
                                                ]
                                            )->label('Email*') ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $form->field($model, 'confermaEmail')->textInput(
                                                [
                                                    'placeholder' => \Yii::t('app', 'conferma il tuo indirizzo email')
                                                ]
                                            )->label(\Yii::t('app', 'Conferma email') . '*') ?>
                                        </div>
                                    </div>
                                    <!--                                    <div class="col-md-6">-->
                                    <!--                                        --><?php //$form->field($model, 'telefono')->textInput(
                                    //                                            [
                                    //                                                'placeholder' => \Yii::t('app', 'inserisci il tuo numero di telefono')
                                    //                                            ]
                                    //                                        ) ?>
                                    <!--                                    </div>-->
                                    <div class="col-md-12" hidden>
                                        <?php $model->tipoMessaggio = 2; ?>
                                        <?= $form->field($model, 'tipoMessaggio')->hiddenInput() ?>
                                    </div>
                                    <!-- <div class="col-12">
                                      < ?= $form->field($model, 'messaggio')->textInput() ?>
                                    </div> -->
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'messaggio')->textarea(
                                            [
                                                'placeholder' => 'Scrivi qui il tuo messaggio',
                                                'rows' => 10
                                            ]
                                        )->label(\Yii::t('app', 'Messaggio') . '*') ?>
                                    </div>

                                    <div class="col-md-12">
                                        <?= $form->field($model, 'reCaptcha')->widget(\himiklab\yii2\recaptcha\ReCaptcha2::className(), [
                                            'siteKey' => \Yii::$app->params['google_recaptcha_site_key'], // unnecessary is reCaptcha component was set up

                                        ])->label('') ?>
                                    </div>

                                    <div class="col-md-12">
                                        <?= $form->field($model, 'privacy')->checkBox(
                                            [
                                                'label' => 'Ho preso visione dell\'' . Html::a('informativa privacy', null, ['href' => '/it/privacy', 'title' => 'Leggi l\'informativa privacy', 'target' => '_blank'])
                                            ]
                                        ) ?>
                                    </div>


                                </div>
                                <div>
                                    <?php
                                    echo Html::submitButton(
                                        'Invia',
                                        [
                                            'class' => 'btn btn-primary px-5',
                                            'name' => 'submit-action',
                                            'value' => 'forward',
                                            'data-confirm' => \Yii::t('app', "Sei sicuro di inviare la richiesta?")
                                        ]
                                    );
                                    ?>
                                </div>


                                <?php ActiveForm::end(); ?>

                            </div>
                        </div>
                        <!-- START SIDEBAR -->
                        <div class="ml-lg-auto col-lg-4">

                        </div>

                    </div>
                </div>


            </div>


        </div>
    </div>
</div>
