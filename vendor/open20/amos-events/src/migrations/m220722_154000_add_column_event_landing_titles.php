<?php

use open20\amos\events\models\Event;
use yii\db\Migration;

/**
 * Class m190524_173430_alter_table_event
 */
class m220722_154000_add_column_event_landing_titles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event_landing', 'title_schedule', $this->string()->comment('Title schedule')->after('title_protagonists'));
        $this->addColumn('event_landing', 'title_presentation', $this->string()->comment('Title presentation')->after('title_protagonists'));
        $this->addColumn('event_landing', 'title_maps', $this->string()->comment('Title maps')->after('title_protagonists'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event_landing', 'title_schedule');
        $this->dropColumn('event_landing', 'title_presentation');
        $this->dropColumn('event_landing', 'title_maps');
    }

}
