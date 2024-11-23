<?php

use open20\amos\events\models\Event;
use yii\db\Migration;

/**
 * Class m190524_173430_alter_table_event
 */
class m220718_124400_add_column_description_participants extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event_landing', 'description_protagonists', $this->text()->comment('Description protagonists')->after('title_protagonists'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event_landing', 'description_protagonists');


    }

}
