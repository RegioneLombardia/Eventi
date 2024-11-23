<?php

use app\assets\PalazziAsset;
use luya\cms\widgets\LangSwitcher;
use app\modules\seo\frontend\behaviors\LuyaSeoBehavior;
use luya\helpers\Url;

$assetBundle = PalazziAsset::register($this);
\app\assets\SimplePaginationAsset::register($this);

/* @var $this luya\web\View */
/* @var $content string */
$this->attachBehavior('seo', LuyaSeoBehavior::className());
$this->beginPage();

//echo \backend\widgets\SmartAppWidget::widget();
$paddingPalazzoLombardia = (!empty($fromBackend) || Yii::$app->menu->current->getAlias() == 'palazzo-lombardia') ? "pb-0" : "";
$paddingGrattacieloPirelli = (!empty($fromBackend) || Yii::$app->menu->current->getAlias() == 'grattacielo-pirelli') ? "pb-0" : "";
?>

    <!DOCTYPE html>

    <html prefix="og: https://ogp.me/ns#" lang="<?= Yii::$app->composition->language; ?>" class="<?= $paddingPalazzoLombardia . $paddingGrattacieloPirelli ?>">
    <!-- HEAD -->
    <?= $this->render('parts/_head', [
        'assetBundle' => $assetBundle,
        'title' => $this->title,

    ]) ?>

    <body class="page-layout-palazzi">

    <a href="#main-content" id="skiplinks"
       class="btn btn-link btn-lg sr-only sr-only-focusable"><?= \Yii::t('app', 'Salta al contenuto principale') ?></a>

    <?php $this->beginBody() ?>

    <!-- NAVBAR -->
    <?= $this->render('parts/_navbar', [
        'assetBundle' => $assetBundle
    ]) ?>
    <!-- END: NAVBAR -->


    <!-- MENU PIANI -->
    <?= $this->render('parts/_menu-piani', [
        'assetBundle' => $assetBundle
    ]) ?>

    <!-- END MENU PIANI -->

    <?php
    $landingClass = strtolower(str_replace(' ', '-', $this->title));
    ?>
    <div id="main-content" class="content content-landing <?= $landingClass ?>">
        <?= $content; ?>
    </div>
    <!-- -->
    <?= backend\modules\eventsadmin\widgets\BannerCookieWidget::widget() ?>

    <!-- FOOTER -->
    <?= $this->render('parts/_footer', [
        'assetBundle' => $assetBundle
    ]) ?>
    <!--    --><?php //if(open20\amos\core\utilities\MobileUtility::isMobile()){ 
    ?>
    <!--        <div class="col-md-12">-->
    <!--            <p>scarica <a href='www.google.it'>l’app Eventi Lombardia</a></p>-->
    <!--        </div>-->
    <!--   --><?php // } 
    ?>
    <!-- END: FOOTER -->

    <?php $this->endBody() ?>
    </body>

    </html>
<?php $this->endPage() ?>