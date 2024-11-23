<?php

use open20\amos\events\models\Event;
use yii\db\Migration;


class m220927_103300_add_columns_portale_giovani_integration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event', 'publish_on_portale_giovani', $this->boolean()->notNull()->defaultValue(0)->after('poi_category')->comment('Publish on Portale Giovani'));
        $this->addColumn('event', 'portale_giovani_category', $this->integer()->null()->defaultValue(null)->after('publish_on_portale_giovani')->comment('Portale Giovani Category'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event', 'publish_on_portale_giovani');
        $this->dropColumn('event', 'portale_giovani_category');


    }

}
