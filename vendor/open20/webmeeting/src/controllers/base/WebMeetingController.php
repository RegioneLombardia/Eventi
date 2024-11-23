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
use open20\webmeeting\utility\WebMeetingUtility;
use open20\webmeeting\providers\api\WebExMeeting;
use open20\webmeeting\models\WebMeetingInviteeModel;

use open20\amos\admin\AmosAdmin;
use open20\amos\core\helpers\Html;
use open20\amos\core\user\User;
use open20\amos\admin\models\search\UserProfileSearch;

use Hybridauth\Data;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\web\HttpException;

/**
 * WebMeetingController
 */
class WebMeetingController extends WebMeetingBaseController
{
    /**
     *
     * @var type
     */
    protected $coHostUserEmail;

    /**
     *
     * @var type
     */
    protected $apiToCall;

    /**
     *
     * @var type
     */
    protected $timezone;

    /**
     *
     * @var type
     */
    protected $webMeetingInviteeModel;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->webMeetingModule = Yii::$app->getModule(WebMeetingModule::getModuleName());
        $this->setModelObj($this->webMeetingModule::instance()->createModel('WebMeetingModel'));
        $this->setModelSearch($this->webMeetingModule::instance()->createModel('WebMeetingModelSearch'));
        $tokenModel = $this->webMeetingModule::instance()
            ->createModel('WebMeetingTokenModel');

        $this->model = $this->getModelObj();

        $this->coHostUserEmail = ArrayHelper::map(
            WebMeetingUtility::getMeetingHostUsers(),
            'id',
            'value'
        );

        $this->timezone = ArrayHelper::map(
            WebMeetingUtility::getMeetingTimezone(),
            'id',
            'value'
        );

        $this->webMeetingInviteeModel = $this->webMeetingModule::instance()
            ->createModel('WebMeetingInviteeModel');

        $this->apiToCall = $this->webMeetingModule
            ->getHybridService(new WebExMeeting());

        $this->refreshToken = $tokenModel->find()
            ->andWhere(['user_id' => 1])
            ->one();

        if ($this->refreshToken) {
            $this->webMeetingModule
                ->getHybridService()
                ->setAccessTokenByRefreshToken($this->refreshToken);

            $tokenLife = strtotime($this->refreshToken->updated_at) + $this->refreshToken->expires_in;

            $todayDate = strtotime(WebMeetingModule::getTodayDate());
            $expired = $tokenLife <= $todayDate;

            if ($expired) {
                $token = $this->webMeetingModule->getHybridService()->refreshAccessToken();
                $data = (new Data\Parser())->parse($token);
                $this->refreshToken->access_token = $data->access_token;
                $this->refreshToken->expires_in = $data->expires_in;
                $this->refreshToken->save();
            }
        }

        $this->view->params = [
            'subTitleSection' => Html::tag('p', WebMeetingModule::_t('')),
            'isGuest' => Yii::$app->user->isGuest,
            'modelLabel' => WebMeetingModule::getModuleName(),
            'titleSection' => WebMeetingModule::_t('#webmeeting'),
            'urlLinkAll' => WebMeetingModule::getMyIndexLink('-history'),
            'labelLinkAll' => WebMeetingModule::_t('#webmeeting_webmeeting_history'),
            'titleLinkAll' => WebMeetingModule::_t('#webmeeting_webmeeting_history'),
            'labelCreate' => WebMeetingModule::_t('#webmeeting_new'),
            'titleCreate' => WebMeetingModule::_t('#webmeeting_create'),
            'labelManage' => WebMeetingModule::_t('#manager'),
            'titleManage' => WebMeetingModule::_t('#meeting_manager'),
            'urlCreate' => WebMeetingModule::getCreateLink(),
            'urlManage' => WebMeetingModule::_t('#'),
        ];

        parent::init();
    }

    /**
     *
     * @param type $layout
     * @return type
     */
    public function actionIndex($layout = null)
    {
        Url::remember();
        $this->setUpLayout('list');

        $queryParams = ArrayHelper::merge([
            'end' => WebMeetingModule::getTodayDate(),
            'context' => is_array($this->scope) ? array_shift(array_keys($this->scope)) : null,
            'context_id' => is_array($this->scope) ? array_shift(array_values($this->scope)) : null
        ],
            Yii::$app->request->getQueryParams()
        );

        $this->setDataProvider($this->modelSearch->searchActiveWebMeeting($queryParams));

        $tokenModel = $this->webMeetingModule::instance()
            ->createModel('WebMeetingTokenModel');

        $this->view->params['dataProvider'] = $this->getDataProvider();
        $this->view->params['todayDateTime'] = WebMeetingModule::getTodayDate();
        $this->view->params['canUpdate'] = Yii::$app->getUser()->can(WebMeetingModule::ADMIN_WEBMEETING);
        $this->view->params['loginUser'] = Yii::$app->getUser();

        return \open20\amos\core\controllers\CrudController::actionIndex($layout);
    }

    /**
     *
     * @param type $id
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $apiToCall = $this->webMeetingModule->getHybridService(new WebExMeeting());

        if (!empty($model->meeting_id)) {
            $apiToCall->deleteMeeting($model->getAttributes());
        }

        // remove invitees too
        $invitees = WebMeetingInviteeModel::deleteAll(['webmeeting_id' => $id]);

        return parent::actionDelete($id);
    }

    /**
     *
     * @return type
     */
    public function _refreshPage($currentLayout = 'create')
    {
        Url::remember();
        $this->setUpLayout('form');

        $views = $this->getAvailableViews();
        $this->setCurrentView($views['icon']);

        if ($this->isPostAndValidate()) {
            // set context from scope
            $this->model->context = is_array($this->scope) ? array_shift(array_keys($this->scope)) : null;
            $this->model->context_id = is_array($this->scope) ? array_shift(array_values($this->scope)) : null;

            $flashSaveMessage = empty($this->model->id)
                ? '#item_created'
                : '#item_updated';

            $transaction = ActiveRecord::getDb()->beginTransaction();
            try {
                $createdOK = true;
                if ($this->model->save()) {
                    $allInvitedUsers = (!empty(Yii::$app->request->post()['selectedUsers']))
                        ? Yii::$app->request->post()['selectedUsers']
                        : [];

                    $userEmailsForInvitee = array_values(
                        ArrayHelper::map(
                            User::find()
                                ->andWhere(['id' => $allInvitedUsers])
                                ->select('email')
                                ->asArray()
                                ->all(),
                            'email',
                            'email'
                        )
                    );

                    $removedInvitees = WebMeetingUtility::saveInvitedUsers(
                        $this->webMeetingModule, $this->model, $allInvitedUsers
                    );

//                  $coHost = false;
//                    if ($this->model->allow_any_user_to_be_co_host) {
//                        $coHost                = true;
//                    }


//                    $user = User::findIdentity($this->user_id);

                    $userHosts = \open20\webmeeting\models\WebMeetingHostUsers::find()
                        ->andWhere(['email' => $this->model->host_email])
                        ->one();

                    $this->model->invitees = json_decode($this->model->invitees);
                    $this->model->invitees = ArrayHelper::merge([
                        [
                            "email" => $this->model->host_email,
                            "display_name" => $userHosts->display_name,
                            "co_host" => true
                        ]
                    ],
                        WebMeetingUtility::prepareUsersToInvitee($userEmailsForInvitee, false)
                    );

                    $apiParams = $this->model->getAttributes();
                    $apiResponse = ($currentLayout == $this->webMeetingModule::WEBMEETING_ACTION_CREATE)
                        ? $this->apiToCall->createMeeting($apiParams)
                        : $this->apiToCall->updateMeeting($apiParams);

                    if (!isset($apiResponse->message)) {
                        $apiResponse['meeting_id'] = $apiResponse['id'];
                        $apiResponse['id'] = $this->model->id;
                        $newApiResponse = [StringHelper::basename(get_class($this->model)) => $apiResponse];
                        $this->model->load($newApiResponse);

                        $this->model->save();

                        Yii::$app->getSession()->addFlash(
                            'success', WebMeetingModule::_t($flashSaveMessage)
                        );
                        $transaction->commit();

//                        // Send all required type emails
//                        $userEmailsForInvitee = WebMeetingUtility::prepareUsersToInvitee($userEmailsForInvitee, $coHost);
//
//                        $this->updateWebMeetingAndSendInvitations(
//                            $userEmailsForInvitee, $removedInvitees, $currentLayout
//                        );
                    } else {
                        $flashSaveMessage = $apiResponse->message;
                        $tmp = [];
                        foreach ($apiResponse->errors as $error) {
                            $tmp[] = $error->description;
                        }
                        $flashSaveMessage .= '<br />' . implode("<br />", $tmp);

                        Yii::$app->getSession()->addFlash(
                            'danger', WebMeetingModule::_t($flashSaveMessage)
                        );
                        Yii::getLogger()->log($apiResponse, \yii\log\Logger::LEVEL_ERROR);
                        $transaction->rollBack();
                    }
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                $flashSaveMessage = $e->getMessage();
                $createdOK = false;
                $currentLayout = $this->webMeetingModule::WEBMEETING_ACTION_CREATE;

                Yii::$app->getSession()->addFlash(
                    'danger', WebMeetingModule::_t($flashSaveMessage)
                );
                Yii::getLogger()->log($flashSaveMessage, \yii\log\Logger::LEVEL_ERROR);
            }

            if ($currentLayout == $this->webMeetingModule::WEBMEETING_ACTION_UPDATE) {
                return $this->redirect(['update', 'id' => $this->model->id]);
            }
        }

        $modelSearch = AmosAdmin::instance()->createModel('UserProfileSearch');
        $userDataProvider = $modelSearch->search(\Yii::$app->request->getQueryParams());
        if (empty($this->scope)) {
            $userDataProvider->pagination = ['pageSize' => WebMeetingModule::WEBMEETING_PAGE_SIZE];
        }
        $userDataProvider->setSort([
            'defaultOrder' => [
                'cognome' => SORT_ASC,
                'nome' => SORT_ASC
            ]
        ]);

        // remove invitees too
        $invitees = $this->webMeetingInviteeModel::find()
            ->andWhere(['webmeeting_id' => $this->model->id])
            ->all();

        return $this->render($currentLayout, [
            'model' => $this->model,
            'coHostUserEmail' => $this->coHostUserEmail,
            'timezone' => $this->timezone,
            'modelInvitees' => $this->webMeetingInviteeModel,
            'modelUser' => $modelSearch,
            'userDataProvider' => $userDataProvider,
            'loginUser' => Yii::$app->getUser(),
            'canUpdate' => Yii::$app->getUser()->can(WebMeetingModule::ADMIN_WEBMEETING),
            'scope' => $this->scope,
            'invitees' => $invitees,
            'todayDateTime' => WebMeetingModule::getTodayDate(),
            'currentView' => $this->getCurrentView(),
        ]);
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
                                'connect',
                                'invitee',
                                'goto-webex',
                                'set-online',
                                'get-online-users'
                            ],
                            'roles' => [
                                'ADMIN',
                                'BASIC_USER',
                                WebMeetingModule::ADMIN_WEBMEETING,
                                WebMeetingModule::USER_WEBMEETING,
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
