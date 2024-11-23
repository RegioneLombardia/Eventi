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
class m210525_160627_add_field_internal_list_query_sql_segmented extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('event_internal_list','active_query_complete', $this->text()->comment('Active query complete')->after('query_sql'));

    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');
        $this->dropColumn('event_internal_list','active_query_complete');

        $this->execute('SET FOREIGN_KEY_CHECKS=1;');


    }
}
