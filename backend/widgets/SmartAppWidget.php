<?php
namespace backend\widgets;


use yii\base\Widget;

class SmartAppWidget extends Widget
{
    public function run()
    {

        $view = $this->getView();
        \app\assets\SmartAppAsset::register($view);

        $view->registerMetaTag(['name' => 'smartbanner:title', 'content' =>  \Yii::t('app','Eventi Regione Lombardia')]);
        $view->registerMetaTag(['name' => 'smartbanner:author', 'content' => 'Regione Lombardia']);
        $view->registerMetaTag(['name' => 'smartbanner:price', 'content' =>  \Yii::t('app','Gratis')]);
        $view->registerMetaTag(['name' => 'smartbanner:price-suffix-apple', 'content' =>  \Yii::t('app',' - In App Store')]);
        $view->registerMetaTag(['name' => 'smartbanner:price-suffix-google', 'content' =>  \Yii::t('app',' - In Google Play')]);
        $view->registerMetaTag(['name' => 'smartbanner:icon-apple', 'content' => \Yii::$app->params['platform']['backendUrl'].'/img/round-poi-logo.png']);
        $view->registerMetaTag(['name' => 'smartbanner:icon-google', 'content' =>  \Yii::$app->params['platform']['backendUrl'].'/img/round-poi-logo.png']);
        $view->registerMetaTag(['name' => 'smartbanner:button', 'content' => \Yii::t('app','Scarica')]);
        $view->registerMetaTag(['name' => 'smartbanner:button-url-apple', 'content' => 'https://apps.apple.com/it/app/example/id']);
        $view->registerMetaTag(['name' => 'smartbanner:button-url-google', 'content' => 'https://play.google.com/store/apps/details?id=iecample']);
        $view->registerMetaTag(['name' => 'smartbanner:enabled-platforms', 'content' => 'android,ios']);
        $view->registerMetaTag(['name' => 'smartbanner:close-label', 'content' =>  \Yii::t('app','Chiudi')]);
    }

}