<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

class m180713_103631_create_widget_monitor  extends \open20\amos\core\migration\AmosMigrationWidgets
{
    const MODULE_NAME = 'tickets';

    /**
     * @inheritdoc
     */
    protected function initWidgetsConfs()
    {
        $this->widgets = [
            [
                'classname' => \backend\modules\tickets\widgets\graphics\WidgetGraphicCustomMonitor::className(),
                'type' => \open20\amos\dashboard\models\AmosWidgets::TYPE_GRAPHIC,
                'module' => self::MODULE_NAME,                
                'status' => \open20\amos\dashboard\models\AmosWidgets::STATUS_ENABLED,
                'default_order' => 1,               
                'dashboard_visible' => 1,
            ]           
        ];
    }
}
