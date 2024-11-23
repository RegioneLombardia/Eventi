<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200722_092613_hide_widget_moodle_ranking extends Migration
{



    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->update('amos_widgets', ['status' => false],[
            'classname' => 'open20\amos\moodle\widgets\icons\WidgetIconMoodleRanking',
            "module" => "moodle" ,
            'sub_dashboard' => 1,
        ]);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('amos_widgets', ['status' => true],[
            'classname' => 'open20\amos\moodle\widgets\icons\WidgetIconMoodleRanking',
            "module" => "moodle" ,
            'sub_dashboard' => 1,
        ]);

    }
}
