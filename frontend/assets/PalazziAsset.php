<?php

namespace app\assets;

/**
 * Application Asset File.
 */
class PalazziAsset extends \luya\web\Asset
{
    public $sourcePath = '@app/resources';

    public $css = [
        '//cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/css/uikit.min.css', //'integrity' => 'sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4', 'crossorigin' => 'anonymous',
        'css/main.css',
        'css/header.css',
        'https://cdn.materialdesignicons.com/5.3.45/css/materialdesignicons.min.css',

    ];

    public $js = [
        '//cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/js/uikit.min.js', //'integrity' => 'sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ', 'crossorigin' => 'anonymous',
        '//cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/js/uikit-icons.min.js', //'integrity' => 'sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm', 'crossorigin' => 'anonymous',
        '//maps.google.com/maps/api/js?key=AIzaSyAs1YQQSSjyfHK1V3Fs4lMIVFPaEHrn4Fw&language=IT',
        'js/script.js',
        'js/markers.js',
        'js/markerclusterer.js',
        'js/slick.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'open20\amos\layout\assets\IconAsset',
    ];
}
