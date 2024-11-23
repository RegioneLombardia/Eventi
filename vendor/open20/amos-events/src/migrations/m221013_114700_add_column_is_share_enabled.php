<?php

use open20\amos\events\models\Event;
use yii\db\Migration;


class m221013_114700_add_column_is_share_enabled extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event_landing', 'is_social_share_enabled', $this->integer(1)->defaultValue(1)->after('description_protagonists')->comment('Share enabled'));
        $this->addColumn('event_landing', 'share_title', $this->string()->after('rating_title')->comment('Share enabled'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event', 'is_share_enabled');
        $this->dropColumn('event_landing', 'share_title');

    }

}