<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-select2
 * @version 2.2.4
 */

namespace kartik\select2;

use kartik\base\AssetBundle;

/**
 * Asset bundle for [[Select2]] Widget. Includes assets from the select2 plugin library.
 *
 * @since 1.0
 */
class Select2Asset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath('@vendor/select2/select2/dist');
        $this->setupAssets('css', ['css/select2']);
        $this->setupAssets('js', ['js/select2.full']);
        parent::init();
    }
}
