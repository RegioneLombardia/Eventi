<?php

use open20\amos\events\models\Event;
use yii\db\Migration;


class m220916_170700_add_columns_rating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event_landing', 'rating_title', $this->string()->comment('Rating Section Title')->after('enable_rating'));
        $this->addColumn('event_landing', 'rating_description', $this->string()->comment('Rating Section Description')->after('rating_title'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event_landing', 'rating_title');
        $this->dropColumn('event_landing', 'rating_description');
    }

}
