<?php

/**
 * @package yii2-sortable
 * @version 1.2.2
 */

namespace kartik\sortable;

use kartik\base\AssetBundle;
/**
 * Asset bundle for the [[Sortable]] widget
 *
 * @since 1.0
 */
class SortableAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/kv-html5-sortable']);
        $this->setupAssets('js', ['js/html5sortable', 'js/kv-html5-sortable']);
        parent::init();
    }

}