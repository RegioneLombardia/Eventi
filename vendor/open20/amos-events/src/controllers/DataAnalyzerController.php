<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 02/05/2022
 * Time: 15:29
 */

namespace open20\amos\events\controllers;


use open20\amos\admin\models\UserProfile;
use open20\amos\core\controllers\BackendController;
use open20\amos\core\helpers\Html;
use open20\amos\core\user\User;
use open20\amos\events\AmosEvents;
use open20\amos\events\models\DataAnalyzerForm;
use open20\amos\events\models\EventInactiveUsersMailup;
use open20\amos\events\models\EventMailupErrors;
use open20\amos\events\utility\EventsUtility;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use \Yii;

class DataAnalyzerController extends BackendController
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
                            'index',
                            'analyze-event-types',
                            'analyze-event-dg',
                            'analyze-event-delegations',
                            'analyze-event-preference-tags',
                            'analyze-event-participants',
                            'user-access',
                            'user-access-frontend',
                            'bounce-errors',
                            'inactive-users',
                            'direzioni-generali-users'
                        ],
                        'roles' => ['EVENT_DATA_ANALYZER']
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
        \Yii::$app->params['bsVersion'] = '4.x';
        \Yii::$app->view->params['customClassMainContent'] = 'box-container sidebar-setting';
        $this->setUpLayout('bootstrap-italia-layout-with-sidebar', []);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
        return $this->redirect('analyze-event-type');
    }

    /**
     * @return string
     */
    public function actionAnalyzeEventTypes()
    {

        $titleChart = \Yii::t('amosevents', "Eventi creati");

        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarPages();
        $model = new DataAnalyzerForm();
        $dataProvider = null;
        $statistics = null;
        $tot = null;
        $result = null;

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
//            pr($model->attributes);
            $typeStat = $model->eventTypeStatistics();
            if (!empty($typeStat)) {
                $result = $typeStat['result'];
                $tot = $typeStat['tot'];
            }

            $dataProvider = new ArrayDataProvider([
                'allModels' => $result
            ]);

            $statistics = $model->eventTypeChartStatistics();
        }

        return $this->render('analyze-event-types', [
            'actionType' => 'analyze-event-types',
            'model' => $model,
            'dataProvider' => $dataProvider,
            'statistics' => $statistics,
            'tot' => $tot,
            'titleChart' => $titleChart
        ]);
    }

    /**
     * @return string
     */
    public function actionAnalyzeEventDg()
    {
        $titleChart = \Yii::t('amosevents', "Eventi creati");

        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarPages();
        $model = new DataAnalyzerForm();
        $dataProvider = null;
        $statistics = null;
        $tot = null;
        $result = null;

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $typeStat = $model->eventDgStatistics();
            if (!empty($typeStat)) {
                $result = $typeStat['result'];
                $tot = $typeStat['tot'];
            }

            $dataProvider = new ArrayDataProvider([
                'allModels' => $result
            ]);

            $statistics = $model->eventDgChartStatistics();
        }

        return $this->render('analyze-event-types', [
            'actionType' => 'analyze-event-dg',
            'model' => $model,
            'dataProvider' => $dataProvider,
            'statistics' => $statistics,
            'tot' => $tot,
            'titleChart' => $titleChart

        ]);
    }

    /**
     * @return string
     */
    public function actionAnalyzeEventDelegations()
    {
        $titleChart = \Yii::t('amosevents', "Eventi creati");

        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarPages();
        $model = new DataAnalyzerForm();
        $dataProvider = null;
        $statistics = null;
        $tot = null;
        $result = null;

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $typeStat = $model->eventDelegationsStatistics();
            if (!empty($typeStat)) {
                $result = $typeStat['result'];
                $tot = $typeStat['tot'];
            }

            $dataProvider = new ArrayDataProvider([
                'allModels' => $result
            ]);

            $statistics = $model->eventDelegationsChartStatistics();
        }

        return $this->render('analyze-event-types', [
            'actionType' => 'analyze-event-delegations',
            'model' => $model,
            'dataProvider' => $dataProvider,
            'statistics' => $statistics,
            'tot' => $tot,
            'titleChart' => $titleChart

        ]);
    }

    /**
     * @return string
     */
    public function actionAnalyzeEventPreferenceTags()
    {
        $titleChart = \Yii::t('amosevents', "Eventi creati");


        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarPages();
        $model = new DataAnalyzerForm();
        $dataProvider = null;
        $statistics = null;
        $tot = null;
        $result = null;

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
//            pr($model->attributes);
            $typeStat = $model->eventPreferenceTagsStatistics();
            if (!empty($typeStat)) {
                $result = $typeStat['result'];
                $tot = $typeStat['tot'];
            }

            $dataProvider = new ArrayDataProvider([
                'allModels' => $result
            ]);

            $statistics = $model->eventPreferenceTagsChartStatistics();
        }

        return $this->render('analyze-event-types', [
            'actionType' => 'analyze-event-preference-tags',
            'model' => $model,
            'dataProvider' => $dataProvider,
            'statistics' => $statistics,
            'tot' => $tot,
            'titleChart' => $titleChart

        ]);
    }


    /**
     * @return string
     */
    public function actionAnalyzeEventParticipants()
    {
        $titleChart = \Yii::t('amosevents', "Partecipazione evento");

        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarPages();
        $model = new DataAnalyzerForm();
        $dataProvider = null;
        $statistics = null;
        $tot = null;
        $result = null;
        $resultTotEventi = null;

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $result = $model->eventParticipantstatistics();
            $resultTotEventi = $model->eventTypeParticipantsStatistics();

            $dataProvider = new ArrayDataProvider([
                'allModels' => $result
            ]);

            $statistics = $model->eventParticipantsChartStatistics($result);
        }

        return $this->render('analyze-event-types', [
            'actionType' => 'analyze-event-participants',
            'model' => $model,
            'dataProvider' => $dataProvider,
            'statistics' => $statistics,
            'result' => $result,
            'titleChart' => $titleChart,
            'resultTotEventi' => $resultTotEventi

        ]);
    }


    public function actionUserAccess()
    {
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarPages();
        $model = new DataAnalyzerForm();
        $dataProvider = null;
        $statistics = null;
        $tot = null;
        $result = null;
        $titleChart = \Yii::t('amosevents', "Accessi");

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $statistics = $model->userAccessChartStatistics();
            $typeStat = $model->userAccessStatistics();

            if (!empty($typeStat)) {
                $result = $typeStat['result'];
                $tot = $typeStat['tot'];
            }

            $dataProvider = new ArrayDataProvider([
                'allModels' => $result
            ]);

        }
        return $this->render('analyze-event-types', [
            'actionType' => 'analyze-user-access',
            'model' => $model,
            'dataProvider' => $dataProvider,
            'statistics' => $statistics,
            'result' => $result,
            'tot' => $tot,
            'titleChart' => $titleChart


        ]);
    }


    public function actionUserAccessFrontend()
    {
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarPages();
        $model = new DataAnalyzerForm();
        $dataProvider = null;
        $statistics = null;
        $tot = null;
        $result = null;
        $titleChart = \Yii::t('amosevents', "Accessi");


        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
//            pr($model->attributes);
            $statistics = $model->userAccessChartStatistics();
            $typeStat = $model->userAccessStatistics();
//            pr($typeStat);
            if (!empty($typeStat)) {
                $result = $typeStat['result'];
                $tot = $typeStat['tot'];
            }

            $dataProvider = new ArrayDataProvider([
                'allModels' => $result
            ]);

        }
        return $this->render('analyze-event-types', [
            'actionType' => 'analyze-user-access',
            'model' => $model,
            'dataProvider' => $dataProvider,
            'statistics' => $statistics,
            'result' => $result,
            'tot' => $tot,
            'titleChart' => $titleChart


        ]);
    }


    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionBounceErrors()
    {
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarPages();

        $dataProvider = new ActiveDataProvider([
            'query' => EventMailupErrors::find()->orderBy('id DESC')
        ]);

        $maxError = EventMailupErrors::find()->one();
        return $this->render('bounce-errors', [
            'dataProvider' => $dataProvider,
            'maxError' => $maxError
        ]);
    }


    /**
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionInactiveUsers()
    {
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarPages();
        $firstImport = EventInactiveUsersMailup::find()->one();
        $last_import_date = null;
        if ($firstImport) {
            $last_import_date = $firstImport->created_at;
        }

        $dataProviderSex = DataAnalyzerForm::inactiveUsersSex();
        $dataProviderAge = DataAnalyzerForm::inactiveUsersAge();
        $dataProviderProvincia = DataAnalyzerForm::inactiveUsersProvince();

        if (\Yii::$app->request->post()) {
            EventsUtility::importInactiveUsers();
            $this->redirect('inactive-users');
        }

        return $this->render('inactive-users', [
            'last_import_date' => $last_import_date,
            'dataProviderSex' => $dataProviderSex,
            'dataProviderAge' => $dataProviderAge,
            'dataProviderProvincia' => $dataProviderProvincia,
        ]);
    }

    /**
     * @return string
     */
    public function actionDirezioniGeneraliUsers(){
        $titleChart = AmosEvents::t('amosevents',"Restrazione utenti");
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarPages();
        $model = new DataAnalyzerForm();
        $dataProvider = null;
        $statistics = null;
        $tot = null;
        $result = null;

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $typeStat = $model->registrationUsersStatistics();
            if (!empty($typeStat)) {
                $result = $typeStat['result'];
                $tot = $typeStat['tot'];
            }

            $dataProvider = new ArrayDataProvider([
                'allModels' => $result
            ]);

            $statistics = $model->registrationUserChartStatistics();
        }

        return $this->render('analyze-event-types', [
            'actionType' => 'registration-users',
            'model' => $model,
            'dataProvider' => $dataProvider,
            'statistics' => $statistics,
            'tot' => $tot,
            'titleChart' => $titleChart

        ]);
    }
}