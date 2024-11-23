<?php
namespace backend\modules\ampevent\controllers;


use open20\amos\core\controllers\BackendController;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventLanding;
use open20\amos\events\models\search\EventSearch;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UrlManager;

class AmpController extends BackendController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'event',
                            'esplora-eventi',
                            'grattacielo-pirelli',
                            'palazzo-lombardia',
                        ],
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
    public function init()
    {
        parent::init();
        $this->setUpLayout('@frontend/views/layouts/amp');
    }

    /**
     * @param $eventpage
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionEvent($eventpage=null){
        $pages = (new Query())
            ->select(['cms_nav.id', 'nav_container_id', 'parent_nav_id', 'is_hidden', 'is_offline', 'is_draft', 'is_home', 'cms_nav_item.title'])
            ->from('cms_nav')
            ->leftJoin('cms_nav_item', 'cms_nav.id=cms_nav_item.nav_id')
            ->where(['cms_nav_item.alias' =>$eventpage,
                'cms_nav.is_deleted' => false
            ])
            ->all();

        if(!empty($pages) && count($pages) > 0){
            $page = $pages[0];
            $landing = EventLanding::find()->andWhere(['luya_page_id' => $page['id']])->one();
            $event = $landing->event;

            if($event){
                return $this->render('event', ['model' => $event, 'landing' => $landing]);
            }
        }
        throw new NotFoundHttpException("Pagina non trovata");

    }

    public function actionEsploraEventi(){
       $modelSearch =  new EventSearch();
       $dataProvider = $modelSearch->cmsPublishedSearch(['day' => 'all']);
       $dataProviderStreaming = $modelSearch->cmsTodayInStreaming([]);
        return $this->render('esplora-eventi',['dataProvider' => $dataProvider, 'dataProviderStreaming' => $dataProviderStreaming]);
    }

    public function actionGrattacieloPirelli(){
        return $this->render('grattacielo-pirelli');
    }

    public function actionPalazzoLombardia(){
        return $this->render('palazzo-lombardia');
    }


}