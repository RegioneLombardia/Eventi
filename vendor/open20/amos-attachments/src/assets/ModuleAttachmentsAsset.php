<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\news
 * @category   CategoryName
 */

namespace open20\amos\attachments\assets;

use yii\web\AssetBundle;
use open20\amos\core\widget\WidgetAbstract;

class ModuleAttachmentsAsset extends AssetBundle
{
    public $sourcePath = '@vendor/open20/amos-attachments/src/assets/web';

    public $css = [
    ];
    public $js = [
        'js/attachments.js'
    ];
    public $depends = [
    ];

    public function init()
    {

        if (!empty(\Yii::$app->params['bsVersion']) && \Yii::$app->params['bsVersion'] == '4.x') {

        } else {
            if (!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
                $this->css = ['less/attachments_fullsize.less','less/attachments_design_bi.less', 'less/databank_attachments_design_bi.less'];
            } else {
                $this->css = ['less/attachments.less'];
            }
        }

        parent::init();
    }
}