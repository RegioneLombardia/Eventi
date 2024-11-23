<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\core
 * @category   CategoryName
 */

use open20\amos\core\components\AmosView;
use open20\amos\dashboard\models\AmosWidgets;
use yii\helpers\Url;
use app\components\CmsHelper;
use yii\helpers\Html;



////\bedezign\yii2\audit\web\JSLoggingAsset::register($this);

/* @var $this \yii\web\View */
/* @var $content string */

$urlCorrente = Url::current();
$arrayUrl = explode('/', $urlCorrente);
$countArrayUrl = count($arrayUrl);
$percorso = '';
$i = 0;
$moduloId = Yii::$app->controller->module->id;
$basePath = Yii::$app->getBasePath();
if ($moduloId != 'app-backend' && $moduloId != 'app-frontend') {
    $basePath = \Yii::$app->getModule($moduloId)->getBasePath();
    $percorso .= '/modules/' . $moduloId . '/views/' . $arrayUrl[$countArrayUrl - 2];
} else {
    $percorso .= 'views';
    while ($i < ($countArrayUrl - 1)) {
        $percorso .= $arrayUrl[$i] . '/';
        $i++;
    }
}
if ($countArrayUrl) {
    $posizioneEsclusione = strpos($arrayUrl[$countArrayUrl - 1], '?');
    if ($posizioneEsclusione > 0) {
        $vista = substr($arrayUrl[$countArrayUrl - 1], 0, $posizioneEsclusione);
    } else {
        $vista = $arrayUrl[$countArrayUrl - 1];
    }
    if (file_exists($basePath . '/' . $percorso . '/help/' . $vista . '.php')) {
        $this->params['help'] = [
            'filename' => $vista
        ];
    }
    if (file_exists($basePath . '/' . $percorso . '/intro/' . $vista . '.php')) {
        $this->params['intro'] = [
            'filename' => $vista
        ];
    }
}
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <?php $isLuyaApplication = get_class(\Yii::$app) == 'luya\web\Application';?>

    <?= $this->render("parts" . DIRECTORY_SEPARATOR . "head", [
        'title' => (($isLuyaApplication && Yii::$app->isCmsApplication()) && !empty($this->params['titleSection'])) ? $this->params['titleSection'] : $this->title
    ]); ?>
</head>

<body>

    <!-- add for fix error message Parametri mancanti -->
    <input type="hidden" id="saveDashboardUrl" value="<?= Yii::$app->urlManager->createUrl(['dashboard/manager/save-dashboard-order']); ?>" />

    <?php $this->beginBody() ?>


        <?php
        $currentAsset = isset($currentAsset) ? $currentAsset : open20\amos\layout\assets\BiLessAsset::register($this);
        ?>
    <?=
    $this->render(
        "parts" . DIRECTORY_SEPARATOR . "bi-less-header",
        [
            'currentAsset' => $currentAsset,
            'privacyPolicyLink' => \Yii::$app->params['platform']['frontendUrl'] . \Yii::$app->params['linkConfigurations']['privacyPolicyLinkCommon'],
            'cookiePolicyLink' => \Yii::$app->params['platform']['frontendUrl'] . \Yii::$app->params['linkConfigurations']['cookiePolicyLinkCommon'],
            'hideHamburgerMenu' => true,
            'alwaysHamburgerMenu' => true,
            'hideGlobalSearch' => true,
            'hideLangSwitchMenu' => true,
            'frontendUrl' => \Yii::$app->params['platform']['frontendUrl'],
        ]
    );
    ?>
        <!--< ?= $this->render("parts" . DIRECTORY_SEPARATOR . "logo"); ?>-->


    <?php if (isset(Yii::$app->params['logo-bordo'])) : ?>

        <div class="container-bordo-logo"><img src="<?= Yii::$app->params['logo-bordo'] ?>" alt=""></div>
    <?php endif; ?>

    <section id="bk-page" class="fullsizeNetworkLayout" role="main">

        <div class="container">
            <?= $this->render("parts" . DIRECTORY_SEPARATOR . "messages"); ?>

            <?= $this->render("parts" . DIRECTORY_SEPARATOR . "help"); ?>

            <?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-breadcrumbs"); ?>
        </div>


        <div class="dashboard-content">

            <?= $this->render("parts" . DIRECTORY_SEPARATOR . "network_scope"); ?>

            <!-- <div class="network-breadcrumb">
                < ?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-breadcrumbs"); ?>
            </div> -->

            <?php if ($this instanceof \open20\amos\core\components\AmosView) : ?>
                <?php $this->beginViewContent() ?>
            <?php endif; ?>

            <?= $content ?>

            <?php if ($this instanceof AmosView) : ?>
                <?php $this->endViewContent() ?>
            <?php endif; ?>

        </div>
    </section>


       <?php
if (isset(\Yii::$app->view->params['hideCookieBar'])) {
        $hideCookieBarCheck = (\Yii::$app->view->params['hideCookieBar']);
    } else {
        if (isset(\Yii::$app->params['layoutConfigurations']['hideCookieBar'])) {
            $hideCookieBarCheck = (\Yii::$app->params['layoutConfigurations']['hideCookieBar']);
        } else {
            $hideCookieBarCheck = false;
        }
    }
?>
<?php if ($hideCookieBarCheck) : ?>
            <?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-less-cookiebar", [
                'currentAsset' => $currentAsset,
                'cookiePolicyLink' => \Yii::$app->params['linkConfigurations']['cookiePolicyLinkCommon']
            ]); ?>
        <?php endif ?>
        <?= $this->render("parts" . DIRECTORY_SEPARATOR . "bi-backtotop-button"); ?>


    <?php /* echo $this->render("parts" . DIRECTORY_SEPARATOR . "assistance"); */ ?>

   

    <?php $this->endBody() ?>

</body>

</html>
<?php $this->endPage() ?>