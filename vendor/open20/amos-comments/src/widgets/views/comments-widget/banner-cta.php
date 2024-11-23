<?php

use open20\amos\comments\assets\CommentsAsset;
use open20\amos\comments\AmosComments;
$asset = CommentsAsset::register($this);
?>
<div class="container">
<div id="box-partecipa" class="footer_box m-t-30">
    <div class="uk-container">
        <span class="triangle"></span>
        <div class="row">
            <div class="col-md-9">
                <h3 class="m-t-15"><strong><?= AmosComments::t('amoscomments', 'Vuoi essere sempre aggiornato?');?></strong></h3>
                <p class="lead"><?= AmosComments::t('amoscomments',  'Partecipa attivamente, accedi o registrati a '); ?><?= Yii::$app->name ?></p>
            </div>
            <div class="col-md-3 text-right m-t-20">
                <a class="btn btn-lg btn-tertiary" href="<?= \Yii::$app->params['linkConfigurations']['loginLinkCommon'] ?>" title="Partecipa"><?=\Yii::t('app', 'Partecipa');?></a>
            </div>
        </div>
    </div>
</div>
</div>
