<?php

/**
 * @package   yii2-grid
 * @version   3.5.0
 */

namespace kartik\grid;

use kartik\base\AssetBundle;

/**
 * Asset bundle for [[EditableColumn]] functionality of the [[GridView]] widget.
 *
 * @since 1.0
 */
class EditableColumnAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->depends = array_merge(["kartik\\grid\\GridViewAsset"], $this->depends);
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/kv-grid-editable']);
        parent::init();
    }
}
