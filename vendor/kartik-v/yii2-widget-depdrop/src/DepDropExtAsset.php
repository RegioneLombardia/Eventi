<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-depdrop
 * @version 1.0.6
 */

namespace kartik\depdrop;

use kartik\base\AssetBundle;

/**
 * Asset bundle for Dependent Dropdown Extension for Yii
 *
 * @since 1.0
 */
class DepDropExtAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/depdrop']);
        parent::init();
    }
}
