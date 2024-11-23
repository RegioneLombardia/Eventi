<?php

namespace app\assets;

/**
 * Application Asset File.
 */
class ResourcesAsset extends \luya\web\Asset
{
    public $sourcePath = '@app/resources';

    public $css = [
        '//cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/css/uikit.min.css', //'integrity' => 'sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4', 'crossorigin' => 'anonymous',
        // 'https://fonts.googleapis.com/css2?family=Red+Hat+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap',
        // 'https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700;900&display=swap',
        'font/style-fonts.css',
        'css/main.css',
        'css/header.css',
        'https://cdn.materialdesignicons.com/5.3.45/css/materialdesignicons.min.css',

    ];

    public $js = [
        '//cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/js/uikit.min.js', //'integrity' => 'sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ', 'crossorigin' => 'anonymous',
        '//cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/js/uikit-icons.min.js', //'integrity' => 'sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm', 'crossorigin' => 'anonymous',
        'js/script.js',
        'js/markers.js',
        'js/markerclusterer.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'open20\amos\layout\assets\IconAsset',
    ];
}
