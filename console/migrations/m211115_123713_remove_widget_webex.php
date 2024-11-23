<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m211115_123713_remove_widget_webex extends Migration
{



    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        ;

        $this->update('amos_widgets',['status' => 0], ['classname' => 'open20\webmeeting\widgets\icons\WidgetIconWebMeetingDashboard'] );

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('amos_widgets',['status' => 1], ['classname' => 'open20\webmeeting\widgets\icons\WidgetIconWebMeetingDashboard'] );

    }
}
