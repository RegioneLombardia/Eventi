<?php

use open20\amos\events\models\Event;
use yii\db\Migration;

/**
 * Class m190524_173430_alter_table_event
 */
class m220616_173600_add_column_events_n_companions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event', 'enable_companions', $this->integer(1)->defaultValue(0)->comment('Enable companions')->after('numero_max_accompagnatori'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event', 'enable_companions');


    }

}
