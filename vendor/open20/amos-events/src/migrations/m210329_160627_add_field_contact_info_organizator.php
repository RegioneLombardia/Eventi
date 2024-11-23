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
class m210329_160627_add_field_contact_info_organizator extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('event_landing','contact_info_organizator',$this->text()->comment('Contact info Organizator')->after('schedule'));

    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');
        $this->dropColumn('event_landing','contact_info_organizator');

        $this->execute('SET FOREIGN_KEY_CHECKS=1;');


    }
}
