<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events\controllers\base
 */

namespace open20\amos\events\controllers\base;

use open20\amos\core\response\Response;
use open20\amos\events\AmosEvents;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventHighlights;
use open20\amos\events\models\EventLanding;
use open20\amos\events\models\EventType;
use open20\amos\events\models\search\EventSearch;
use open20\amos\events\utility\EventsUtility;
use open20\amos\events\utility\OrderElementsUtility;
use Yii;
use open20\amos\events\models\EventLandingSection;
use open20\amos\events\models\search\EventLandingSectionSearch;
use open20\amos\core\controllers\CrudController;
use open20\amos\core\module\BaseAmosModule;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\helpers\Html;
use open20\amos\core\helpers\T;
use yii\helpers\Url;


/**
 * Class EventLandingSectionController
 * EventLandingSectionController implements the CRUD actions for EventLandingSection model.
 *
 * @property \open20\amos\events\models\EventLandingSection $model
 * @property \open20\amos\events\models\search\EventLandingSectionSearch $modelSearch
 *
 * @package open20\amos\events\controllers\base
 */
class EventLandingSectionController extends CrudController
{

    /**
     * @var string $layout
     */
    public $layout = 'main';


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                'order',
                                'index',
                                'reset-order',
                                'active-sections-ajax'
                            ],
                            'roles' => ['@']
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
        return $behaviors;
    }


    public function init()
    {
        $this->setModelObj(new EventLandingSection());
        $this->setModelSearch(new EventLandingSectionSearch());

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt') . Html::tag('p', BaseAmosModule::tHtml('amoscore', 'Table')),
                'url' => '?currentView=grid'
            ],
        ]);

        parent::init();
        \Yii::$app->params['bsVersion'] = '4.x';
        \Yii::$app->view->params['customClassMainContent'] = 'box-container sidebar-setting';
        $this->setUpLayout('bootstrap-italia-layout-with-sidebar', []);
    }


    /**
     * @param $id
     * @param $layout
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex($id, $layout = NULL)
    {
        $model = Event::findOne($id);
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarEvents($model);
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['enableChangeView'] = true;
        \Yii::$app->view->params['createNewBtnParams']['layout'] = '';
        \Yii::$app->view->params['additionalButtons'] = [
            'htmlButtons' => [
                Html::a(AmosEvents::t('amosevents', 'Resetta ordinamento'), ['/events/event-landing-section/reset-order', 'id' => $id], [
                    'class' => 'btn btn-secondary btn-xs',
                    'title' => AmosEvents::t('amosevents', 'Ripristina ordinamento di default')
                ])
            ]
        ];


        $landing = $model->eventLanding;
        Url::remember();
        $sectionConfigs = $model->getConfigLandingSections();
        $sections = [];
        $i = 1;
        foreach ($sectionConfigs as $sectioname => $config) {
            if ($config['to_order']) {
                $sections[$i] = $sectioname;
                $i++;
            }
        }
        $modelSections = EventLandingSection::find()->andWhere(['event_landing_id' => $landing->id])->asArray()->all();
//        $dataProvider = new ActiveDataProvider([
//            'query' =>
//        ]);
        if (count($modelSections) == 0) {
            foreach ($sections as $n => $section) {
                $landingSection = new EventLandingSection();
                $landingSection->event_landing_id = $landing->id;
                $landingSection->n_order = $n;
                $landingSection->section = $section;
                $landingSection->save(false);
            }
        } else {
            $maxOrder = EventLandingSection::find()->andWhere(['event_landing_id' => $landing->id])->max('n_order');
            foreach ($sections as $section) {
                $key = array_search($section, array_column($modelSections, 'section'));
                if (($key === false)) {
                    $landingSection = new EventLandingSection();
                    $landingSection->event_landing_id = $landing->id;
                    $landingSection->n_order = $maxOrder + 1;
                    $landingSection->section = $section;
                    $landingSection->save(false);
                }
            }
        }
        $dataProvider = new ActiveDataProvider([
            'query' => EventLandingSection::find()
                ->andWhere(['event_landing_id' => $landing->id])
                ->orderBy('n_order'),
            'pagination' => false
        ]);
        $this->setDataProvider($dataProvider);
        $max = \open20\amos\events\models\EventLandingSection::find()->andWhere(['event_landing_id' => $landing->id])->max("n_order");
        $min = \open20\amos\events\models\EventLandingSection::find()->andWhere(['event_landing_id' => $landing->id])->min("n_order");

        return $this->render(
            'index',
            [
                'max' => $max,
                'min' => $min,
                'model' => $model,
                'landing' => $landing,
                'dataProvider' => $this->getDataProvider(),
                'currentView' => $this->getCurrentView(),
                'availableViews' => $this->getAvailableViews(),
            ]
        );
    }

    public function actionOrder($id, $event_id, $direction)
    {
        $landing = EventLanding::find()->andWhere(['event_id' => $event_id])->one();
        $elements = EventLandingSection::find()
            ->andWhere(['event_landing_id' => $landing->id])
            ->orderBy('n_order ASC')->all();
        $orderList = [];
        foreach ($elements as $element) {
            $orderList [] = $element->id;
        }

        //find the element  in the ids array and move it up or down
        $indexElemToMove = array_search($id, $orderList);

        if ($direction == 'up') {
            $orderList = OrderElementsUtility::up($orderList, $indexElemToMove);
        } else {
            $orderList = OrderElementsUtility::down($orderList, $indexElemToMove);
        }
        //save the element with the new order
        $this->resetNumberOrder($orderList);

        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return true;
        }

        return $this->redirect(['/events/event-landing-section/index', 'id' => $event_id]);

    }

    public function actionResetOrder($id)
    {
        $landing = EventLanding::find()->andWhere(['event_id' => $id])->one();
        if ($landing) {
            EventLandingSection::deleteAll(['event_landing_id' => $landing->id]);
            \Yii::$app->session->addFlash('success', "Ordinamento resettato");
        }
        return $this->redirect(['/events/event-landing-section/index', 'id' => $id]);
    }

    /**
     * @param $orderList
     */
    public function resetNumberOrder($orderList)
    {
        $i = 1;
        foreach ($orderList as $id) {
            $containerElemMm = EventLandingSection::findOne($id);
            $containerElemMm->n_order = $i;
            $containerElemMm->save(false);
            $i++;
        }
    }

    /**
     * @param $id
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionActiveSectionsAjax($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        /** @var  $landing EventLanding */
        $landing = EventLanding::find()->andWhere(['event_id' => $id])->one();
        $params = ['conditionSearch' => "['event_id' => {$id}]"];
        $modelSearch = new EventSearch();
        $sections = [];
        if ($landing) {
            $dataProviderProtagonists = $modelSearch->cmsSearchProtagonists($params);
            $dataProviderNews = $modelSearch->cmsSearchNews($params);
            $dataProviderDocs = $modelSearch->cmsSearchDocuments($params);
            $dataProviderGallery = $modelSearch->cmsSearchGallery($params);
            $dataProviderVideo = $modelSearch->cmsSearchVideo($params);
            $dataProviderInstagram = $modelSearch->cmsSearchInstagramVideo($params);
            $dataProviderChildren = $modelSearch->cmsChildrenSearch($params);
            $dataProviderRelatedEvents = $modelSearch->cmsSearchRelatedEvents($params);
            $dataProviderTimePeriods = $modelSearch->cmsTimePeriodsSearch($params);

            $sections = [
                'location' => true,
                'presentation' => true,
                'schedule' => true,
                'protagonists' => $dataProviderProtagonists->count > 0,
                'news' => $dataProviderNews->count > 0,
                'documents' => $dataProviderDocs->count > 0,
                'image-gallery' => $dataProviderGallery->count > 0,
                'video-gallery' => $dataProviderVideo->count > 0,
                'instagram' => $dataProviderInstagram->count > 0,
                'rating' => $landing->enable_rating,
                'related_events' => $dataProviderRelatedEvents > 0,
                'request_info' => !empty($landing->contact_info_organizator),
                'share' => $landing->is_social_share_enabled,
                'children_events' => $dataProviderChildren->count > 0,
                'time-period' => $dataProviderTimePeriods > 0,
                'landing_form' => $landing->event->eventType->event_type != EventType::TYPE_INFORMATIVE
            ];
        }
        return $sections;
    }

}
