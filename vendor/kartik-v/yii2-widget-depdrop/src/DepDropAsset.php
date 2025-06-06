<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-depdrop
 * @version 1.0.6
 */

namespace kartik\depdrop;

use kartik\base\AssetBundle;

/**
 * Asset bundle for Dependent Dropdown widget
 *
 * @since 1.0
 */
class DepDropAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath('@vendor/kartik-v/dependent-dropdown');
        $this->setupAssets('css', ['css/dependent-dropdown']);
        $this->setupAssets('js', ['js/dependent-dropdown']);
        parent::init();
    }
}
