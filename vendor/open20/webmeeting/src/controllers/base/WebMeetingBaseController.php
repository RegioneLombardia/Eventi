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

use open20\webmeeting\assets\WebMeetingAssets;
use open20\webmeeting\models\WebMeetingModel;
use open20\webmeeting\WebMeetingModule;

use open20\amos\core\controllers\CrudController;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\utilities\Email;
use open20\amos\core\views\DataProviderView;
use open20\amos\dashboard\controllers\TabDashboardControllerTrait;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use Hybridauth\Data;

/**
 * Class WebMeetingBaseController 
 * This class is used as father controller for all WebMeetingFoo derived classes.
 */
class WebMeetingBaseController extends CrudController
{
    use TabDashboardControllerTrait;

    /**
     *
     * @var type 
     */
    protected $currentAction;
    
    /**
     *
     * @var type 
     */
    protected $currentLayout;
    
    /**
     * @var model WebMeeting module ref
     */
    public $webMeetingModule;
    
    /**
     * @var model WebMeeting module ref
     */
    public $user_id;
    
    /**
     * Just to check the scope
     */
    public $moduleCwh;

    /**
     *  From main dashboard or from community?
     */
    public $scope;

    /**
     * 
     */
    protected $refreshToken;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initDashboardTrait();
        
        $this->webMeetingModule = Yii::$app->getModule(WebMeetingModule::getModuleName());
        $this->user_id = Yii::$app->user->id;
        
        $this->moduleCwh  = Yii::$app->getModule('cwh');
        $this->scope = null;
        if (!empty($this->moduleCwh)) {
            $this->scope = $this->moduleCwh->getCwhScope();
        }

        $viewList = [
            'name' => 'list',
            'label' => AmosIcons::show('view-list')
                . Html::tag('p', $this->webMeetingModule::_t('#card')),
            'url' => '?currentView=list',
        ];

        $viewGrid = [
            'name' => 'grid',
            'label' => AmosIcons::show('view-list-alt')
                . Html::tag('p', $this->webMeetingModule::_t('#table')),
            'url' => '?currentView=grid',
        ];
        
        $iconView = [
            'name' => 'icon',
            'label' => AmosIcons::show('grid').Html::tag('p', $this->webMeetingModule::_t('#icon')),
            'url' => '?currentView=icon'
        ];
        
        $defaultViews = [
            'icon' => $iconView,
            'list' => $viewList,
            'grid' => $viewGrid,
        ];

        $availableViews = [];
        foreach ($this->webMeetingModule->defaultListViews as $view) {
            if (isset($defaultViews[$view])) {
                $availableViews[$view] = $defaultViews[$view];
            }
        }

        $this->setAvailableViews($availableViews);

        parent::init();

        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();

        $this->setUpLayout();
    }
    
    /**
     * WebEx authorization request
     */
    public function actionAuth()
    {
        $tokenModel = $this->webMeetingModule::instance()->createModel('WebMeetingTokenModel');
        $refreshToken = $tokenModel->find()->one();
        
        if (empty($refreshToken)) {
            $isConnected = $this->webMeetingModule->getHybridService()->authenticate();

            $tokens = $this->apiToCall->getAccessToken();
            $tokenModel = $this->webMeetingModule::instance()->createModel('WebMeetingTokenModel');
            $refreshToken = $tokenModel->find()
                ->andWhere(['refresh_token' => $tokens['refresh_token']])
                ->one();
       
            if (empty($refreshToken)) {
                $tokenModel->user_id = 1;   // Always ADMIN user
                $tokenModel->access_token = $tokens['access_token'];
                $tokenModel->refresh_token = $tokens['refresh_token'];
                $tokenModel->expires_in = $tokens['expires_in'];
                $tokenModel->expires_at = $tokens['expires_at'];
                $tokenModel->save();
            }
        }
        
        return $this->redirect($this->webMeetingModule::getMyIndexLink());
    }
    
    /**
     * @inheritdoc
     */
    public function actionIndex($layout = null)
    {
        Url::remember();
        $this->setUpLayout('list');

        $this->setDataProvider($this->modelSearch->search(Yii::$app->request->getQueryParams()));
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();

        return parent::actionIndex($layout);
    }
    
    /**
     * Displays a single WebMeeting model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    /**
     * 
     * @return type
     */
    public function isPostAndValidate()
    {
        return $this->model->load(Yii::$app->request->post()) && $this->model->validate();
    }
    
    /**
     * @return type
     */
    public function actionCreate() {
        Url::remember();
        $this->setUpLayout('form');

        if ($this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            if ($this->model->save()) {                
                Yii::$app->getSession()->addFlash('success', WebMeetingModule::_t('#item_created'));
                return $this->redirect(['update', 'id' => $this->model->id]);
            } else {
                Yii::$app->getSession()->addFlash('danger', WebMeetingModule::_t('#item_not_created'));
            }
        }

        return $this->render($this->webMeetingModule::WEBMEETING_ACTION_CREATE, [
            'model' => $this->model,
        ]);
    }

    /**
     * Updates an existing WebMeeting model
     * If update is successful, the browser will be redirected to the 'view' page
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        Url::remember();
        $this->setUpLayout('form');

        $this->model = $this->findModel($id);

        if ($this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            if ($this->model->save()) {
                Yii::$app->getSession()->addFlash('success', WebMeetingModule::_t('#item_updated'));
                return $this->redirect(['update', 'id' => $this->model->id]);
            } else {
                Yii::$app->getSession()->addFlash('danger', WebMeetingModule::_t('#item_not_updated'));
            }
        }

        return $this->render('update', [
            'model' => $this->model,
        ]);
    }

    /**
     * Deletes an existing WebMeeting model
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->model = $this->findModel($id);
        if ($this->model) {
            $this->model->delete();
            if (!$this->model->hasErrors()) {
                Yii::$app->getSession()->addFlash('success', WebMeetingModule::_t('#element_deleted'));
            } else {
                Yii::$app->getSession()->addFlash('danger', WebMeetingModule::_t('#you_are_not_authorized_to_delete'));
            }
        } else {
            Yii::$app->getSession()->addFlash('danger', WebMeetingModule::_t('#not_found'));
        }

        return $this->redirect(['index']);
    }

    /**
     *
     * @return array
     */
    public static function getManageLinks()
    {
        $links[] = [
            'title' => WebMeetingModule::_t('#webmeeting_list'),
            'label' => WebMeetingModule::_t('#webmeeting'),
            'url'   => WebMeetingModule::getMyIndexLink()
        ];

        $links[] = [
            'title' => WebMeetingModule::_t('#webmeeting_team_list'),
            'label' => WebMeetingModule::_t('#teams'),
            'url'   => WebMeetingModule::getMyIndexLink('-team')
        ];
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
                            'create',
                            'update',
                            'delete',
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
