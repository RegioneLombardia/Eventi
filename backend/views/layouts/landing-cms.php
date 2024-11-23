<?php

use app\assets\ResourcesAsset;
use luya\cms\widgets\LangSwitcher;
use app\modules\seo\frontend\behaviors\LuyaSeoBehavior;
use luya\helpers\Url;



/* @var $this luya\web\View */
/* @var $content string */

$this->beginPage();

//echo \backend\widgets\SmartAppWidget::widget();
//\open20\amos\layout\assets\BaseAsset::register($this);

?>
<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns#" lang="<?= Yii::$app->language; ?>">
<!-- HEAD -->
<?= $this->render('@frontend/views/layouts/parts/_head', [
    'assetBundle' => $assetBundle,
    'title' => $this->title,

]) ?>

<body class="layout-landing layout-backend">
<a href="#main-content" id="skiplinks" class="btn btn-link btn-lg sr-only sr-only-focusable"><?= \Yii::t('app', 'Salta al contenuto principale') ?></a>

    <?php $this->beginBody() ?>

    <!-- NAVBAR -->
    <div class="bg-primary">
        <?= $this->render('@frontend/views/layouts/parts/_navbar', [
            'assetBundle' => $assetBundle,
            'fromBackend' => true,
        ]) ?>
    </div>

    <!-- END: NAVBAR -->

    <?php
    $landingClass = strtolower(str_replace(' ', '-', $this->title));
    ?>
    <div id="main-content" class="content content-landing <?= $landingClass ?>">
        <?= $content; ?>
    </div>
    <!-- -->
    <?= backend\modules\eventsadmin\widgets\BannerCookieWidget::widget() ?>
    <!-- FOOTER -->
<!--    --><?php //echo $this->render('parts/_footer', [
//        'assetBundle' => $assetBundle
//    ]) ?>
    <!--    --><?php //if(open20\amos\core\utilities\MobileUtility::isMobile()){ 
                ?>
    <!--        <div class="col-md-12">-->
    <!--            <p>scarica <a href='www.google.it'>l’app Eventi Lombardia</a></p>-->
    <!--        </div>-->
    <!--    --><?php // } 
                ?>
    <!-- END: FOOTER -->

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>