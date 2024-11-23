<?php

namespace open20\amos\events\controllers;

use open20\amos\core\controllers\BackendController;
use open20\amos\events\AmosEvents;
use open20\amos\events\models\EventCommunication;
use open20\amos\events\utility\EventsUtility;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class MarketingAutomationController extends BackendController
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
                            'tag-not-defined',
                            'inactive',
                            'calculate-users-found-ajax',
                            'send-communication',
                            'delete-communication'
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
    }

    public function init()
    {
        parent::init();
        \Yii::$app->params['bsVersion'] = '4.x';
        \Yii::$app->view->params['customClassMainContent'] = 'box-container sidebar-setting';
        $this->setUpLayout('bootstrap-italia-layout-with-sidebar', []);
    }


    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        \Yii::$app->getView()->params['bi-menu-sidebar'] = self::getSidebarPages();
        $dataProvider = new ActiveDataProvider([
            'query' => EventCommunication::find()->andWhere(['event_id' => null])
        ]);

        return $this->render('index', ['dataProviderLists' => $dataProvider]);

    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionTagNotDefined($id = null)
    {
        \Yii::$app->getView()->params['bi-menu-sidebar'] = self::getSidebarPages();
        $eventCommunication = EventCommunication::findOne($id);
        if (empty($eventCommunication)) {
            $eventCommunication = new EventCommunication();
            $eventCommunication->communication_type = EventCommunication::TYPE_MARKET_AUTOMATION_NOTAG;
            $eventCommunication->text_email = $eventCommunication::defaultTextMarketingAutomation(EventCommunication::TYPE_MARKET_AUTOMATION_NOTAG);
            $eventCommunication->subject_email = $eventCommunication::defaultSubjectMarketingAutomation(EventCommunication::TYPE_MARKET_AUTOMATION_NOTAG);
        }

        if (\Yii::$app->request->post() && $eventCommunication->load(\Yii::$app->request->post()) && $eventCommunication->validate()) {

            $query = $eventCommunication->searchUsersMarketingAutomationQuery($eventCommunication->search_inverval);
            $eventCommunication->n_users = $query->count();
            if ($eventCommunication->save()) {
                \Yii::$app->session->addFlash('success', 'Comunicazione salvata correttamente');
                return $this->redirect(['index']);
            }
        }

        $n_users = '' . $eventCommunication->searchUsersMarketingAutomationQuery($eventCommunication->search_inverval)->count();
        $descriptionMarketingAutomation = AmosEvents::t('amosevents', "Questa funzione consente di cercare all'interno dell'archivio degli utenti registrati nella piattaforma tutti gli utenti che non hanno ancora espresso un tag di interesse");
        $title = AmosEvents::t('amosevents', "Tag non definiti");

        return $this->render('form', [
            'eventCommunication' => $eventCommunication,
            'n_users' => $n_users,
            'currentAction' => 'tag-not-defined',
            'descriptionMarketingAutomation' => $descriptionMarketingAutomation,
            'title' => $title,
            'number_communication_type' => EventCommunication::TYPE_MARKET_AUTOMATION_NOTAG


        ]);
    }

    public function actionInactive($id = null)
    {
        \Yii::$app->getView()->params['bi-menu-sidebar'] = self::getSidebarPages();
        $eventCommunication = EventCommunication::findOne($id);
        if (empty($eventCommunication)) {
            $eventCommunication = new EventCommunication();
            $eventCommunication->communication_type = EventCommunication::TYPE_MARKET_AUTOMATION_INACTIVE;
            $eventCommunication->text_email = $eventCommunication::defaultTextMarketingAutomation(EventCommunication::TYPE_MARKET_AUTOMATION_INACTIVE);
            $eventCommunication->subject_email = $eventCommunication::defaultSubjectMarketingAutomation(EventCommunication::TYPE_MARKET_AUTOMATION_INACTIVE);
        }

        if (\Yii::$app->request->post() && $eventCommunication->load(\Yii::$app->request->post()) && $eventCommunication->validate()) {
            $query = $eventCommunication->searchUsersMarketingAutomationQuery($eventCommunication->search_inverval);
            $eventCommunication->n_users = $query->count();
            if ($eventCommunication->save()) {
                \Yii::$app->session->addFlash('success', 'Comunicazione salvata correttamente');
                return $this->redirect(['index']);
            }
        }

        $n_users = '' . $eventCommunication->searchUsersMarketingAutomationQuery($eventCommunication->search_inverval)->count();
        $descriptionMarketingAutomation = AmosEvents::t('amosevents', "Questa funzione consente di cercare all'interno dell'archivio degli utenti registrati nella piattaforma che non hanno effettuato l'accesso nel periodo definito nell'intervallo di ricerca");
        $title = AmosEvents::t('amosevents', "Inattivi");

        return $this->render('form', [
            'eventCommunication' => $eventCommunication,
            'n_users' => $n_users,
            'currentAction' => 'inactive',
            'descriptionMarketingAutomation' => $descriptionMarketingAutomation,
            'title' => $title,
            'number_communication_type' => EventCommunication::TYPE_MARKET_AUTOMATION_INACTIVE
        ]);
    }

    /**
     * @param $idCommunication
     * @return string|Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSendCommunication($id)
    {
        \Yii::$app->getView()->params['bi-menu-sidebar'] = self::getSidebarPages();
        $eventCommunication = EventCommunication::findOne($id);

        if(empty($eventCommunication)){
            throw new NotFoundHttpException('Pagina non trovata');
        }

        $query = $eventCommunication->searchUsersMarketingAutomationQuery($eventCommunication->search_inverval);
        if (\Yii::$app->request->post()) {
            $users = $query->all();
            if ($eventCommunication->communication_type != EventCommunication::TYPE_NOMINAL) {
                $eventCommunication->n_users = count($users);
            }
            $eventCommunication->time_schedule_type = EventCommunication::SENDING_TIME_IMMEDIATE;
            $eventCommunication->status = EventCommunication::STATUS_USERS_TO_IMPORT;
            $eventCommunication->save(false);

            \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', 'Sono state inviate {tot} comunicazioni', ['tot' => $eventCommunication->n_users]));
            return $this->redirect(['/events/marketing-automation/index']);
        }

        $n_users = $query->count();
        return $this->render('communications_send', [
            'n_users' => $n_users,
            'eventCommunication' => $eventCommunication
        ]);
    }


    public static function getSidebarPages()
    {
        $menu = [];

        $menu[] = [
            'mainMenu' => [
                'label' => AmosEvents::t('amosevents', 'Marketing Automation'),
                'icon' => '/sprite/material-sprite.svg#ic_show_chart',
                'activeTargetAction' => 'index',
                'activeTargetController' => 'marketing-automation',
                'titleLink' => AmosEvents::t('amosevents', 'Marketing Automation'),
                'url' => '/events/marketing-automation/index'
            ],
            'dinamicSubMenu' => [
                'menu0' => [
                    'label' => AmosEvents::t('amosevents', 'Lista comunicazioni'),
                    'url' => '/events/marketing-automation/index',
                    'activeTargetAction' => 'index',
                    'activeTargetController' => 'marketing-automation',
                    'titleLink' => AmosEvents::t('amosevents', 'Lista comunicazioni')
                ],
                'menu1' => [
                    'label' => AmosEvents::t('amosevents', 'Tag non definito'),
                    'url' => '/events/marketing-automation/tag-not-defined',
                    'activeTargetAction' => 'tag-not-defined',
                    'activeTargetController' => 'marketing-automation',
                    'titleLink' => AmosEvents::t('amosevents', 'Tag non definito')
                ],
                'menu2' => [
                    'label' => AmosEvents::t('amosevents', 'Inattivi'),
                    'titleLink' => AmosEvents::t('amosevents', 'Inattivi'),
                    'url' => '/events/marketing-automation/inactive',
                    'activeTargetAction' => 'inactive',
                    'activeTargetController' => 'marketing-automation',
                ],
            ],

        ];
        return $menu;

    }

    /**
     * @param $type
     * @param $interval
     * @return bool|int|string|null
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCalculateUsersFoundAjax($type, $interval){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new EventCommunication();
        $model->communication_type = $type;
        $count = $model->searchUsersMarketingAutomationQuery($interval)->count();
        return $count;
    }

    /**
     * @param $idCommunication
     * @return Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteCommunication($id)
    {
        $eventCommunication = EventCommunication::findOne($id);
        $eventCommunication->delete();
        \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', 'Comunicazione eliminata correttamente'));

        return $this->redirect(['/events/marketing-automation/index']);
    }


}