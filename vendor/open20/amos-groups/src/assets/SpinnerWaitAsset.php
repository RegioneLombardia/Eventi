<?php
/**
 */

namespace open20\amos\groups\assets;

use yii\web\AssetBundle;


class SpinnerWaitAsset extends AssetBundle
{
    public $sourcePath = '@vendor/open20/amos-groups/src/assets/web';

    public $css = [
        'css/spinner.css'
    ];
    public $js = [
    ];
    public $depends = [
    ];
}
