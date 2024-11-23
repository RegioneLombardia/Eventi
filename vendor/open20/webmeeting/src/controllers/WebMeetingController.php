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
use open20\webmeeting\providers\api\WebExMeetingParticipants;

use open20\amos\core\controllers\CrudController;
use open20\amos\core\user\User;
use open20\amos\admin\models\UserProfile;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class WebMeetingController 
 */
class WebMeetingController extends base\WebMeetingController
{
    /**
     * @return type
     */
    public function actionCreate()
    {
        $this->model->timezone = WebMeetingModule::WEBMEETING_DEFAULT_TIMEZONE;
        
        return $this->_refreshPage($this->webMeetingModule::WEBMEETING_ACTION_CREATE);
    }

    /**
     * Updates an existing WebMeeting model
     * If update is successful, the browser will be redirected to the 'view' page
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->model = $this->findModel($id);
        
        return $this->_refreshPage($this->webMeetingModule::WEBMEETING_ACTION_UPDATE);
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function actionGotoWebex($id)
    {
        $this->model = $this->findModel($id);
        
        return $this->render('webex-meet', [
            'model' => $this->model,
            'access_token' => $this->apiToCall->getAccessToken(),
            'user_id' => $this->user_id
        ]);
    }
    
//    /**
//     * 
//     * @return boolean
//     */
//    public function actionSetOnline()
//    {
//        if (Yii::$app->request->isAjax) {            
//            $data = Yii::$app->request->post();
//        
//            $invitee = WebMeetingInviteeModel::find()
//                ->andWhere(['webmeeting_id' => $data['id']])
//                ->andWhere(['user_id' => $data['uid']])
//                ->one();
//            if ($invitee) {
//                $invitee->online = $data['status'];
//                $invitee->save(false);
//            }
//        }
//        
//        return true;
//    }

//    /**
//     * 
//     * @return type
//     */
//    public function actionGetOnlineUsers()
//    {
//        $data = null;
//        if (Yii::$app->request->isAjax) {
//            
//            $this->apiToCall = $this->webMeetingModule
//                ->getHybridService(new WebExMeetingParticipants());
//
//            $data = Yii::$app->request->post();
//
//            $participants = $this->apiToCall->listMeetingParticipants($data);
//            $data = $this->renderPartial('parts/participants', [
//                'participants' => $participants,
//                'currentView' => $this->getCurrentView(),
//            ]);
//        }
//        
//        return $data;
//    }

    /**
     * 
     * @param type $layout
     * @return type
     */
    public function actionHistory($layout = null)
    {
        Url::remember();
        $this->setUpLayout('list');

        $queryParams = ArrayHelper::merge(
            [
                'context' => array_shift(array_keys($this->scope)),
                'context_id' => array_shift(array_values($this->scope)),
                'end' <= date('Y-m-d H:m:s')
            ],
            Yii::$app->request->getQueryParams()
        );
        
        $this->setDataProvider($this->modelSearch->search($queryParams));
        
        $this->view->params['dataProvider'] = $this->getDataProvider();
        
        return $this->render('index', [
            'model' => $this->model,
        ]);
    }

//    /**
//     * 
//     * @param type $apiResponse
//     * @param type $userEmailsForInvitee
//     * @param type $removedInvitees
//     * @param type $currentLayout
//     * @return type
//     */
//    public function updateWebMeetingAndSendInvitations($userEmailsForInvitee, $removedInvitees, $currentLayout)
//    {
//        foreach($userEmailsForInvitee as $userEmail) {
//            $ok = false;
//            $invitee = WebMeetingInviteeModel::find()
//                ->andWhere(['webmeeting_id' => $this->model->id])
//                ->andWhere(['email' => $userEmail])
//                ->andWhere(['sent_email' => null])
//                ->one();
//            
//            if (empty($invitee)) {
//                $invitee = $this->webMeetingModule::instance()
//                    ->createModel('WebMeetingInviteeModel');
//                $invitee->user_id = $userEmail['user_id'];
//                $invitee->webmeeting_id = $this->model->id;
//                $invitee->email = $userEmail['email'];
//                $invitee->display_name = $userEmail['display_name'];
//                $invitee->co_host = $userEmail['coHost'];
//                $invitee->sent_email = null;
//                $invitee->save();
//            }
//            
//            $apiToCall = $this->webMeetingModule->getHybridService(new WebExMeetingInvitees());
//
//            $params = [
//                'meetingId' => $model->meeting_id,
//                'email' => $userEmail['email'],
//                'displayName' => $userEmail['display_name'],
//                'coHost' => 0,
//            ];
//            $apiToCall->createMeetingInvitee($params);
//
//            if ($currentLayout == $this->webMeetingModule::WEBMEETING_ACTION_CREATE) {
//                $ok = WebMeetingUtility::sendWebexMeetInvitationEmail($this->model, $invitee->getAttributes());
//            } else if ($currentLayout == $this->webMeetingModule::WEBMEETING_ACTION_UPDATE) {
//                $ok = WebMeetingUtility::sendWebexMeetUpdatedEmail($this->model, $invitee->getAttributes());
//            }
//
//            if ($ok) {
//                $invitee->sent_email = 1;
//                $invitee->save(false);
//            }
//        }
//
//        // TBD send an email to user removed?
//        if ($removedInvitees) {
//            $invitees = WebMeetingInviteeModel::find()
//                ->andWhere(['webmeeting_id' => $this->model->id])
//                ->andWhere(['email' => $removedInvitees])
//                ->all();
//                        
//            foreach($invitees as $invitee) {
//                $invitee->delete();
//            }
//        }
//    }
}
