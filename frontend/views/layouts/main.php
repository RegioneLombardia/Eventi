<?php

use app\assets\ResourcesAsset;
use luya\helpers\Url;
use app\modules\seo\frontend\behaviors\LuyaSeoBehavior;
use luya\cms\widgets\LangSwitcher;

$assetBundle = ResourcesAsset::register($this);
\app\assets\SimplePaginationAsset::register($this);

/* @var $this luya\web\View */
/* @var $content string */
$this->attachBehavior('seo', LuyaSeoBehavior::className());

//echo \backend\widgets\SmartAppWidget::widget();
$language = 'it';
if(\Yii::$app->language == 'en-GB'){
    $language = 'en';
}
$this->beginPage();
?>
<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns#" lang="<?= $language ?>">
<!-- HEAD -->
<?= $this->render('parts/_head', [
    'assetBundle' => $assetBundle,
    'title' => $this->title,
]) ?>
<body class="layout-main">


<a href="#main-content" id="skiplinks"
   class="btn btn-link btn-lg sr-only sr-only-focusable"><?= \Yii::t('app', 'Salta al contenuto principale') ?></a>
<?php $this->beginBody() ?>

<!-- NAVBAR -->
<?= $this->render('parts/_navbar', [
    'assetBundle' => $assetBundle
]) ?>
<!-- END: NAVBAR -->


<div id="main-content" class="content">
    <?= $content; ?>
</div>

<?= backend\modules\eventsadmin\widgets\BannerCookieWidget::widget() ?>

<!-- FOOTER -->
<?= $this->render('parts/_footer', [
    'assetBundle' => $assetBundle
]) ?>
<!-- END: FOOTER -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
