<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200807_162013_hide_dashboard_widgets extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('amos_widgets', ['status' => false], ['classname' => 'open20\amos\admin\widgets\graphics\WidgetGraphicMyProfile']);
        $this->update('amos_widgets', ['status' => false], ['classname' => 'open20\amos\events\widgets\graphics\WidgetGraphicsEvents']);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->update('amos_widgets', ['status' => true], ['classname' => 'open20\amos\events\widgets\graphics\WidgetGraphicsEvents']);
        $this->update('amos_widgets', ['status' => true], ['classname' => 'open20\amos\admin\widgets\graphics\WidgetGraphicMyProfile']);
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

    }
}
