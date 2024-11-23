<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200618_125413_remove_widgets_from_community extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->delete('amos_widgets',  [
            'classname' => 'open20\amos\moodle\widgets\icons\WidgetIconMoodle',
            'module' => 'community',
            'sub_dashboard' => true
        ]);

        $this->update('amos_widgets',  [
            'status' => 0,
        ],[
            'classname' => 'open20\amos\chat\widgets\graphics\WidgetGraphicChatAssistance',
            'module' => 'community',
        ]);

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');



    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
;
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
