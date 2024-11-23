<?php

use open20\amos\events\models\Event;
use yii\db\Migration;


class m220922_154200_alter_columns_rating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('event_landing', 'rating_description', $this->text()->comment('Rating Section Description')->after('rating_title'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('event_landing', 'rating_description', $this->string()->comment('Rating Section Description')->after('rating_title'));
    }

}
