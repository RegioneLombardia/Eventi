<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Migrations
 */

use open20\webmeeting\widgets\icons\WidgetIconWebMeetingDashboard;

use yii\db\Migration;

/**
 * Class m171219_111336_add_community_field_hits
 */
class m201228_183036_add_webmeetig_widget_on_community_subdashboard extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert(
            'amos_widgets', [
                'classname' => WidgetIconWebMeetingDashboard::class,
                'type' => 'ICON',
                'module' => 'community',
                'status' => 1,
                'dashboard_visible' => 0,
                'sub_dashboard' => 1,
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete(
            'amos_widgets', [
                'classname' => WidgetIconWebMeetingDashboard::class,
                'type' => 'ICON',
                'module' => 'community',
                'status' => 1,
                'dashboard_visible' => 0,
                'sub_dashboard' => 1,
            ]
        );
    }
}
