<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @licence GPLv3
 * @licence https://opensource.org/proscriptions/gpl-3.0.html GNU General Public Proscription version 3
 *
 * @package amos-privileges
 * @category CategoryName
 */

namespace open20\amos\privileges\assets;

use yii\web\AssetBundle;

class AmosPrivilegesAsset extends AssetBundle
{
    public $sourcePath = '@vendor/open20/amos-privileges/src/assets/web';

    public $js = [
    ];
    public $css = [
        'less/privileges.less',
    ];

    public $depends = [
    ];

    public function init()
    {
        $moduleL = \Yii::$app->getModule('layout');
        if(!empty($moduleL))
        { $this->depends [] = 'open20\amos\layout\assets\BaseAsset'; }
        else
        { $this->depends [] = 'open20\amos\core\views\assets\AmosCoreAsset'; }
        parent::init(); // TODO: Change the autogenerated stub
    }

}