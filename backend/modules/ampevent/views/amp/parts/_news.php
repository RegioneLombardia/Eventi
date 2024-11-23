<?php
$blockItemId ='';
$module =  \Yii::$app->getModule('backendobjects');
//$route = $module::getSeoUrl($model->getPrettyUrl(), $blockItemId);
$route = $module::getDetachUrl($model->id, get_class($model), "@frontend/modules/backendobjects/frontend/views/default/detailNews",$model->getPrettyUrl());
?>
    <a href="<?=$route?>" title="Leggi tutto">
        <div class="item-news-home">

            <h4><?= $model->titolo; ?></h4>
        </div>
    </a>
