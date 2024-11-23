<?php

use open20\amos\events\models\Event;
use yii\db\Migration;

/**
 * Class m190524_173430_alter_table_event
 */
class m220502_152000_add_widget_data_analyzer  extends \open20\amos\core\migration\AmosMigrationWidgets
{
    const MODULE_NAME = 'events';

    /**
     * @inheritdoc
     */
    protected function initWidgetsConfs()
    {
        $this->widgets = [
            [
                'classname' => \open20\amos\events\widgets\icons\WidgetIconEventDataAnalyzer::className(),
                'type' => \open20\amos\dashboard\models\AmosWidgets::TYPE_ICON ,
                'module' => self::MODULE_NAME,
                'status' => \open20\amos\dashboard\models\AmosWidgets::STATUS_ENABLED,
                'dashboard_visible' => 1,
            ],

        ];
    }
}