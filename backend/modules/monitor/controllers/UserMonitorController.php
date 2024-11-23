<?php

namespace backend\modules\monitor\controllers;

use open20\amos\admin\models\UserProfile;
use open20\amos\core\controllers\BackendController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use amos\userimporter\models\UserImportTask;
use open20\amos\events\models\EventGroupReferentMm;
use open20\amos\events\models\EventInvitationSent;

class UserMonitorController extends BackendController
{


    const LOG_TYPE_ACCOUNT_DELETED = 'account_deleted';
    const LOG_TYPE_PROFILE_UPDATED = 'profile_updated';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'actions' => ['user-history'],
                            'allow' => true,
                            'roles' => ['AMMINISTRATORE_UTENTI']  // for all log in users

                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'public' => ['post', 'get']
                    ]
                ]
            ]
        );

        return $behaviors;
    }

    /**
     * @param $user_id
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUserHistory($user_id){
        $model = UserProfile::find()->andWhere(['user_id' => $user_id])->one();
        if(empty($model)){
            throw new NotFoundHttpException(\Yii::t('app', "Pagina non trovata"));
        }

        $dataProviderDgs = new \yii\data\ActiveDataProvider([
            'query' => EventGroupReferentMm::find()->andWhere(['user_id' => $model->user_id])->groupBy('event_group_referent_id')
        ]);

        $queryInvite = EventInvitationSent::find()
            ->innerJoin('event_internal_list', 'event_internal_list.id = event_invitation_sent.event_internal_list_id')
            ->andWhere(['user_id' => $model->user_id])
            ->andWhere(['event_internal_list.deleted_at' => null])
            ->groupBy('event_internal_list.event_id');
        $dataProviderInvitations = new \yii\data\ActiveDataProvider([
            'query' => $queryInvite
        ]);

        $queryRegister = \open20\amos\community\models\CommunityUserMm::find()
            ->innerJoin('event', 'event.community_id = community_user_mm.community_id')
            ->innerJoin('event_invitation', 'event_invitation.user_id = community_user_mm.user_id AND event_invitation.event_id = event.id')
            ->andWhere(['community_user_mm.user_id' => $model->user_id])
            ->groupBy('event.id');
        $dataProviderregistration = new \yii\data\ActiveDataProvider([
            'query' => $queryRegister
        ]);

        $queryPresenza = \open20\amos\events\models\EventInvitation::find()
            ->innerJoin('event', 'event.id = event_invitation.event_id')
            ->andWhere(['event_invitation.user_id' => $model->user_id])
            ->andWhere(['presenza' => 1])
            ->groupBy('event.id');
        $dataProviderPresenza = new \yii\data\ActiveDataProvider([
            'query' => $queryPresenza
        ]);

        $dataProviderHistory = new \yii\data\ActiveDataProvider([
            'query' => \open20\amos\core\models\UserActivityLog::find()->andWhere(['user_id' => $model->user_id])->orderBy('created_at')
        ]);

        return $this->render('user_history',  [
            'model' => $model,
            'dataProviderDgs' => $dataProviderDgs,
            'queryInvite' => $queryInvite,
            'dataProviderInvitations' => $dataProviderInvitations,
            'queryRegister' => $queryRegister,
            'dataProviderregistration' => $dataProviderregistration,
            'queryPresenza' => $queryPresenza,
            'dataProviderPresenza' => $dataProviderPresenza,
            'dataProviderHistory' => $dataProviderHistory
        ]);
    }

}