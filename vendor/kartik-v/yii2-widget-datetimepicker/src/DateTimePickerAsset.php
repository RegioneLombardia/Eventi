<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-datetimepicker
 * @version 1.5.1
 */

namespace kartik\datetime;

use kartik\base\AssetBundle;

/**
 * Asset bundle for the [[DateTimePicker]] widget.
 *
 * @since 1.0
 */
class DateTimePickerAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__.'/assets');
        $bsCss = 'bootstrap-datetimepicker'.($this->isBs(3) ? '3' : '4');
        $this->setupAssets('css', ['css/'.$bsCss, 'css/datetimepicker-kv']);
        $this->setupAssets('js', ['js/bootstrap-datetimepicker']);
        parent::init();
    }
}