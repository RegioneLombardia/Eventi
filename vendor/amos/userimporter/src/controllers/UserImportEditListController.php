<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    amos\userimporter\controllers 
 */
 
namespace amos\userimporter\controllers;

use amos\userimporter\models\UserImportEditList;
use open20\amos\core\user\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Class UserImportEditListController 
 * This is the class for controller "UserImportEditListController".
 * @package amos\userimporter\controllers 
 */
class UserImportEditListController extends \amos\userimporter\controllers\base\UserImportEditListController
{

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'actions' => [
                                'check-email-exist',
                            ],
                            'allow' => true,
                            'roles' => ['USERIMPORTTASK_CREATE']
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'request-information' => ['post', 'get']
                    ]
                ]
            ]);

        return $behaviors;
    }

    /**
     *
     */
    public function init()
    {
        parent::init();
        \Yii::$app->params['bsVersion']                     = '4.x';
        \Yii::$app->view->params['customClassMainContent'] = 'box-container sidebar-setting';
        $this->setUpLayout('bootstrap-italia-layout-with-sidebar');
        \Yii::$app->getView()->params['bi-menu-sidebar']   = UserImportTaskController::getSidebar();
    }


    /**
     * @param $email
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCheckEmailExist($email){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $exist = false;
        $users = User::find()->andWhere(['email' => $email])->all();
        $countUser = UserImportEditList::find()->andWhere(['email' => $email])->count();

        if(count($users) > 0){
            $message = \Yii::t('amosuserimporter',"L'utente è già registrato");
            $exist = true;
        }else if($countUser > 0) {
            $message = \Yii::t('amosuserimporter',"L'utente è già presente nella corrente lista di importazione");
            $exist = true;
        }
        return [
            'exist' => $exist,
            'message'=> $message
        ];

    }
}
