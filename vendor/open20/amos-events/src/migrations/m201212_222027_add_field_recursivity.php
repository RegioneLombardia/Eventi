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
class m201212_222027_add_field_recursivity extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('event','is_time_period',$this->integer(1)->defaultValue(0)->comment('Is time period')->after('event_id'));
        $this->addColumn('event','original_event_id',$this->integer()->defaultValue(null)->comment('Original event')->after('event_id'));

        $this->addForeignKey('fk_event_original_event_id1','event', 'original_event_id', 'event', 'id');
    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');
        $this->dropForeignKey('fk_event_original_event_id1','event');

        $this->dropColumn('event','is_time_period');
        $this->dropColumn('event','original_event_id');
        $this->execute('SET FOREIGN_KEY_CHECKS=1;');


    }
}
