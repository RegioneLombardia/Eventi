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

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use open20\amos\core\helpers\Html;

/**
 * WebMeetingTeamController
 */
class WebMeetingTeamController extends WebMeetingBaseController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->webMeetingModule = Yii::$app->getModule(WebMeetingModule::getModuleName());
        $this->setModelObj($this->webMeetingModule::instance()->createModel('WebMeetingTeamModel'));
        $this->setModelSearch($this->webMeetingModule::instance()->createModel('WebMeetingTeamModelSearch'));
        
        $this->model = $this->getModelObj();
        
        $this->view->params = [
            'subTitleSection' => $subTitleSection = Html::tag('p', WebMeetingModule::_t('')),
            'isGuest'      => Yii::$app->user->isGuest,
            'modelLabel'   => WebMeetingModule::getModuleName(),
            'titleSection' => WebMeetingModule::_t('#webmeeting_team'),
            'urlLinkAll'   => WebMeetingModule::getMyIndexLink(),
            'labelLinkAll' => WebMeetingModule::_t('#webmeeting'),
            'titleLinkAll' => WebMeetingModule::_t('#webmeeting_team_list'),
            'labelCreate'  => WebMeetingModule::_t('#webmeeting_team_new'),
            'titleCreate'  => WebMeetingModule::_t('#webmeeting_team_create'),
            'labelManage'  => WebMeetingModule::_t('#manager'),
            'titleManage'  => WebMeetingModule::_t('#meeting_manager'),
            'urlCreate'    => WebMeetingModule::getCreateLink('-team'),
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
            parent::behaviors(),
            [
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
