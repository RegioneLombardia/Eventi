<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var User */

$appLink = Yii::$app->urlManager->createAbsoluteUrl(['/']);
$appName = Yii::$app->name;

//$this->title ='Richiesta Informazioni';
$this->registerCssFile('https://fonts.googleapis.com/css?family=Roboto');
?>


<table width=" 600" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <td>
            <div class="corpo"
                 style="padding:10px;margin-bottom:10px;background-color:#fff;">
                <div class="sezione titolo" style="overflow:hidden;color:#000000;">
                    <h2 style="padding:5px 0;	margin:0;">Dati utente segnalante</h2>
                </div>
                <?php if (!empty($modelProfile)) { ?>
                    <div class="sezione" style="overflow:hidden;color:#000000;">
                        <div class="testo">
                            <p>
                                <strong>Username</strong>: <?=
                                Yii::t('mail/assistenza/richiesta-info', '{username}',
                                    [
                                    'username' => Html::encode($modelProfile->user->username)]
                                );
                                ?>
                            </p>
                            <p>
                                <strong><?= \Yii::t('amosapp', 'Nome') ?></strong>: <?=
                                Yii::t('mail/assistenza/richiesta-info', '{first_name}',
                                    [
                                    'first_name' => Html::encode($modelProfile->nome)]
                                );
                                ?>
                            </p>
                            <p>
                                <strong><?= \Yii::t('amosapp', 'Cognome') ?></strong>: <?=
                                Yii::t('mail/assistenza/richiesta-info', '{surname}',
                                    [
                                    'surname' => Html::encode($modelProfile->cognome)]
                                );
                                ?>
                            </p>
                            <p>
                                <strong>Email</strong>: <?=
                                Yii::t('mail/assistenza/richiesta-info', '{email}',
                                    [
                                    'email' => Html::encode($modelProfile->user->email)
                                ]);
                                ?>
                            </p>
                        </div>

                    </div>
                <?php } else { ?>
                    <div class="sezione" style="overflow:hidden;color:#000000;">
                        <div class="testo">
                            <p>Utente non loggato.</p>
                        </div>
                    </div>
                <?php } ?>

                <div class="sezione titolo" style="overflow:hidden;color:#000000;">
                    <h2 style="padding:5px 0;	margin:0;"><?= \Yii::t('amosapp', 'Richiesta di assistenza') ?></h2>
                </div>
                <div class="sezione" style="overflow:hidden;color:#000000;">
                    <div class="testo">
                        <p>
                            <strong><?= \Yii::t('amosapp', 'Nome') ?></strong>: <?=
                            Yii::t('mail/assistenza/richiesta-info', '{first_name}',
                                [
                                'first_name' => Html::encode($model->first_name)]
                            );
                            ?>
                        </p>
                        <p>
                            <strong><?= \Yii::t('amosapp', 'Cognome') ?></strong>: <?=
                            Yii::t('mail/assistenza/richiesta-info', '{surname}',
                                [
                                'surname' => Html::encode($model->surname)]
                            );
                            ?>
                        </p>
                        <p>
                            <strong>Email</strong>: <?=
                            Yii::t('mail/assistenza/richiesta-info', '{email}',
                                [
                                'email' => Html::encode($model->email)
                            ]);
                            ?>
                        </p>
                        <p>
                            <strong><?= \Yii::t('amosapp', 'Telefono') ?></strong>: <?=
                            Yii::t('mail/assistenza/richiesta-info', '{telephone}',
                                [
                                'telephone' => Html::encode($model->telephone)]
                            );
                            ?>
                        </p>
                        <p>
                            <strong><?= \Yii::t('amosapp', 'Messaggio') ?></strong>: <?=
                            Yii::t('mail/assistenza/richiesta-info', '{message}',
                                [
                                'message' => Html::encode($model->message)]
                            );
                            ?>
                        </p>
                    </div>

                </div>

            </div>
        </td>
    </tr>
</table>


