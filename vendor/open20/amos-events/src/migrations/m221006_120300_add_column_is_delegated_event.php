<?php

use open20\amos\events\models\Event;
use yii\db\Migration;


class m221006_120300_add_column_is_delegated_event extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event', 'is_delegated_event', $this->boolean()->notNull()->defaultValue(0)->after('event_group_referent_id')->comment('Delegated Event'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event', 'is_delegated_event');
    }

}
