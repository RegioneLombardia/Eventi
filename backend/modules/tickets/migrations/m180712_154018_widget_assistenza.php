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
class m180712_154018_widget_assistenza extends \open20\amos\core\migration\AmosMigrationPermissions {

    protected function setAuthorizations()
    {
        $this->authorizations = [
            [
                'name' => \backend\modules\tickets\widgets\icons\WidgetIconAssistenza::className(),
                'type' => \yii\rbac\Permission::TYPE_PERMISSION,
                'description' => 'Permesso invio email assistenza',
                'ruleName' => null,
                'parent' => ['ADMIN', 'BASIC_USER']
            ],
        ];
    }

}
