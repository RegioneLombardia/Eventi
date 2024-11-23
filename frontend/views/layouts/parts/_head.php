<?php
use yii\helpers\Html;
$url = \Yii::$app->request->absoluteUrl;

$baseUrl = (parse_url($url, PHP_URL_PATH));
$query = (parse_url($url, PHP_URL_QUERY));
$baseUrl.='/amp';
if(!empty($query)){
    $baseUrl.='?'.$query;
}
$ampUrl = \Yii::$app->params['platform']['frontendUrl'].$baseUrl;


?>
<head>
    <?php $this->head() ?>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php if(\Yii::$app->params['prod-mode']){ ?>
        <meta name="robots" content="index, follow" />
    <?php } else{ ?>
        <meta name="robots" content="noindex, nofollow">
    <?php }?>
    <link rel="shortcut icon" href="<?= $assetBundle->baseUrl ?>/img/favicon.png">
    <title><?= $title ?> - <?= Html::encode(Yii::$app->name) ?></title>

    <?php $head ?>

    <link rel="amphtml" href="<?=$ampUrl?>">

</head>