<?php

use app\modules\cms\helpers\CmsHelper;
use open20\amos\news\AmosNews;
use app\assets\ResourcesAsset;

$resourceAsset = ResourcesAsset::register($this);


$truncate = 250;
$url = \Yii::$app->params['platform']['backendUrl'] . '/img/img_default.jpg';
$module = \Yii::$app->getModule('backendobjects');
?>


<?php
$route = $module::getDetachUrl($model->id, get_class($model), "@frontend/modules/backendobjects/frontend/views/default/detailNews", $model->getPrettyUrl());
if (\Yii::$app->language == 'en-GB') {
    $route .= "&language=en-GB";
}

//if (!empty($model->newsImage)) {
//    $url = $model->newsImage->getWebUrl('social', false, true);
//}
?>
<div class="uk-width-4-5">
    <a href="<?= $route ?>" title="Leggi tutto">
        <div class="item-news-home">

            <h4><?= $model->getTitle(); ?></h4>
        </div>
    </a>
</div>