<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m201204_160513_cms_ultimi_documenti_disable extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('amos_widgets', ['status' => 0], ['classname' => 'open20\amos\documenti\widgets\graphics\WidgetGraphicsCmsUltimiDocumenti']);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->update('amos_widgets', ['status' => 1], ['classname' => 'open20\amos\documenti\widgets\graphics\WidgetGraphicsCmsUltimiDocumenti']);
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

    }
}
