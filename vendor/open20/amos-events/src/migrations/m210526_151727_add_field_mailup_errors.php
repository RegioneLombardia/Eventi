<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    cruscotto-lavoro\platform\common\console\migrations
 * @category   CategoryName
 */

use open20\amos\core\migration\libs\common\MigrationCommon;
use open20\amos\tag\models\Tag;
use yii\db\Migration;

/**
 * Class m180904_084129_add_cl_roles_tags
 */
class m210526_151727_add_field_mailup_errors extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('event_internal_list','mailup_errors', $this->integer()->defaultValue(0)->comment('N. Mailup errors')->after('status'));
        $this->addColumn('event_communication','mailup_errors', $this->integer()->defaultValue(0)->comment('N. Mailup errors')->after('status'));
        $this->addColumn('event_invitation_sent','mailup_import_id', $this->string()->comment('Import id')->after('send_at'));
        $this->addColumn('event_communication_sent','mailup_import_id', $this->string()->comment('Import id')->after('sent_at'));

    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');
        $this->dropColumn('event_internal_list','mailup_errors');
        $this->dropColumn('event_communication','mailup_errors');
        $this->dropColumn('event_invitation_sent','mailup_import_id');
        $this->dropColumn('event_communication_sent','mailup_import_id');

        $this->execute('SET FOREIGN_KEY_CHECKS=1;');


    }
}
