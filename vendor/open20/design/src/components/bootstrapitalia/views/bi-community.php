<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    [NAMESPACE_HERE]
 * @category   CategoryName
 */

use open20\design\assets\BootstrapItaliaDesignAsset;
use open20\design\Module;

$bootstrapItaliaAsset = BootstrapItaliaDesignAsset::register($this);

$image = (isset($image)) ? $image :  'https://picsum.photos/800/450?random=6';

$isGuest = (isset($isGuest)) ? $isGuest :  \Yii::$app->user->isGuest;
$isSigned = (isset($isSigned)) ? $isSigned :  false;
if (isset($hideAllCtaGuest) && $hideAllCtaGuest) {
    $hideAllCtaGuest = \Yii::$app->user->isGuest;
}

if ($isSigned) {
    $dateSigned = (isset($dateSigned)) ? \Yii::$app->formatter->asDate($dateSigned, 'php:d M Y') :  false;
    $url = $urlSigned;
}

$isClosedCommunity = (isset($isClosedCommunity)) ? $isClosedCommunity : false;
$isPrivateCommunity = (isset($isPrivateCommunity)) ? $isPrivateCommunity : false;
$isWaitingToSigned = (isset($isWaitingToSigned)) ? $isWaitingToSigned : false;


$labelCtaSubscribe = (isset($customLabelCtaSubscribe)) ? $customLabelCtaSubscribe : Module::t('amosdesign', 'Richiedi iscrizione');
$labelCtaWaiting = (isset($customLabelCtaWaiting)) ? $customLabelCtaWaiting : Module::t('amosdesign', 'Richiesta iscrizione inviata');
$labelCtaWaitingTooltip = (isset($customLabelCtaWaitingTooltip)) ? $customLabelCtaWaitingTooltip : Module::t('amosdesign', 'Sei in attesa che un community manager convalidi la richiesta per poter accedere alla community');
$labelCtaView = (isset($labelCtaView)) ? $labelCtaView : Module::t('amosdesign', 'Visita la community');
$titleCtaView = (isset($titleCtaView)) ? $titleCtaView : Module::t('amosdesign', 'Vai alla community {title} ', ['title' => $title]);

$socialAuthModule = Yii::$app->getModule('socialauth');
$labelSigninOrSignup = Module::t('amosdesign', 'Accedi o registrati');
if ($socialAuthModule && ($socialAuthModule->enableRegister == false)) {
    $labelSigninOrSignup = Module::t('amosdesign', 'Accedi');
}
$tooltipPreventCtaView = Module::t(
    'amosdesign',
    '{labelSigninOrSignup} alla piattaforma {platformName} per visitare la community',
    ['labelSigninOrSignup' => $labelSigninOrSignup, 'platformName' => \Yii::$app->name]
);

$model     = (isset($model) ? $model : null);
$actionModify      = (isset($actionModify) ? $actionModify : null);
$actionDelete      = (isset($actionDelete) ? $actionDelete : null);

if ($isClosedCommunity) {
    $tooltipTitle = Module::t('amosdesign', "Community ristretta ai partecipanti");
    $tooltipClass = "bg-danger";
    $tooltipIcon = "eye-off";
} else if ($isPrivateCommunity) {
    $tooltipTitle = Module::t('amosdesign', "Community riservata ai partecipanti");
    $tooltipClass = "bg-warning";
    $tooltipIcon = "lock";
} else {
    //isOpenCommunity
    $url = $urlSigned;
    $tooltipTitle = Module::t('amosdesign', "Community aperta");
    $tooltipClass = "bg-success";
    $tooltipIcon = "lock-open";
}

$js = <<<JS
$(document).ready(function () {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0|| !!navigator.userAgent.match(/Trident.*rv\:11\./)) /*Seleziona solo IE*/
    {   
        var max = 55;
        var tot, str;
        $('.community-title').each(function() {
            str = String($(this).html());
            tot = str.length;
            str = (tot <= max) ? str : str.substring(0,(max + 1))+"...";
            $(this).html(str);
        });
    }
});
JS;
$this->registerJs($js);

?>

<div class="community-container p-3 <?= $widthColumn ?> d-flex flex-column justify-content-center align-items-center mb-3 mb-md-5">
    <div class="position-relative">
        <div class="community-image img-shimmer rounded">
            <?php if ($hideAllCtaGuest) : ?>
                <img alt="immagine community" class="h-100 w-100 shimmer-image" src="<?= $image ?>">
            <?php elseif ($isClosedCommunity && !$isSigned) : ?>
                <img alt="immagine community" class=" h-100 w-100 shimmer-image" src="<?= $image ?>">
            <?php elseif ($isGuest) : ?>
                <img alt="immagine community" class=" h-100 w-100 shimmer-image" src="<?= $image ?>">
            <?php else : ?>
                <a href="<?= $url ?>" title="<?= $title ?>">
                    <img alt="immagine community" class="h-100 w-100 shimmer-image" src="<?= $image ?>">
                </a>
            <?php endif; ?>
        </div>
        <a href="javascript::void(0)" class="position-absolute" data-toggle="tooltip" title="<?= $tooltipTitle ?>">
            <svg class="icon icon-white icon-sm p-1 rounded-circle <?= $tooltipClass ?>" role="img">
                <use xlink:href="<?= $bootstrapItaliaAsset->baseUrl ?>/sprite/material-sprite.svg#<?= $tooltipIcon ?>"></use>
            </svg>
            <span class="sr-only"><?= $tooltipTitle ?></span>
        </a>
        <div class="ml-2 position-absolute community-context-menu">
            <?php
            echo $this->render(
                '@vendor/open20/design/src/components/bootstrapitalia/views/bi-context-menu-widget',
                [
                    'buttons' => \open20\amos\core\utilities\ButtonUtility::composeContextMenuButtons($model, $actionModify, $actionDelete),
                    'model' => $model
                ]
            );
            ?>
        </div>

    </div>

    <div class="community-title-container mw-100 py-3 ">
        <?php if ($hideAllCtaGuest) : ?>
            <h3 class="community-title h6 font-weight-bold pb-0 mb-0 text-center title-two-line text-truncate"><?= $title ?></h3>
        <?php elseif ($isClosedCommunity && !$isSigned) : ?>
            <h3 class="community-title h6 font-weight-bold pb-0 mb-0 text-center title-two-line text-truncate"><?= $title ?></h3>
        <?php elseif ($isGuest) : ?>
            <h3 class="community-title h6 font-weight-bold pb-0 mb-0 text-center title-two-line text-truncate"><?= $title ?></h3>
        <?php else : ?>
            <a href="<?= $url ?>" class="link-list-title" title="<?= $titleCtaView ?>">
                <h3 class="community-title h6 font-weight-bold pb-0 mb-0 text-center overflow-hidden title-two-line text-truncate"><?= $title ?></h3>
            </a>
        <?php endif ?>
    </div>
    <div class="mt-auto text-center">

        <?php if (!$hideAllCtaGuest) : ?>
            <?php if ($isSigned) : ?>
                <?php if ($dateSigned) : ?>
                    <small class="text-muted mt-2">
                        <?= Module::t('amosdesign', 'Iscritto il') . ' ' ?><?= $dateSigned ?>
                    </small>
                <?php endif ?>
                <div class="button-container w-100 d-flex justify-content-center border-top">
                    <a href="<?= $url ?>" title="<?= $titleCtaView ?>" class="btn btn-link btn-xs mt-2 font-weight-bold"><?= $labelCtaView ?></a>
                </div>
            <?php else : ?>
                <?php if ($isOpenCommunity) : ?>
                    <?php if ($isGuest) : ?>
                        <div class="button-container w-100 d-flex justify-content-center border-top">
                            <a href="javascript::void(0)" class="btn btn-primary btn-xs mt-2 disabled disabled-with-pointer-events" data-toggle="tooltip" data-html="true" data-original-title="<?= $tooltipPreventCtaView ?>"><?= $labelCtaView ?></a>
                        </div>
                    <?php else : ?>
                        <div class="button-container w-100 d-flex justify-content-center border-top">
                            <a href="<?= $url ?>" title="<?= $titleCtaView ?>" class="btn btn-primary btn-xs mt-2"><?= $labelCtaView ?></a>
                        </div>
                    <?php endif; ?>
                <?php elseif ($isPrivateCommunity) : ?>
                    <?php if ($isWaitingToSigned) : ?>
                        <small class="d-flex align-items-end text-muted mt-2">
                            <?= $labelCtaWaiting ?>
                            <a href="javascript::void(0)" class="bi-form-field-tooltip-info" data-toggle="tooltip" data-html="true" data-original-title="<?= $labelCtaWaitingTooltip ?>">
                                <span class="icon ml-1">
                                    <svg class="icon icon-xs design-theme-fill-text-muted">
                                        <use xlink:href="<?= $bootstrapItaliaAsset->baseUrl ?>/node_modules/bootstrap-italia/dist/svg/sprite.svg#it-info-circle"></use>
                                    </svg>
                                </span>
                                <span class="sr-only"><?= $labelCtaWaitingTooltip ?></span>
                            </a>
                        </small>
                    <?php endif ?>
                    <div class="button-container w-100 d-flex justify-content-center border-top">
                        <a href="<?= $url ?>" title="<?= $titleCtaView ?>" class="btn btn-primary btn-xs mt-2"><?= $labelCtaView ?></a>
                    </div>

                <?php else : ?>
                    <!-- $isClosedCommunity -->
                    <!-- I can see closed community only isSigned -->
                <?php endif ?>
            <?php endif ?>
        <?php endif ?>
    </div>
</div>