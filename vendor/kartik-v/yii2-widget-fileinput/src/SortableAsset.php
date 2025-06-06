<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-fileinput
 * @version 1.1.1
 */

namespace kartik\file;

/**
 * Sortable asset bundle for FileInput Widget
 *
 * @since 1.0
 */
class SortableAsset extends BaseAsset
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setupAssets('js', ['js/plugins/sortable']);
        parent::init();
    }
}