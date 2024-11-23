<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\invitations
 * @category   CategoryName
 */

namespace open20\amos\invitations\controllers;

use open20\amos\admin\AmosAdmin;
use open20\amos\invitations\models\GoogleInvitationForm;
use open20\amos\invitations\models\Invitation;
use open20\amos\invitations\models\InvitationUser;
use open20\amos\invitations\models\UserToInvite;
use open20\amos\invitations\Module;
use open20\amos\invitations\utility\InvitationsUtility;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;

/**
 * This is the class for controller "InvitationController".
 */
class InvitationController extends base\InvitationController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $result = ArrayHelper::merge(parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'ruleConfig' => [
                        'class' => AccessRule::className(),
                    ],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                'check-email-ajax',
                                'download-import-template',
                                'invite-user',
                                'invite-google',
                                'invitations-sent',
                                're-send'
                            ],
                            'roles' => ['INVITATIONS_ADMINISTRATOR', 'INVITATIONS_BASIC_USER']
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                'index-all',
                            ],
                            'roles' => ['INVITATIONS_ADMINISTRATOR']
                        ],
                    ],

                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'remove-prevalent-partnership' => ['post'],
                        'delete' => ['post', 'get']
                    ]
                ]
            ]);

        return $result;
    }

    /**
     * @param string $email
     * @return string
     */
    public function actionCheckEmailAjax($email = '')
    {
        $responseArray = ['success' => 1];
        $response = InvitationsUtility::checkUserAlreadyPresent($email, true, true);
        if(!empty($response['present']) && $response['present']){
            $responseArray = ArrayHelper::merge(['success' => 0, 'messageConfirm' => ''], $response) ;
            return json_encode($responseArray);
        }
        if (!Invitation::alreadySended($email)) {
            $responseArray = ArrayHelper::merge($responseArray, [
                'message' => '',
                'messageConfirm' => \Yii::t('amosinvitations', 'Are you sure to send this invitation?'),
            ]);
            return json_encode($responseArray);
        } else {
            $invitationUser = InvitationUser::getInvitationUserFromEmail($email);
            if (!empty($invitationUser)) {
                $num = $invitationUser->numberNotificationSended;
                $messageConfirm = \Yii::t('amosinvitations', "To this email have already been sent {numInviti} invitations, send the invitation again?", ['numInviti' => $num]);
                $message = \Yii::t('amosinvitations', "To this email have already been sent {numInviti} invitations", ['numInviti' => $num]);
                $responseArray = ArrayHelper::merge($responseArray, [
                    'message' => $message,
                    'messageConfirm' => $messageConfirm,
                ]);
                return json_encode($responseArray);
            } else {
                $responseArray = ArrayHelper::merge($responseArray, [
                    'message' => '',
                    'messageConfirm' => \Yii::t('amosinvitations', 'Are you sure to send this invitation?'),
                ]);
                return json_encode($responseArray);
            }
        }
    }

    /**
     * Used by invitation widget to send invitation from modal
     * @return bool|string
     */
    public function actionInviteUser()
    {
        $view = '@vendor/open20/amos-invitations/src/widgets/views/invite-user';
        $invitation = new Invitation();
        $invitationUser = new InvitationUser();
        $this->layout = false;
        if (Yii::$app->getRequest()->isAjax) {
            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                if ($invitation->load($post) && $invitation->validate($post) && $invitationUser->load($post) && $invitationUser->validate($post)) {
                    return $this->sendInvitation(null, $invitation, $invitationUser);
                }
            }
        }
        return $this->renderAjax($view, ['invitation' => $invitation, 'invitationUser' => $invitationUser]);
    }


    /**
     * @return \yii\console\Response|\yii\web\Response
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Writer_Exception
     * @throws \yii\base\Exception
     */
    public function actionDownloadImportTemplate()
    {
        $fileName = 'inviti.xlsx';
        $storePath = \Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . 'invitations' . DIRECTORY_SEPARATOR . 'docs';

        if (!is_dir($storePath)) {
            BaseFileHelper::createDirectory($storePath, 0775, true);
        }

        $path = $storePath . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($path)) {
            $this->createImportTemplate($path);
        }

        return \Yii::$app->response->sendFile($path);
    }

    /**
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    private function createImportTemplate($path)
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'EMAIL');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'NOME');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'COGNOME');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'MESSAGGIO PERSONALE');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($path);
    }

    /**
     * @param int $id
     * @return string
     */
    public function actionInvitationsSent($id)
    {
        $model = Invitation::findOne($id);
        if ($this->moduleName && $this->contextModelId) {
            $model->module_name = $this->moduleName;
            $model->context_model_id = $this->contextModelId;
        }
        return $this->renderAjax('_invitations_sent', ['model' => $model]);
    }

    /**
     * Send invitations to the platform to the selected google contacts
     *
     * @param null $search
     * @return string|\yii\web\Response
     */
    public function actionInviteGoogle($search = null)
    {
        $this->setUpLayout('form');

        $sentInvitations = 0;
        $send = false;
        $selection = [];
        $invitationForm = new GoogleInvitationForm();
        $post = Yii::$app->request->post();
        $searchText = $search;
        if(array_key_exists('search',$post)){
            $searchText = $post['search'];
        }
        if($invitationForm->load($post)){
            $selection = explode(',', $invitationForm->selection);
            $searchText = $invitationForm->search;
            if($invitationForm->validate() ) {
                $send = true;
            }
        }
        $session = Yii::$app->session;
        $contacts = $session->get(AmosAdmin::GOOGLE_CONTACTS);
        $contactsNotPlatform = $session->get(AmosAdmin::GOOGLE_CONTACTS_NOT_PLATFORM);
        $allModels = [];
        $allModelsSelected = [];
        $contactsNotPlatform = array_unique($contactsNotPlatform);
        foreach ($contactsNotPlatform as $contactKey){
            if(array_key_exists($contactKey, $contacts)){
                $contactToInvite = new UserToInvite();
                $invitationUser = null;
                $contact = $contacts[$contactKey];
                if(!empty($contact['names'])){
                    $names = $contact['names'];
                    if(!empty($names['name'])){
                        $contactToInvite->name = $names['name'][0];
                    }
                    if(!empty($names['surname'])){
                        $contactToInvite->surname = $names['surname'][0];
                    }
                    if(!empty($names['displayName'])){
                        $contactToInvite->displayName = $names['displayName'][0];
                    }
                }
                if(!empty($contact['email'])){
                    $contactToInvite->email = $contact['email'];
                }
                if(!empty($contact['photos'])){
                    $photos = $contact['photos'];
                    if(!empty($photos['url'])){
                        $contactToInvite->photoUrl = $photos['url'][0];
                    }
                }
                if($contactToInvite->email && $contactToInvite->name && $contactToInvite->surname) {
                    $invitationUser = InvitationUser::getInvitationUserFromEmail($contactToInvite->email);
                    if(!is_null($invitationUser)) {
                        $contactToInvite->invitationUserId = $invitationUser->id;
                        $contactToInvite->sentInvitations = $invitationUser->getNumberNotificationSendedByMe();
                    }
                    if (in_array($contactToInvite->email, $selection) && !array_key_exists($contactToInvite->email, $allModels)) {
                        /** @var InvitationUser $invitationUser */

                        $contactToInvite->selected = true;
                        $allModelsSelected[$contactToInvite->email] = $contactToInvite;
                        if($send) {
                            if (is_null($invitationUser)) {
                                $invitationUser = new InvitationUser();
                                $invitationUser->email = $contactToInvite->email;
                                $invitationUser->save();
                            }
                            $invitation = new Invitation();
                            $invitation->message = $invitationForm->message;
                            $invitation->invitation_user_id = $invitationUser->id;
                            $invitation->name = $contactToInvite->name;
                            $invitation->surname = $contactToInvite->surname;
                            if ($invitation->validate()) {
                                $invitation = $this->sendMailInvitation($invitation);
                                if ($invitation->save()) {
                                    $sentInvitations++;
                                }
                            }
                        }
                    }
                    if(empty($searchText)){
                        $allModels[$contactToInvite->email] = $contactToInvite;
                    }else{
                        if(strstr($contactToInvite->displayName, $searchText) || strstr($contactToInvite->email, $searchText) ){
                            $allModels[$contactToInvite->email] = $contactToInvite;
                        }
                    }

                }
            }
        }
        if($sentInvitations){
            Yii::$app->session->addFlash('success', Module::t('amosinvitations', '{sentInvitations} invitations sent successfully', ['sentInvitations' => $sentInvitations]));
            if (Yii::$app->user->can('ADMIN')) {
                return $this->redirect(['index-all']);
            } else {
                return $this->redirect(['index']);
            }
        }
        $dataProvider = new ArrayDataProvider([
            'modelClass' => UserToInvite::className(),
            'allModels' => $allModels,
            'pagination' => ['pageSize' => 10, 'params' => ArrayHelper::merge($_GET, ['search' => $searchText])]
        ]);
        $dataProviderSelected = new ArrayDataProvider([
            'modelClass' => UserToInvite::className(),
            'allModels' => $allModelsSelected,
            'pagination' => false
        ]);
        return $this->render('invite-google', [
            'dataProvider' => $dataProvider,
            'dataProviderSelected' => $dataProviderSelected,
            'invitationForm' => $invitationForm
        ]);
    }
}
