<?php

use open20\amos\admin\AmosAdmin;

?>
<div class="alert alert-info fade in alert-dismissible banner-cookie" style="max-height: 85vh;">
    <div class="container">



        <?php
        $currentUrl = \Yii::$app->request->absoluteUrl;
        $urlRejectAll = \Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/reject-all-cookie?urlRedirect=' . urlencode($currentUrl); ?>
        <a href="<?=$urlRejectAll?>" class="close" aria-label="close" title="close">×</a>
        <strong><?= AmosAdmin::t('amosadmin', 'Impostazioni privacy') ?></strong>
        <p>
            <?= AmosAdmin::t('amosadmin', 'Questo sito fa uso di cookie tecnici necessari al corretto funzionamento delle pagine web e, previa accettazione da parte dell’utente, di cookie analytics e di profilazione per migliorare l’esperienza di navigazione degli utenti ed ottimizzare l’utilizzo dei servizi messi a disposizione. 
Selezionando <strong>Accetta tutti i cookie</strong> si acconsente all’utilizzo dei cookie analytics e di profilazione. Chiudendo il banner verranno utilizzati solo i cookie tecnici necessari alla navigazione e alcune funzionalità contenuti potrebbero non essere disponibili.
La preferenza può essere modificata in qualsiasi momento accedendo dal footer alla pagina Impostazione cookie
') ?>
        </p>

        <p><?= AmosAdmin::t('amosadmin', "Per conoscere i dettagli consulta la nostra <a href='{link_cookie}'><strong>cookie policy</strong></a> e la nostra <a href='{link_privacy}'><strong>privacy policy</strong></a>", [
                'link_cookie' => Yii::$app->params['platform']['frontendUrl'] . '/it/note-legali-e-cookie-policy',
                'link_privacy' => Yii::$app->params['platform']['frontendUrl'] . '/it/privacy'

            ]) ?></p>
        <div class="row">
            <div class="col-md-6 col-md-offset-6 button-cookie">
                <?php
                $urlPersonalize = Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/personalize-cookie?urlRedirect=' . urlencode(\Yii::$app->request->absoluteUrl);
                $urlAcceptAll = Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/accept-all-cookie?urlRedirect=' . urlencode(\Yii::$app->request->absoluteUrl);
                ?>
                <?= \yii\helpers\Html::a(AmosAdmin::t('amosadmin', "Personalizza"), $urlPersonalize, [
                    'class' => 'btn btn-default btn-lg btn-outline-primary'
                ]); ?>
                <?= \yii\helpers\Html::a(AmosAdmin::t('amosadmin', "Accetta tutti i cookie"), $urlAcceptAll, [
                    'class' => 'btn btn-primary btn-lg'
                ]); ?>
            </div>
        </div>
    </div>
</div>