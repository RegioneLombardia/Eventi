<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\groups\controllers
 */

namespace open20\amos\groups\controllers;

use open20\amos\groups\models\GroupsComunication;
use open20\amos\groups\Module;
use open20\amos\groups\utility\GroupsMailUtility;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Class GroupsComunicationController
 * This is the class for controller "GroupsComunicationController".
 * @package open20\amos\groups\controllers
 */
class GroupsComunicationController extends \open20\amos\groups\controllers\base\GroupsComunicationController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'send-email-test',
                            'send-email',
                            'duplicate'
                        ],
                        'roles' => ['AMMINISTRATORE_GROUPS']
                    ],

                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get']
                ]
            ]
        ]);
    }

    /**
     * @return string
     */
    public function actionSendEmailTest()
    {
        $this->model = new GroupsComunication();
        $post = \Yii::$app->request->post();
        $ok = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;

        if ($this->model->load($post)) {
            $text = $this->model->text;
            $subject = $this->model->subject;
            $emailTo = $this->model->email;
            $from = \Yii::$app->params['email-assistenza'];;

            $ok = GroupsMailUtility::sendEmailGeneral($from, $emailTo, $subject, $text);
            if ($ok) {
                return $emailTo;
            } else return 'Nessuno';
        }
        return '';

    }

    /**
     * @param $id
     * @return Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionSendEmail($id)
    {

        $this->model = $this->findModel($id);
        if($this->model->status == GroupsComunication::STATUS_DRAFT) {
            $group = $this->model->groups;
            $userMembers = $group->users;
            $emailList = [];
            foreach ($userMembers as $user) {
                $emailList [] = $user->email;
            }

            $text = $this->model->text;
            $subject = $this->model->subject;
            $from = \Yii::$app->params['email-assistenza'];;

            $ok = GroupsMailUtility::sendEmailGeneral($from, $emailList, $subject, $text);
            if ($ok) {
                \Yii::$app->session->addFlash('success', Module::t('amosgroups','Comunicazione inviata correttamente'));

                $this->model->sent_at = date('Y-m-d H:i:s');
                $this->model->status = GroupsComunication::STATUS_SENT;
                $this->model->save(false);
            }
        }else {
            \Yii::$app->session->addFlash('danger', Module::t('amosgroups','Comunicazione già inviata'));
        }
        return $this->redirect(['/groups/groups-comunication', 'idGroup' => $this->model->groups_id]);

    }

    /**
     * @param $id
     * @return Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDuplicate($id){
        $this->model = $this->findModel($id);
        $duplicate = new GroupsComunication();
        $duplicate->groups_id = $this->model->groups_id;
        $duplicate->subject = $this->model->subject . ' ' .('(Copy)');
        $duplicate->text = $this->model->text;
        $duplicate->save(false);
        \Yii::$app->session->addFlash('success', Module::t('amosgroups','Comunicazione duplicata'));


        return $this->redirect(['/groups/groups-comunication', 'idGroup' => $this->model->groups_id]);

    }
}
