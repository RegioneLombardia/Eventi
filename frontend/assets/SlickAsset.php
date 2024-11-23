<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 04/05/2021
 * Time: 10:54
 */

namespace app\assets;


use yii\web\AssetBundle;

class SlickAsset extends AssetBundle
{
    public $sourcePath = '@bower/slick-carousel/slick/';

    public $css = [
        'slick.css',
        'slick-theme.css',
    ];

    public $js = [
        'slick.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}