<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m211111_150713_add_column_specific_users extends Migration
{
    const TABLE_EVENT = "event_invitation_specific_users";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_EVENT, 'webmeeting_invitee_id', $this->string()->after('send_at'));

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropColumn(self::TABLE_EVENT, 'webmeeting_invitee_id');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
