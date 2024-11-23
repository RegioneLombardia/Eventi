<?php

use open20\amos\events\models\Event;
use yii\db\Migration;


class m221021_175400_add_column_time_cron_executed extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event', 'time_cron_executed', $this->integer(1)->defaultValue(0)->after('is_father')->comment('TimeCron executed'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event', 'time_cron_executed');

    }

}