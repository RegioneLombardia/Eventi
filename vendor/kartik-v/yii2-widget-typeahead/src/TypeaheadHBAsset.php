<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-typeahead
 * @version 1.0.4
 */

namespace kartik\typeahead;

use kartik\base\AssetBundle;

/**
 * Asset bundle for Typeahead Handle Bars plugin
 *
 * @since 1.0
 */
class TypeaheadHBAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/handlebars']);
        parent::init();
    }
}
