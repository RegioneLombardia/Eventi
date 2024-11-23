<?php

use app\assets\ResourcesAsset;
use luya\cms\widgets\LangSwitcher;
use app\modules\seo\frontend\behaviors\LuyaSeoBehavior;
use luya\helpers\Url;

$assetBundle = ResourcesAsset::register($this);
\app\assets\SimplePaginationAsset::register($this);

/* @var $this luya\web\View */
/* @var $content string */
$this->attachBehavior('seo', LuyaSeoBehavior::className());
$this->beginPage();

//echo \backend\widgets\SmartAppWidget::widget();

?>
    <!DOCTYPE html>
    <html prefix="og: https://ogp.me/ns#" lang="<?= Yii::$app->composition->language; ?>">
    <!-- HEAD -->
    <?= $this->render('parts/_head', [
        'assetBundle' => $assetBundle,
        'title' => $this->title,

    ]) ?>

    <body class="layout-landing">

    <a href="#main-content" id="skiplinks"
       class="btn btn-link btn-lg sr-only sr-only-focusable"><?= \Yii::t('app', 'Salta al contenuto principale') ?></a>

    <?php $this->beginBody() ?>

    <!-- NAVBAR -->
    <?= $this->render('parts/_navbar', [
        'assetBundle' => $assetBundle
    ]) ?>
    <!-- END: NAVBAR -->

    <?php
    $landingClass = strtolower(str_replace(' ', '-', $this->title));
    ?>
    <div class="content content-landing <?= $landingClass ?>">
        <?= $content; ?>
    </div>
    <!-- -->
    <?= backend\modules\eventsadmin\widgets\BannerCookieWidget::widget() ?>
    <!-- FOOTER -->
    <?= $this->render('parts/_footer', [
        'assetBundle' => $assetBundle
    ]) ?>
    <!--    --><?php //if(open20\amos\core\utilities\MobileUtility::isMobile()){ ?>
    <!--        <div class="col-md-12">-->
    <!--            <p>scarica <a href='www.google.it'>l’app Eventi Lombardia</a></p>-->
    <!--        </div>-->
    <!--   --><?php // } ?>
    <!-- END: FOOTER -->

    <?php $this->endBody() ?>
    </body>

    </html>
<?php $this->endPage() ?>