<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events\controllers\base
 */

namespace open20\amos\events\controllers\base;

use open20\amos\events\models\EventLanding;
use open20\amos\events\utility\EventsUtility;
use Yii;
use open20\amos\events\models\EventLandingProtagonist;
use open20\amos\events\models\search\EventLandingProtagonistSearch;
use open20\amos\core\controllers\CrudController;
use open20\amos\core\module\BaseAmosModule;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\helpers\Html;
use open20\amos\core\helpers\T;
use yii\helpers\Url;


/**
 * Class EventLandingProtagonistController
 * EventLandingProtagonistController implements the CRUD actions for EventLandingProtagonist model.
 *
 * @property \open20\amos\events\models\EventLandingProtagonist $model
 * @property \open20\amos\events\models\search\EventLandingProtagonistSearch $modelSearch
 *
 * @package open20\amos\events\controllers\base
 */
class EventLandingProtagonistController extends CrudController
{

    /**
     * @var string $layout
     */
    public $layout = 'main';

    public function init()
    {
        $this->setModelObj(new EventLandingProtagonist());
        $this->setModelSearch(new EventLandingProtagonistSearch());

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
        $this->setUpLayout('bootstrap-italia-layout-with-sidebar');


    }

    /**
     * Lists all EventLandingProtagonist models.
     * @return mixed
     */
    public function actionIndex($layout = NULL)
    {
        Url::remember();
        $this->setDataProvider($this->modelSearch->search(Yii::$app->request->getQueryParams()));
        return parent::actionIndex($layout);
    }

    /**
     * Displays a single EventLandingProtagonist model.
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
     * Creates a new EventLandingProtagonist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($idLanding)
    {
        $this->model = new EventLandingProtagonist();
        $this->model->event_landing_id = $idLanding;
        $landing = EventLanding::findOne($idLanding);
        if(empty($landing)){
            throw new NotFoundHttpException('Pagina non trovata');
        }
        $this->setMenuSidebar($landing->event);

        if ($this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            if ($this->model->save()) {
                EventsUtility::updateLuyaPageTimestampForCache($landing);
                EventsUtility::flushCache();
                Yii::$app->getSession()->addFlash('success', Yii::t('amoscore', 'Item created'));
                return $this->redirect(['/events/event-dashboard/landing-manage-contents', 'id' => $landing->event_id]);
            } else {
                Yii::$app->getSession()->addFlash('danger', Yii::t('amoscore', 'Item not created, check data'));
            }
        }

        return $this->render('create', [
            'model' => $this->model,
            'fid' => NULL,
            'dataField' => NULL,
            'dataEntity' => NULL,
            'event' => $landing->event
        ]);
    }

    /**
     * Creates a new EventLandingProtagonist model by ajax request.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateAjax($fid, $dataField)
    {
        $this->model = new EventLandingProtagonist();

        if (\Yii::$app->request->isAjax && $this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            if ($this->model->save()) {
//Yii::$app->getSession()->addFlash('success', Yii::t('amoscore', 'Item created'));
                return json_encode($this->model->toArray());
            } else {
//Yii::$app->getSession()->addFlash('danger', Yii::t('amoscore', 'Item not created, check data'));
            }
        }

        return $this->renderAjax('_formAjax', [
            'model' => $this->model,
            'fid' => $fid,
            'dataField' => $dataField
        ]);
    }

    /**
     * Updates an existing EventLandingProtagonist model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->model = $this->findModel($id);
        $landing = $this->model->eventLanding;
        $this->setMenuSidebar($landing->event);
        $event = $landing->event;

        if ($this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            if ($this->model->save()) {
                EventsUtility::updateLuyaPageTimestampForCache($landing);
                EventsUtility::flushCache();
                Yii::$app->getSession()->addFlash('success', Yii::t('amoscore', 'Item updated'));
                return $this->redirect(['/events/event-dashboard/landing-manage-contents', 'id' => $landing->event->id]);
            } else {
                Yii::$app->getSession()->addFlash('danger', Yii::t('amoscore', 'Item not updated, check data'));
            }
        }

        return $this->render('update', [
            'model' => $this->model,
            'fid' => NULL,
            'dataField' => NULL,
            'dataEntity' => NULL,
            'event' => $event
        ]);
    }

    /**
     * Deletes an existing EventLandingProtagonist model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->model = $this->findModel($id);
        $event = $this->model->eventLanding->event;
        $landing = $this->model->eventLanding;
        if ($this->model) {
            $this->model->delete();
            if (!$this->model->hasErrors()) {
                EventsUtility::updateLuyaPageTimestampForCache($landing);
                EventsUtility::flushCache();
                Yii::$app->getSession()->addFlash('success', BaseAmosModule::t('amoscore', 'Element deleted successfully.'));
            } else {
                Yii::$app->getSession()->addFlash('danger', BaseAmosModule::t('amoscore', 'You are not authorized to delete this element.'));
            }
        } else {
            Yii::$app->getSession()->addFlash('danger', BaseAmosModule::tHtml('amoscore', 'Element not found.'));
        }
        return $this->redirect(['/events/event-dashboard/landing-manage-contents', 'id' => $event->id]);
    }


    /**
     * @param $model
     */
    public function setMenuSidebar($model)
    {
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarEvents($model);
    }
}
