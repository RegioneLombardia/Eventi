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
class m211209_101127_add_fields_event_landing_titles extends Migration
{




    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('event_landing', 'title_protagonists', $this->string()->after('contact_info_organizator'));
        $this->addColumn('event_landing', 'title_pics', $this->string()->after('contact_info_organizator'));
        $this->addColumn('event_landing', 'title_video', $this->string()->after('contact_info_organizator'));
        $this->addColumn('event_landing', 'title_info', $this->string()->after('contact_info_organizator'));
        $this->addColumn('event_landing', 'title_documents', $this->string()->after('contact_info_organizator'));
        $this->addColumn('event_landing', 'title_request_info', $this->string()->after('contact_info_organizator'));
    }



    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('event_landing', 'title_protagonists');
        $this->dropColumn('event_landing', 'title_pics' );
        $this->dropColumn('event_landing', 'title_video');
        $this->dropColumn('event_landing', 'title_info');
        $this->dropColumn('event_landing', 'title_documents');
        $this->dropColumn('event_landing', 'title_request_info');

    }
}
