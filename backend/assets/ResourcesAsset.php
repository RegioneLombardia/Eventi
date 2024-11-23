<?php

namespace app\assets;

/**
 * Application Asset File.
 */
class ResourcesAsset extends \luya\web\Asset
{
    public $sourcePath = '@frontend/resources';

    public $css = [
        '//cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/css/uikit.min.css', 
        
        'font/style-fonts.css',
        'css/main.css',
        'css/header.css',
        'https://cdn.materialdesignicons.com/5.3.45/css/materialdesignicons.min.css',

    ];

    public $js = [
        '//cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/js/uikit.min.js', 
        '//cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/js/uikit-icons.min.js', 
        '//maps.google.com/maps/api/js?key=KEY&language=IT',
        'js/script.js',
        'js/markers.js',
        'js/markerclusterer.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'open20\amos\layout\assets\IconAsset',
        'kartik\icons\FontAwesomeAsset'
    ];
}
