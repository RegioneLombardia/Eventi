<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeetingControllers
 */
 
namespace open20\webmeeting\controllers;

use open20\webmeeting\WebMeetingModule;
use open20\webmeeting\models\WebMeetingModel;
use open20\webmeeting\models\WebMeetingInviteeModel;
use open20\webmeeting\utility\WebMeetingUtility;
use open20\webmeeting\providers\api\WebExMeetingInvitees;

use open20\amos\core\controllers\CrudController;
use open20\amos\core\user\User;
use open20\amos\core\helpers\Html;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Class WebMeetingHistoryController 
 */
class WebMeetingHistoryController extends WebMeetingController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->view->params = [
            'subTitleSection' => Html::tag('p', WebMeetingModule::_t('')),
            'isGuest'      => Yii::$app->user->isGuest,
            'modelLabel'   => WebMeetingModule::getModuleName(),
            'titleSection' => WebMeetingModule::_t('#webmeeting_webmeeting_history'),
            'urlLinkAll'   => WebMeetingModule::getMyIndexLink(),
            'labelLinkAll' => WebMeetingModule::_t('#webmeeting'),
            'titleLinkAll' => WebMeetingModule::_t('#webmeeting_list'),
            'labelCreate'  => WebMeetingModule::_t('#webmeeting_new'),
            'titleCreate'  => WebMeetingModule::_t('#webmeeting_create'),
            'labelManage'  => WebMeetingModule::_t('#manager'),
            'titleManage'  => WebMeetingModule::_t('#meeting_manager'),
            'urlCreate'    => WebMeetingModule::getCreateLink(),
            'urlManage'    => WebMeetingModule::_t('#'),
        ];
    }
    
    /**
     * @param type $layout
     * @return type
     */
    public function actionIndex($layout = null)
    {
        Url::remember();
        $this->setUpLayout('list');

        $queryParams = ArrayHelper::merge(
            [
                'end' => WebMeetingModule::getTodayDate(),
                'context' => is_array($this->scope) ? array_shift(array_keys($this->scope)) : null,
                'context_id' => is_array($this->scope) ? array_shift(array_values($this->scope)) : null
            ],
            Yii::$app->request->getQueryParams()
        );
        
        $this->setDataProvider($this->modelSearch->searchPassedWebMeeting($queryParams));
        
        $this->view->params['dataProvider'] = $this->getDataProvider();
        $this->view->params['todayDateTime'] = WebMeetingModule::getTodayDate();
        $this->view->params['canUpdate'] = Yii::$app->getUser()->can(WebMeetingModule::ADMIN_WEBMEETING);
        $this->view->params['loginUser'] = Yii::$app->getUser();

        return \open20\amos\core\controllers\CrudController::actionIndex($layout);
    }
    
    /**
     * @return type
     */
    public function actionCreate()
    {
        return $this->redirect(WebMeetingModule::getCreateLink());
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
                            'index',
                            'view'
                        ],
                        'roles' => [
                            'ADMIN',
                            'BASIC_USER',
                            WebMeetingModule::ADMIN_WEBMEETING,
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
