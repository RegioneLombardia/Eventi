<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-touchspin
 * @version 1.2.4
 */

namespace kartik\touchspin;

use kartik\base\AssetBundle;

/**
 * Asset bundle for TouchSpin Widget
 *
 * @since 1.0
 */
class TouchSpinAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/jquery.bootstrap-touchspin']);
        $this->setupAssets('js', ['js/jquery.bootstrap-touchspin']);
        parent::init();
    }
}
