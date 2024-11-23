<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events\controllers\base
 */

namespace open20\amos\events\controllers\base;

use open20\amos\events\models\Event;
use open20\amos\events\models\EventHighlights;
use open20\amos\events\models\EventLanding;
use open20\amos\events\models\search\EventSearch;
use open20\amos\events\utility\EventsUtility;
use Yii;
use open20\amos\events\models\EventRelated;
use open20\amos\events\models\search\EventRelatedSearch;
use open20\amos\core\controllers\CrudController;
use open20\amos\core\module\BaseAmosModule;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\helpers\Html;
use open20\amos\core\helpers\T;
use yii\helpers\Url;


/**
 * Class EventRelatedController
 * EventRelatedController implements the CRUD actions for EventRelated model.
 *
 * @property \open20\amos\events\models\EventRelated $model
 * @property \open20\amos\events\models\search\EventRelatedSearch $modelSearch
 *
 * @package open20\amos\events\controllers\base
 */
class EventRelatedController extends CrudController
{

    /**
     * @var string $layout
     */
    public $layout = 'main';

    public function init()
    {
        $this->setModelObj(new EventRelated());
        $this->setModelSearch(new EventRelatedSearch());

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt') . Html::tag('p', BaseAmosModule::tHtml('amoscore', 'Table')),
                'url' => '?currentView=grid'
            ],
            /*'list' => [
                'name' => 'list',
                'label' => AmosIcons::show('view-list') . Html::tag('p', BaseAmosModule::tHtml('amoscore', 'List')),         
                'url' => '?currentView=list'
            ],
            'icon' => [
                'name' => 'icon',
                'label' => AmosIcons::show('grid') . Html::tag('p', BaseAmosModule::tHtml('amoscore', 'Icons')),           
                'url' => '?currentView=icon'
            ],
            'map' => [
                'name' => 'map',
                'label' => AmosIcons::show('map') . Html::tag('p', BaseAmosModule::tHtml('amoscore', 'Map')),      
                'url' => '?currentView=map'
            ],
            'calendar' => [
                'name' => 'calendar',
                'intestazione' => '', //codice HTML per l'intestazione che verrà caricato prima del calendario,
                                      //per esempio si può inserire una funzione $model->getHtmlIntestazione() creata ad hoc
                'label' => AmosIcons::show('calendar') . Html::tag('p', BaseAmosModule::tHtml('amoscore', 'Calendari')),                                            
                'url' => '?currentView=calendar'
            ],*/
        ]);

        parent::init();
        \Yii::$app->params['bsVersion'] = '4.x';
        \Yii::$app->view->params['customClassMainContent'] = 'box-container sidebar-setting';
        $this->setUpLayout('bootstrap-italia-layout-with-sidebar', []);
    }

    /**
     * Lists all EventRelated models.
     * @return mixed
     */
    public function actionIndex($layout = NULL)
    {
        Url::remember();
        $this->setDataProvider($this->modelSearch->search(Yii::$app->request->getQueryParams()));

        return $this->render(
            'index',
            [
                'dataProvider' => $this->getDataProvider(),
                'model' => $this->getModelSearch(),
                'currentView' => $this->getCurrentView(),
                'availableViews' => $this->getAvailableViews(),
            ]
        );
    }

    /**
     * Displays a single EventRelated model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $this->model = $this->findModel($id);

        if ($this->model->load(Yii::$app->request->post()) && $this->model->save()) {
            return $this->redirect(['view', 'id' => $this->model->id]);
        } else {
            return $this->render('view', ['model' => $this->model]);
        }
    }

    /**
     * Creates a new EventRelated model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($event_id, $eventToAddId = null)
    {
        $event = Event::findOne($event_id);
        if (!$event) {
            throw new NotFoundHttpException('Evento non trovato');
        }
        $this->setMenuSidebar($event);
        $this->model = new EventRelated();

        $modelSearch = new EventSearch();
//        $dataProvider = $modelSearch->searchMyGroup(\Yii::$app->request->get());
        $dataProvider = $modelSearch->searchPublished(\Yii::$app->request->get());
        $dataProvider->query->andWhere(['not in', 'event.id', EventRelated::find()->andWhere(['event_id' => $event_id])->select('related_event_id')]);
        $dataProvider->query->andWhere(['not in', 'event.id', [$event_id]]);

        //aggiunta evento correlato
        if (!empty($eventToAddId)) {
            $eventRelated = EventRelated::find()->andWhere(['event_id' => $event_id, 'related_event_id' => $eventToAddId])->one();

            if (empty($eventRelated)) {
                $lastOrder = EventRelated::getLastOrder($event_id);
                $eventRelated = new EventRelated();
                $eventRelated->event_id = $event_id;
                $eventRelated->related_event_id = $eventToAddId;
                $eventRelated->n_order = $lastOrder + 1;
                $eventRelated->save(false);
                $landing = EventLanding::find()->andWhere(['event_id' => $event_id])->one();
                if ($landing) {
                    EventsUtility::updateLuyaPageTimestampForCache($landing);
                }
                Yii::$app->getSession()->addFlash('success', BaseAmosModule::t('amoscore', 'Item created'));
                return $this->redirect(['/events/event-dashboard/landing-manage-contents', 'id' => $event->id, '#' =>'container-related-events']);
            }
        }

        return $this->render('create', [
            'model' => $this->model,
            'modelSearch' => $modelSearch,
            'event' => $event,
            'fid' => NULL,
            'dataField' => NULL,
            'dataEntity' => NULL,
            'dataProvider' =>  $dataProvider
        ]);
    }

    /**
     * Creates a new EventRelated model by ajax request.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateAjax($fid, $dataField)
    {
        $this->model = new EventRelated();

        if (\Yii::$app->request->isAjax && $this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            if ($this->model->save()) {
//Yii::$app->getSession()->addFlash('success', BaseAmosModule::t('amoscore', 'Item created'));
                return json_encode($this->model->toArray());
            } else {
//Yii::$app->getSession()->addFlash('danger', BaseAmosModule::t('amoscore', 'Item not created, check data'));
            }
        }

        return $this->renderAjax('_formAjax', [
            'model' => $this->model,
            'fid' => $fid,
            'dataField' => $dataField
        ]);
    }

    /**
     * Updates an existing EventRelated model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->setUpLayout('form');
        $this->model = $this->findModel($id);

        if ($this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            if ($this->model->save()) {
                $landing = EventLanding::find()->andWhere(['event_id' => $this->model->event_id])->one();
                if ($landing) {
                    EventsUtility::updateLuyaPageTimestampForCache($landing);
                }
                Yii::$app->getSession()->addFlash('success', BaseAmosModule::t('amoscore', 'Item updated'));
                return $this->redirect(['update', 'id' => $this->model->id]);
            } else {
                Yii::$app->getSession()->addFlash('danger', BaseAmosModule::t('amoscore', 'Item not updated, check data'));
            }
        }

        return $this->render('update', [
            'model' => $this->model,
            'fid' => NULL,
            'dataField' => NULL,
            'dataEntity' => NULL,
        ]);
    }

    /**
     * Deletes an existing EventRelated model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->model = $this->findModel($id);
        $event_id = $this->model->event_id;
        $landing = EventLanding::find()->andWhere(['event_id' => $this->model->event_id])->one();

        if ($this->model) {
            $this->model->delete();

            //resetto la lista dopo aver leiminato un elemento
            $elements = \open20\amos\events\models\base\EventRelated::find()->andWhere(['event_id' => $event_id])->orderBy('n_order ASC')->all();
            foreach ($elements as $element) {
                $orderList [] = $element->id;
            }
            $this->resetNumberOrder($orderList);

            if ($landing) {
                EventsUtility::updateLuyaPageTimestampForCache($landing);
            }

            if (!$this->model->hasErrors()) {
                Yii::$app->getSession()->addFlash('success', BaseAmosModule::t('amoscore', 'Element deleted successfully.'));
            } else {
                Yii::$app->getSession()->addFlash('danger', BaseAmosModule::t('amoscore', 'You are not authorized to delete this element.'));
            }
        } else {
            Yii::$app->getSession()->addFlash('danger', BaseAmosModule::tHtml('amoscore', 'Element not found.'));
        }
        return $this->redirect(['/events/event-dashboard/landing-manage-contents', 'id' => $event_id, '#' =>'container-related-events']);
    }

    /**
     * @param $model
     */
    public function setMenuSidebar($model)
    {
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarEvents($model);
    }
}
