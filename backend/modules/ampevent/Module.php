<?php

namespace backend\modules\ampevent;

use open20\amos\core\module\AmosModule;

/**
 * registry module definition class
 */
class Module extends AmosModule
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\ampevent\controllers';
    public $newFileMode = 0666;
    public $name = 'amp';

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
        return 'amp';
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
