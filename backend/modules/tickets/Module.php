<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

namespace backend\modules\tickets;

use open20\amos\core\module\AmosModule;

/**
 * registry module definition class
 */
class Module extends AmosModule
{

    const site_key_param = 'google_recaptcha_site_key';
    const secret_param = 'google_recaptcha_secret';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\tickets\controllers';
    public $newFileMode = 0666;
    public $name = 'tickets';

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
        return 'tickets';
    }

    public function getWidgetGraphics()
    {
        return NULL;
    }

    public function getWidgetIcons()
    {
        return [
            \backend\modules\tickets\widgets\icons\WidgetIconAssistenza::className(),

        ];
    }
}
