<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    amos\sitemanagement\migrations
 * @category   CategoryName
 */

use yii\db\Migration;

class m220901_144100_add_fields_event_communication extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('event_communication', 'time_schedule_type', $this->integer()->null()->defaultValue(null)->comment('Time schedule type')->after('status'));
        $this->addColumn('event_communication', 'time_schedule_date', $this->dateTime()->null()->defaultValue(null)->comment('Time schedule date')->after('status'));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->dropColumn('event_communication', 'time_schedule_type');
        $this->dropColumn('event_communication', 'time_schedule_date');


        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

        return true;
    }
}
