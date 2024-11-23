<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\translation\migrations
 * @category   CategoryName
 */

use yii\db\Migration;

/**
 * Class m181108_161715_regroup_widgets_translation
 */
class m221125_124815_alter_column_translation_conf extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('translation_conf', 'fields', $this->text()->defaultValue(null));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->alterColumn('translation_conf', 'fields', $this->string()->defaultValue(null));
    }
}
