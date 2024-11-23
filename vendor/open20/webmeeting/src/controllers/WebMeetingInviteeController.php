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
use open20\webmeeting\models\WebMeetingInviteesModel;
use open20\webmeeting\models\base\WebMeetingInviteeModel;
use open20\webmeeting\providers\api\WebExMeetingInvitees;
use open20\webmeeting\utility\WebMeetingUtility;

use open20\amos\core\helpers\Html;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Class WebMeetingInviteeController
 */
class WebMeetingInviteeController extends base\WebMeetingInviteeController
{
    /**
     * 
     * @param type $layout
     * @return type
     */
    public function actionIndex($layout = null)
    {
        Url::remember();
        $this->setUpLayout('list');

        $this->setDataProvider($this->modelSearch->search($queryParams));
        
        $this->view->params['dataProvider'] = $this->getDataProvider();
        
        return parent::actionIndex($layout);
    }
    
    /**
     * 
     * @param type $id
     */
    public function actionDelete($id)
    {
        $this->model = $this->findModel($id);
        $apiToCall = $this->webMeetingModule->getHybridService(new WebExMeetingInvitees());

        if ($this->model) {
            if (!empty($model->meeting_invitee_id)) {
                $apiToCall->deleteMeetingInvitee($model->getAttributes());
            }
        
            $this->model->delete();
            if (!$this->model->hasErrors()) {
                Yii::$app->getSession()->addFlash('success', WebMeetingModule::_t('#element_deleted'));
                WebMeetingInviteeModel::deleteAll(['id' => $id]);
            } else {
                Yii::$app->getSession()->addFlash('danger', WebMeetingModule::_t('#you_are_not_authorized_to_delete'));
            }
        } else {
            Yii::$app->getSession()->addFlash('danger', WebMeetingModule::_t('#not_found'));
        }

        return $this->redirect(WebMeetingModule::getMeetingInviteesToWebEx($this->model->webmeeting_id));
    }   

}