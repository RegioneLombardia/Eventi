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
use open20\amos\core\icons\AmosIcons;
use yii\helpers\Url;

ModuleAdminAsset::register(Yii::$app->view);

/** @var $socialAuthModule \open20\amos\socialauth\Module */
$socialAuthModule = \open20\amos\socialauth\Module::getInstance();


if (\Yii::$app->controller->action->id == 'login-frontend' && YII_ENV == 'prod') {
    $socialAuthModule = \Yii::$app->getModule('socialauthfe');
}
?>
<?php //Html::tag('h2', AmosAdmin::t('amosadmin', '#fullsize_spid'), ['class' => 'title-login']) ?>
<!-- < ?php echo Html::tag('h2', AmosAdmin::t('amosadmin', '<strong>ACCEDI</strong> con IdPC - Sistema di Identity Provider di Regione Lombardia'), ['class' => 'title-login']) ?> -->
<div class="col-sm-12 col-xs-12 nop">
    <?=
    Html::a(
    //AmosIcons::show('account-circle') . AmosAdmin::t('amosadmin', $socialAuthModule->shibbolethConfig['buttonLabel']),
        AmosAdmin::t('amosadmin', 'Accedi con la tua identità digitale'),
        Url::to("/{$socialAuthModule->id}/shibboleth/endpoint", 'https'),
        [
            'class' => 'btn btn-spid',
            'title' => AmosAdmin::t('amosadmin', 'Accedi con la tua identità digitale'),
            //'target' => '_blank'
        ]
    )
    ?>
</div>
<!--<div class="col-xs-12 nop">
    <p class="spid-text">< ?= AmosAdmin::t('amosadmin', $socialAuthModule->shibbolethConfig['buttonDescription']) ?></p>
</div>-->
