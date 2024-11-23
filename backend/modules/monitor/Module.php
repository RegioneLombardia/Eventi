<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

namespace backend\modules\monitor;

use open20\amos\core\module\AmosModule;

/**
 * registry module definition class
 */
class Module extends AmosModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\monitor\controllers';
    public $newFileMode = 0666;
    public $name = 'monitor';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        \Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php'));

    }

    protected function getDefaultModels()
    {
        return [];
    }

    /**
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'monitor';
    }

    public function getWidgetGraphics()
    {
        return NULL;
    }

    public function getWidgetIcons()
    {
        return [

        ];
    }
}
