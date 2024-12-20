<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @licence GPLv3
 * @licence https://opensource.org/proscriptions/gpl-3.0.html GNU General Public Proscription version 3
 *
 * @package amos-admin
 * @category CategoryName
 */

use open20\amos\admin\AmosAdmin;
use open20\amos\core\helpers\Html;
use open20\amos\admin\assets\ModuleAdminAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

ModuleAdminAsset::register(Yii::$app->view);

/**
 * @var $socialAuthModule \open20\amos\socialauth\Module
 */
$socialAuthModule = Yii::$app->getModule('socialauth');
$adminModule = Yii::$app->getModule(AmosAdmin::getModuleName());

//change social url

$urlSocial = ($type == 'login') ? '/socialauth/social-auth/sign-in?provider=' : '/socialauth/social-auth/sign-up?provider=';

if(empty($communityId)) {
    $communityId = \Yii::$app->request->get('community');
}
$paramCommunity = '';
$paramsRedirectUrl = '';
if($communityId){
    $paramCommunity = '&community='.$communityId;
}else if($redirectUrl){
    $paramsRedirectUrl = '';/**'&redirectUrl='.$redirectUrl;*/
}

if(!empty($adminModule) && $adminModule->enableDlSemplification){
    $redirectTo = \Yii::$app->params['platform']['backendUrl'].'/'.AmosAdmin::getModuleName().'/security/reconciliation';
    $paramsRedirectUrl.= '&redirectTo='.$redirectTo;
}



?>
<?= Html::tag('h5', ($type == 'login') ? AmosAdmin::t('amosadmin', '#fullsize_social_title_login') : AmosAdmin::t('amosadmin', '#fullsize_social_title_register'), ['class' => 'title-login']) ?>
<div class="social-buttons row">
    <?php
    foreach ($socialAuthModule->providers as $name => $config) :
        ?>

        <div class="col-xs-12 nop" style="max-width:50%;">
            <a
                    class="btn btn-<?= strtolower($name); ?> btn-block social-link"
                    title="<?= ($type == 'login') ? AmosAdmin::t('amosadmin', '#login_with_social') : AmosAdmin::t('amosadmin', '#register_with_social') ?> <?= $name; ?>"
                    target="_self"
                    href="<?= Yii::$app->urlManager->createAbsoluteUrl($urlSocial . strtolower($name).$paramCommunity.$paramsRedirectUrl); ?>"
            >
                <span class="am am-<?= strtolower($name); ?>"></span>
                <span class="text"><?= AmosAdmin::t('amosadmin', '#register_with_social_label_btn') . ' ' . $name; ?></span>
            </a>
        </div>
    <?php endforeach; ?>
</div>
