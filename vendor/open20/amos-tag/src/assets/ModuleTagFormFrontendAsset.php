<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\tag
 * @category   CategoryName
 */

namespace open20\amos\tag\assets;

use open20\amos\core\widget\WidgetAbstract;
use yii\web\AssetBundle;

class ModuleTagFormFrontendAsset extends AssetBundle
{
    public $sourcePath = '@vendor/open20/amos-tag/src/assets/web';

    
    public $js = [
        'js/tag-form.js', 
    ]; 
    public $depends = [
    ];

    /*
    //force reset cache asset (devel ONLY)
    public $publishOptions = [
        'forceCopy'=>true,
    ];
    */

}
