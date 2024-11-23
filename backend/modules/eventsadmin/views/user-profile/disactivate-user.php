<?php

$this->title = \Yii::t('app', "Disattiva utente");
?>
<h3><?=\Yii::t('app', "Se disattivi l'utente non potrai accedere alla piattaforma e non riceverai più notifiche dalla paittaforma.") ?></h3>
<h3><?=\Yii::t('app', "Sei sicuro di disattivare l'utente?") ?></h3>

<?= \yii\helpers\Html::a(\Yii::t('app',"Disattiva"),['/admin/user-profile/deactivate-account','id' => $model->id],[
    'class' => 'btn btn-primary',
    'data-confirm' => \Yii::t('app', "Sei sicuro di disattivare l'utente?")
    ])?>


