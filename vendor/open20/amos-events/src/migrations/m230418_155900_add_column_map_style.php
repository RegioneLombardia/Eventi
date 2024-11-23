<?php

use open20\amos\events\models\Event;
use yii\db\Migration;


class m230418_155900_add_column_map_style extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event_landing', 'map_style', $this->string()->defaultValue(null)->after('description_protagonists')->comment('Map style'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event_landing', 'map_style');

    }

}