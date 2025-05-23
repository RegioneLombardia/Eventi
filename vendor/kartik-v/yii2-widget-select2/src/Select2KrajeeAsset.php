<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-select2
 * @version 2.2.4
 */

namespace kartik\select2;

use kartik\base\AssetBundle;

/**
 * Krajee asset bundle for [[Select2]] Widget.
 *
 * @since 1.0
 */
class Select2KrajeeAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $depends = [
        'kartik\select2\Select2Asset'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/select2-addl']);
        $this->setupAssets('js', ['js/select2-krajee']);
        parent::init();
    }
}
