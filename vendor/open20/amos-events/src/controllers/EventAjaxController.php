<?php

namespace open20\amos\events\controllers;

use open20\amos\core\components\AmosView;
use open20\amos\core\controllers\BackendController;
use open20\amos\events\models\Event;
use open20\amos\events\models\search\EventSearch;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class EventAjaxController extends BackendController
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
                            'render-event-section',
                            'render-event-section-v2',
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
        \yii\base\Event::on(\app\modules\cms\base\CmsView::className(), AmosView::EVENT_AFTER_RENDER, function ($e) {
            unset($e->sender->assetBundles['yii\web\YiiAsset']);
            unset($e->sender->assetBundles['yii\web\JqueryAsset']);
            unset($e->sender->assetBundles['yii\bootstrap\BootstrapPluginAsset']);
            unset($e->sender->assetBundles['yii\bootstrap\BootstrapAsset']);
            unset($e->sender->assetBundles['open20\amos\core\views\assets\CheckScopeAsset']);
            unset($e->sender->assetBundles['app\assets\ResourcesAsset']);
            unset($e->sender->assetBundles['open20\amos\layout\assets\IsotopeAsset']);
//            unset($e->sender->assetBundles['yii\widgets\ActiveFormAsset']);
            unset($e->sender->assetBundles['yii\validators\ValidationAsset']);
//            pr($e->sender->assetBundles, 'sssss');
//            $e->sender->assetBundles = [];
        });
    }


    public function actionRenderEventSection($view = null, $methodSearch = null, $withPagination = null, $conditionSearch = null,
                                             $numElementsPerPage = null, $blockItemId = null, $withoutSearch = null, $viewFields = null,
                                             $language = 'it-IT', $currentUrl = null)
    {

        \Yii::$app->language = $language;
        $searchModel = new EventSearch();
//        $searchModel = new $this->modelSearchClass();
        $limit = null;
        if (!$withPagination) {
            $limit = $numElementsPerPage;
        }
        if (empty(trim($methodSearch))) {
            $methodSearch = "cmsSearch";
        }
        $parms['conditionSearch'] = $conditionSearch;
        $parms['withPagination'] = $withPagination;
        $parms['withoutSearch'] = $withoutSearch;
        $parms['methodSearch'] = $methodSearch;
        $parms['numElementsPerPage'] = $numElementsPerPage;
        $parms['blockItemId'] = $blockItemId;

        $dataProvider = $searchModel->$methodSearch($parms, $limit);
        if (!is_null($dataProvider)) {
            if ($withPagination) {
                $pagination = $dataProvider->getPagination();
                if (!$pagination) {
                    $pagination = new Pagination();
                    $dataProvider->setPagination($pagination);
                }
                $pagination->route = \Yii::$app->request->getPathInfo();
                $pagination->setPageSize($numElementsPerPage);
            } else {
                $dataProvider->setPagination(FALSE);
            }
        }
        \Yii::$app->request->queryParams = array_merge([
            "parms" => $parms],
            \Yii::$app->request->queryParams
        );

        return $this->renderAjax('@frontend/modules/backendobjects/frontend/views/default/' . $view, [
            'dataProvider' => $dataProvider,
            'blockItemId' => $blockItemId,
            'parms' => $parms,
            'withoutSearch' => $withoutSearch,
            'viewFields' => json_decode($viewFields),
            'currentUrl' => $currentUrl

        ]);
    }

    public function isEnabledSectionCache($section){
        return Event::getConfigsAttrSingleSection($section, 'cache');
    }

    /**
     * @param $event_id
     * @param $section
     * @param $language
     * @param $currentUrl
     * @param $enableCache
     * @return false|string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRenderEventSectionV2($event_id, $section = null, $language = 'it-IT', $currentUrl = null, $enableCache = false)
    {
        $disableCache = false;
        if(!empty(\Yii::$app->params['disablePageCache'])){
            $disableCache = true;
        }


        $cache = \Yii::$app->landingCache;
        $isEnabledSectionCache = $this->isEnabledSectionCache($section);
        $keyCache = ['content_event_ajax',$section, $language, $event_id];

        \Yii::$app->language = $language;
        $limit = null;
        $withPagination = false;
        $numElementsPerPage = 20;
        $renderPage =  (!$disableCache && $enableCache && $isEnabledSectionCache) ? $cache->get($keyCache): false;

        if ($renderPage === false) {
            $dependency = \Yii::createObject([
                'class' => 'yii\caching\DbDependency',
                'sql' => Event::CACHE_SQL_DEPENDENCY,
            ]);


            $model = Event::findOne($event_id);
            $eventLanding = $model->eventLanding;

            $searchModel = new EventSearch();
            $methodSearch = Event::getConfigsAttrSingleSection($section, 'search');
            $view = Event::getConfigsAttrSingleSection($section, 'view');
//        $searchModel = new $this->modelSearchClass();

            $dataProvider = null;
            $params = ['conditionSearch' => "['event_id' => {$model->id}]"];

            $totalCount = 1;
            if (!empty($methodSearch)) {
                $totalCount = 0;
                $dataProvider = $searchModel->$methodSearch($params, $limit);
                if (!is_null($dataProvider)) {
                    if ($withPagination) {
                        $pagination = $dataProvider->getPagination();
                        if (!$pagination) {
                            $pagination = new Pagination();
                            $dataProvider->setPagination($pagination);
                        }
                        $pagination->route = \Yii::$app->request->getPathInfo();
                        $pagination->setPageSize($numElementsPerPage);
                    } else {
                        $dataProvider->setPagination(FALSE);
                    }
                    $totalCount = $dataProvider->totalCount;

                }
            }
            if ($totalCount == 0) {
                return '';
            }


            $renderPage = $this->renderAjax('@frontend/modules/uikit/views/eventlanding/' . $view, [
                'dataProvider' => $dataProvider,
                'model' => $model,
                'eventLanding' => $eventLanding,
                'currentUrl' => $currentUrl,
                'totalCount' => $totalCount

            ]);
            $cache->set($keyCache, $renderPage, Event::CACHE_DURATION, $dependency);
        }

        return $renderPage;
    }

}