<?php

namespace open20\amos\events\controllers;

use open20\amos\core\components\AmosView;
use open20\amos\core\controllers\BackendController;
use open20\amos\events\AmosEvents;
use open20\amos\events\models\Event;
use open20\amos\events\models\search\EventSearch;
use open20\amos\favorites\models\Favorite;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class FavouritesController extends BackendController
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
                            'index',
                        ],
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

    public function init()
    {
        parent::init();
    }


    public function actionIndex()
    {
        $this->setUpLayout('@backend/views/layouts/landing-cms');

        $dataProvider = new ActiveDataProvider([
            'query' => \open20\amos\favorites\models\Favorite::find()
                ->leftJoin('event','event.id = favorite.content_id')
                ->andWhere(["!=", 'event.status',Event::EVENTS_WORKFLOW_STATUS_SUSPENDED])
                ->andWhere([ 'favorite.content_classname' => 'open20\amos\events\models\Event'])
                ->andWhere(['user_id' => \Yii::$app->user->id])
        ]);
        $labelNoFavorites = AmosEvents::t('amosfavorites', 'Attualmente non hai selezionato dei preferiti');

        return $this->render('index',[
            'noFavouriteLabel' => $labelNoFavorites,
            'dataProvider' => $dataProvider
        ]);

    }

}