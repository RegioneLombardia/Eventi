<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open2\amos\ticket
 * @category   WebMeeting Module
 */

namespace open20\webmeeting;

use open20\webmeeting\providers\WebEx;

use open20\amos\core\interfaces\CmsModuleInterface;
use open20\amos\core\interfaces\SearchModuleInterface;
use open20\amos\core\module\AmosModule;
use open20\amos\core\module\ModuleInterface;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * WebMeetingModule
 * This is the main module for WebMeeting package
 */
class WebMeetingModule
    extends AmosModule 
    implements ModuleInterface, SearchModuleInterface, CmsModuleInterface
{
    /**
     * User by widgets to generate its code
     * @inheritdoc
     */
    const WEBMEETING_CODE_FOR_WIDGETS = 'WEBMEETING_MODULE';

    /**
     * roles
     * @inheritdoc
     */
    const ADMIN_WEBMEETING = 'ADMIN_WEBMEETING';
    const USER_WEBMEETING  = 'USER_WEBMEETING';

    /**
     * cans on WebMeeting
     * @inheritdoc
     */
    const WEBMEETING_MODEL_CREATE = 'WEBMEETINGMODEL_CREATE';
    const WEBMEETING_MODEL_READ   = 'WEBMEETINGMODEL_READ';
    const WEBMEETING_MODEL_UPDATE = 'WEBMEETINGMODEL_UPDATE';
    const WEBMEETING_MODEL_DELETE = 'WEBMEETINGMODEL_DELETE';

    /**
     * cans on Team
     * @inheritdoc
     */
    const WEBMEETING_TEAM_MODEL_CREATE  = 'WEBMEETINGTEAMMODEL_CREATE';
    const WEBMEETING_TEAM_MODEL_READ    = 'WEBMEETINGTEAMMODEL_READ';
    const WEBMEETING_TEAM_MODEL_UPDATE  = 'WEBMEETINGTEAMMODEL_UPDATE';
    const WEBMEETING_TEAM_MODEL_DELETE  = 'WEBMEETINGTEAMMODEL_DELETE';

    /**
     * cans on History
     * @inheritdoc
     */
    const WEBMEETING_HISTORY_MODEL_CREATE  = 'WEBMEETINGHISTORYMODEL_CREATE';
    const WEBMEETING_HISTORY_MODEL_READ    = 'WEBMEETINGHISTORYMODEL_READ';
    const WEBMEETING_HISTORY_MODEL_UPDATE  = 'WEBMEETINGHISTORYMODEL_UPDATE';
    const WEBMEETING_HISTORY_MODEL_DELETE  = 'WEBMEETINGHISTORYMODEL_DELETE';
    
    /**
     * cans on History
     * @inheritdoc
     */
    const WEBMEETING_ACTION_CREATE  = 'create';
    const WEBMEETING_ACTION_UPDATE  = 'update';

    /**
     * cans on Team
     * @inheritdoc
     */
    const WEBMEETING_DEFAULT_TIMEZONE  = 'Europe/Rome';

    /**
     * cans on Team
     * @inheritdoc
     */
    const WEBMEETING_SHORT_TEXT_255  = 255;

    /**
     * @inheritdoc
     */
    const WEBMEETING_MAXIMUM_INVITEES = 1000;

    /**
     * 
     */
    const WEBMEETING_AGENDA_TEXT_1000 = 1000;

    /**
     *  const WEBMEETING_USER_ONLINE = 1     -> connesso al meeting
     *  const WEBMEETING_USER_OFFLINE = 0    -> NON connesso al meeting
     */
    const WEBMEETING_USER_ONLINE = 1;
    const WEBMEETING_USER_OFFLINE = 0;
    
    /**
     *  Used for GridView pagination
     */
    const WEBMEETING_PAGE_SIZE = 10;
    
    /**
     * @var type 
     */
    public $name = 'webmeeting';

    /**
     * Default config directory
     * @var type 
     */
    public static $CONFIG_FOLDER = 'config';

    /**
     *  @var string $controllerNamespace the controller namespace
     */
    public $controllerNamespace = 'open20\webmeeting\controllers';

    /**
     * @var string $defaultView Set the default view for module
     */
    public $defaultListViews = ['icon', 'grid', 'list'];

    /**
     * @var type 
     */
    protected $formPartsView = '@vendor/open20/webmeeting/src/views/';

    /**
     *
     * @var type 
     */
    public $webExClientID;

    /**
     *
     * @var type 
     */
    public $webExClientSecret;

    /**
     *
     * @var type 
     */
    public $webExHostedUrl = null;

    /**
     *
     * @var type 
     */
    public $callback = null;
    
    /**
     * @var array providers configuration
     */
    public $providers = [];

    /**
     * hybridService
     */
    public static $hybridService = null;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::setAlias(
            '@open20/webmeeting/'.self::getModuleName().'/controllers', __DIR__.'/controllers/'
        );

        // Configuration: merge default module configurations loaded from config.php 
        // with module configurations set by the application
        $config = require(__DIR__.DIRECTORY_SEPARATOR.self::$CONFIG_FOLDER.DIRECTORY_SEPARATOR.'config.php');

        Yii::configure(
            $this,
            ArrayHelper::merge($config, $this)
        );
    }
    
    /**
     * 
     * @param type $serviceToCall
     * @return WebEx
     */
    public function getHybridService($serviceToCall = null)
    {
        $hybridService = new WebEx($this->providers['WebEx']);
        
        if (!empty($serviceToCall)) {
            $hybridService->serviceToCall = $serviceToCall;
        }
        
        return $hybridService;
    }

    /**
     * @inheritdoc
     */
    public static function getModuleName()
    {
        return 'webmeeting';
    }

    /**
     * 
     * @return type
     */
    public static function getTodayDate()
    {
        return date('Y-m-d H:m:s');
    }
    
    /**
     * 
     * @param type $message
     * @param type $category
     * @param type $params
     * @param type $language
     * @return type
     */
    public static function _t($message, $params = [], $language = null)
    {
        return parent::t('amoswebmeeting', $message, $params, $language);
    }

    /**
     * 
     * @param type $message
     * @param type $category
     * @param type $params
     * @param type $language
     * @return type
     */
    public static function _tHtml($message, $params = array(), $language = null)
    {
        return parent::tHtml('amoswebmeeting', $message, $params, $language);
    }

    /**
     * 
     * @return type
     */
    public static function getWebExAuthenticateLink()
    {
        return Yii::$app->urlManager->createUrl([
            self::getModuleName() . '/web-meeting/auth',
        ]);
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public static function getWebexWidgetPage($id)
    {
        return Yii::$app->urlManager->createUrl([
            self::getModuleName() . '/web-meeting/goto-webex',
            'id' => $id
        ]);
    }
    
    /**
     * 
     * @param type $section
     * @return type
     */
    public static function getMyIndexLink($section = null)
    {
        return Yii::$app->urlManager->createUrl([
            self::getModuleName() . '/web-meeting' . $section . '/index',
        ]);
    }

    /**
     * 
     * @param type $section
     * @return type
     */
    public static function getCreateLink($section = null)
    {
        return Yii::$app->urlManager->createUrl([
            self::getModuleName() . '/web-meeting' . $section . '/create'
        ]);
    }

    /**
     * 
     * @param type $id
     * @param type $section
     * @return type
     */
    public static function getUpdateLink($id, $section = null)
    {
        return Yii::$app->urlManager->createUrl([
            self::getModuleName() . '/web-meeting' . $section . '/update',
            'id' => $id
        ]);
    }

    /**
     * 
     * @param type $id
     * @param type $section
     * @return type
     */
    public static function getViewLink($id, $section = null)
    {
        return Yii::$app->urlManager->createUrl([
            self::getModuleName() . '/web-meeting' . $section . '/view',
            'id' => $id
        ]);
    }

    /**
     * 
     * @param type $id
     * @param type $section
     * @return type
     */
    public static function getDeleteLink($id, $section = null)
    {
        return Yii::$app->urlManager->createUrl([
            self::getModuleName() . '/web-meeting' . $section . '/delete',
            'id' => $id
        ]);
    }

    /**
     * 
     * @return type
     */
    public static function getSetOnlineLink()
    {
        return Yii::$app->urlManager->createUrl([
            self::getModuleName() . '/web-meeting/set-online',
        ]);
    }

    /**
     * 
     * @return type
     */
    public static function getOnlineUsersLink()
    {
        return Yii::$app->urlManager->createUrl([
            self::getModuleName() . '/web-meeting/get-online-users',
        ]);
    }

    /**
     * 
     * @param type $id
     * @param type $section
     * @return type
     */
    public static function getConnectToWebEx($id, $section = null)
    {
        return Yii::$app->urlManager->createUrl([
            self::getModuleName() . '/web-meeting' . $section . '/connect',
            'id' => $id
        ]);
    }

    /**
     * 
     * @param type $id
     * @param type $section
     * @return type
     */
    public static function getUnlinkFromWebEx($id, $section = null)
    {
        return Yii::$app->urlManager->createUrl([
            self::getModuleName() . '/web-meeting' . $section . '/unlink',
            'id' => $id
        ]);
    }

    /**
     * 
     * @param type $id
     * @param type $section
     * @return type
     */
    public static function getMeetingInviteesToWebEx($id, $section = null)
    {
        return Yii::$app->urlManager->createUrl([
            self::getModuleName() . '/web-meeting-invitee',
            'webmeeting_id' => $id
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function getWidgetGraphics()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getWidgetIcons()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultModels()
    {
        return [
            'WebMeetingModel' => __NAMESPACE__ . '\\' . 'models\WebMeetingModel',
            'WebMeetingTokenModel' => __NAMESPACE__ . '\\' . 'models\WebMeetingTokenModel',
            'WebMeetingModelSearch' => __NAMESPACE__.'\\'.'models\search\WebMeetingModelSearch',
            'WebMeetingTeamModel' => __NAMESPACE__ . '\\' . 'models\WebMeetingTeamModel',
            'WebMeetingTeamModelSearch' => __NAMESPACE__.'\\'.'models\search\WebMeetingTeamModelSearch',
            'WebMeetingInviteeModel' => __NAMESPACE__ . '\\' . 'models\WebMeetingInviteeModel',
            'WebMeetingInviteeModelSearch' => __NAMESPACE__.'\\'.'models\search\WebMeetingInviteeModelSearch',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getModelSearchClassName()
    {
        return models\search\WebMeetingModelSearch::class;
    }

    /**
     * @inheritdoc
     */
    public static function getModelClassName()
    {
        return models\WebMeetingModel::class;
    }

    /**
     * @inheritdoc
     */
    public static function getModuleIconName()
    {
        return 'feed';
    }

}