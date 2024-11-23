<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Migrations
 */

use open20\amos\core\migration\AmosMigration;

use open20\webmeeting\widgets\icons\WidgetIconWebMeetingMeeting;
use open20\webmeeting\WebMeetingModule;

use yii\db\Migration;
use yii\rbac\Permission;

/**
 * @inheritdoc
 */
class m201127_104609_role_webmeeting_webmeeting_widgets extends AmosMigration
{
    /**
     * @var type 
     */
    protected $authorizations;
    
    /**
     * @inheritdoc
     */
    public function init() {
        $this->authorizations = [
            [
                'name' => WidgetIconWebMeetingMeeting::class,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Perm on WidgetIconWebMeetingMeeting widget',
                'ruleName' => null,
                'parent' => [
                    'ADMIN',
                    WebMeetingModule::ADMIN_WEBMEETING,
                    WebMeetingModule::USER_WEBMEETING
                ]
            ],
        ];
    }

    /**
     * Insert widgets into amos widgets table
     * 
     * @inheritdoc
     */
    public function safeUp() {
        return $this->addAuthorizations();
    }
    
    /**
     * Remove widgets
     * 
     * @inheritdoc
     */
    public function safeDown() {
        return $this->removeAuthorizations();
    }

}
