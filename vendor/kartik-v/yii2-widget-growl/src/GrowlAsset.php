<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-growl
 * @version 1.1.2
 */

namespace kartik\growl;

use kartik\base\AssetBundle;

/**
 * Asset bundle for [[Growl]] widget
 *
 * @since 1.0
 */
class GrowlAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/kv-bootstrap-notify']);
        $this->setupAssets('js', ['js/bootstrap-notify']);
        parent::init();
    }

    /**
     * Adds a theme CSS file
     *
     * @param string $theme the theme name
     *
     * @return kartik\growl\GrowlAsset object instance
     */
    public function addTheme($theme)
    {
        $ext = YII_DEBUG ? '.css' : '.min.css';
        $this->css[] = "css/{$theme}{$ext}";
        return $this;
    }
}