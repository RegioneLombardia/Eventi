<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    amos\sitemanagement\migrations
 * @category   CategoryName
 */

use yii\db\Migration;

class m220823_105500_add_column_instagram_video_slider_id extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('event_landing', 'instagram_video_slider_id', $this->integer()->null()->defaultValue(null)->comment('Slider Instagram videos')->after('video_slider_id'));
        $this->addColumn('event_landing', 'title_instagram_video', $this->string()->null()->defaultValue(null)->after('title_video'));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->dropColumn('event_landing', 'instagram_video_slider_id');
        $this->dropColumn('event_landing', 'title_video');

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

        return true;
    }
}
