<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

use yii\db\Schema;

/**
 * Default migration for the relations of the Application Project
 */
class m180713_164018_widget_monitor extends \open20\amos\core\migration\AmosMigrationPermissions {

    protected function setAuthorizations()
    {
        $this->authorizations = [
            [
                'name' => \backend\modules\tickets\widgets\graphics\WidgetGraphicCustomMonitor::className(),
                'type' => \yii\rbac\Permission::TYPE_PERMISSION,
                'description' => 'Permesso view monitor',
                'ruleName' => null,
                //'parent' => ['ADMIN', 'BASIC_USER']
            ],
        ];
    }

}
