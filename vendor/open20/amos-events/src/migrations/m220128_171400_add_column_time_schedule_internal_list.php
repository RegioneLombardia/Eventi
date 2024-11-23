<?php

use open20\amos\events\models\Event;
use yii\db\Migration;

/**
 * Class m190524_173430_alter_table_event
 */
class m220128_171400_add_column_time_schedule_internal_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event_internal_list', 'time_schedule_date', $this->dateTime()->comment('Scheduled date')->after('status'));
        $this->addColumn('event_internal_list', 'time_schedule_type', $this->integer()->comment('Time schedule type')->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event_internal_list', 'time_schedule_date');
        $this->dropColumn('event_internal_list', 'time_schedule_type');

    }

}
