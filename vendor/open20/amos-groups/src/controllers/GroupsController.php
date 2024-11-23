<?php

namespace open20\amos\groups\controllers;

use open20\amos\core\utilities\Email;
use open20\amos\groups\models\Groups;
use open20\amos\groups\models\GroupsMailer;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;
use yii\helpers\BaseArrayHelper;
use yii\web\UploadedFile;
use open20\amos\groups\controllers\base\GroupsController as BaseGroupsController;

/**
 * This is the class for controller "GroupsController".
 */
class GroupsController extends BaseGroupsController
{

    public function behaviors()
    {
        return BaseArrayHelper::merge(parent::behaviors(),
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
                                'send-email-to-group'
                            ],
                            'roles' => ['@']
                        ],
                    ],
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
     * 
     * @param integer $idGroup
     * @return type
     */
    public function actionSendEmailToGroup($idGroup = null)
    {
        $mailer          = new GroupsMailer();
        $mailer->idGroup = $idGroup;

        if (\Yii::$app->request->post() && $mailer->load(\Yii::$app->request->post()) && $mailer->validate()) {

            /** @var  $model Groups */
            //$model = parent::findModel($idGroup);

            if (GroupsMailer::sendEmail($mailer->idGroup, $mailer->subject, $mailer->text)) {
                \Yii::$app->session->addFlash('success', \Yii::t('app', 'Email sent'));
                //$this->redirect(['index']);
            } else {
                \Yii::$app->session->addFlash('danger', \Yii::t('app', 'Email not sent'));
            }
        }
        return $this->render('_new_email', ['model' => $mailer]);
    }
}