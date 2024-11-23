<?php

use open20\amos\events\models\Event;
use yii\db\Migration;


class m220915_152000_add_column_enable_rating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event_landing', 'enable_rating', $this->boolean()->comment('Enable Rating')->after('social_reg'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event_landing', 'social_reg');
    }

}
