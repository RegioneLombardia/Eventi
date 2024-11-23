<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 22/11/2021
 * Time: 15:42
 */

namespace open20\amos\events\assets;


use yii\web\AssetBundle;

class FlagAssets extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/open20/amos-events/src/assets/web/';
    public $baseUrl = '@web/';

    /**
     * @inheritdoc
     */
    public $js = [
    ];

    /**
     * @inheritdoc
     */
    public $css = [
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
    ];


}