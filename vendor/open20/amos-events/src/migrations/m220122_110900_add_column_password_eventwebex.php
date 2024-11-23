<?php

use open20\amos\events\models\Event;
use yii\db\Migration;

/**
 * Class m190524_173430_alter_table_event
 */
class m220122_110900_add_column_password_eventwebex extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('event_webex_accounts', 'webex_password', $this->string()->comment('Webex password')->after('email_webex'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event_webex_accounts', 'webex_password');
    }

}
