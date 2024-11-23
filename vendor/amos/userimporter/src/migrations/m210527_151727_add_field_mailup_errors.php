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
class m210527_151727_add_field_mailup_errors extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('user_import_task','mailup_errors', $this->integer()->defaultValue(0)->comment('N. Mailup errors')->after('status'));
        $this->addColumn('user_import_task_user','mailup_import_id', $this->integer()->defaultValue(null)->comment('Mailup import id')->after('to_send'));

    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');
        $this->dropColumn('user_import_task','mailup_errors');
        $this->dropColumn('user_import_task_user','mailup_import_id');
        $this->execute('SET FOREIGN_KEY_CHECKS=1;');


    }
}
