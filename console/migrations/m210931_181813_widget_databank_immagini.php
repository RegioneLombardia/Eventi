<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m210931_181813_widget_databank_immagini extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('amos_widgets',['child_of' => null, 'dashboard_visible' => 1],['classname' => 'open20\amos\attachments\widgets\icons\WidgetIconGalleryDashboard']);
    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->update('amos_widgets',['child_of' => 'open20\amos\dashboard\widgets\icons\WidgetIconManagement', 'dashboard_visible' => 1],['classname' => 'open20\amos\attachments\widgets\icons\WidgetIconGalleryDashboard']);
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

    }
}
