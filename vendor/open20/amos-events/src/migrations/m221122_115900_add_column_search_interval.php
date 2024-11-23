<?php

use open20\amos\events\models\Event;
use yii\db\Migration;


class m221122_115900_add_column_search_interval extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event_communication', 'search_inverval', $this->string()->defaultValue(null)->after('n_sent')->comment('Search interval'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event_communication', 'search_inverval');

    }

}