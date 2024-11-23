<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Proscriptions/proscription-default.txt to change this proscription
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace open20\amos\events\controllers;

use open20\amos\core\helpers\Html;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventLocation;
use open20\amos\events\models\EventLocationEntrances;
use yii\bootstrap4\ActiveForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
/**
 * Description of EventWebexController
 *
 */
class EventWebexController  extends base\EventWebexController {
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
                            'index',
                            'delete',
                            'update',
                            'assign',
                        ],
                        'roles' => ['EVENTS_ADMINISTRATOR']
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get'],
                    'assign' => ['post', 'get']
                ]
            ]
        ]);
    }
}
