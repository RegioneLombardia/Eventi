<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeetingControllers
 */
 
namespace open20\webmeeting\controllers\base;

use open20\webmeeting\WebMeetingModule;

use open20\amos\core\helpers\Html;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * WebMeetingInviteeController
 */
class WebMeetingInviteeController extends WebMeetingBaseController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->webMeetingModule = Yii::$app->getModule(WebMeetingModule::getModuleName());
        $this->setModelObj($this->webMeetingModule::instance()->createModel('WebMeetingInviteeModel'));
        $this->setModelSearch($this->webMeetingModule::instance()->createModel('WebMeetingInviteeModelSearch'));
        
        $this->model = $this->getModelObj();
        
        $this->view->params = [
            'subTitleSection' => Html::tag('p', WebMeetingModule::_t('')),
            'isGuest'      => Yii::$app->user->isGuest,
            'modelLabel'   => WebMeetingModule::getModuleName(),
            'titleSection' => WebMeetingModule::_t('#webmeeting_invitee'),
            'urlLinkAll'   => WebMeetingModule::getMyIndexLink('-invitee'),
            'labelLinkAll' => WebMeetingModule::_t('#invitee'),
            'titleLinkAll' => WebMeetingModule::_t('#webmeeting_invitee_list'),
            'labelCreate'  => WebMeetingModule::_t('#webmeeting_new_invitee'),
            'titleCreate'  => WebMeetingModule::_t('#webmeeting_create_invitee'),
            'labelManage'  => WebMeetingModule::_t('#manager'),
            'titleManage'  => WebMeetingModule::_t('#meeting_manager'),
            'urlCreate'    => WebMeetingModule::getCreateLink('invitee'),
            'urlManage'    => WebMeetingModule::_t('#'),
        ];

        parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(
            parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'auth',
                        ],
                        'roles' => [
                            'ADMIN',
                            'BASIC_USER',
                            WebMeetingModule::ADMIN_WEBMEETING,
                            WebMeetingModule::USER_WEBMEETING
                        ]
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post', 'get']
                ]
            ]
        ]);

        return $behaviors;
    }

}
