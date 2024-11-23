<?php

use open20\amos\events\models\Event;
use yii\db\Migration;


class m220725_154300_add_columns_publish_on_poi_and_poi_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event', 'publish_on_poi', $this->integer(1)->notNull()->defaultValue(0)->after('publish_on_prl')->comment('Publish on Open Innovation'));
        $this->addColumn('event', 'poi_category', $this->integer()->null()->defaultValue(null)->after('publish_on_poi')->comment('Publish on Open Innovation'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event', 'publish_on_poi');
        $this->dropColumn('event', 'poi_category');


    }

}
