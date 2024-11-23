<?php

/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 11/03/2020
 * Time: 12:19
 */

namespace open20\amos\events\controllers;

use open20\amos\admin\controllers\SecurityController;
use open20\amos\admin\models\UserProfile;
use open20\amos\audit\components\db\ActiveRecord;
use open20\amos\community\AmosCommunity;
use open20\amos\community\models\Community;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\core\controllers\CrudController;
use open20\amos\core\forms\ActiveForm;
use open20\amos\core\helpers\Html;
use open20\amos\core\helpers\StringHelper;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\user\User;
use open20\amos\core\utilities\Email;
use open20\amos\core\validators\CFValidator;
use open20\amos\core\widget\WidgetAbstract;
use open20\amos\cwh\models\CwhRegolePubblicazione;
use open20\amos\dashboard\models\AmosWidgets;
use open20\amos\emailmanager\models\EmailTemplate;
use open20\amos\events\AmosEvents;
use open20\amos\events\assets\EventsAsset;
use open20\amos\events\events\EventChangeAttributesEvent;
use open20\amos\events\models\DuplicateRecursiveForm;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventChangeAttributes;
use open20\amos\events\models\EventCommunication;
use open20\amos\events\models\EventCommunicationSent;
use open20\amos\events\models\EventEmailTemplates;
use open20\amos\events\models\EventHighlights;
use open20\amos\events\models\EventInternalList;
use open20\amos\events\models\EventInvitation;
use open20\amos\events\models\EventInvitationSent;
use open20\amos\events\models\EventInvitationSpecificUsers;
use open20\amos\events\models\EventLanding;
use open20\amos\events\models\EventLocation;
use open20\amos\events\models\EventLocationEntrances;
use open20\amos\events\models\EventParticipantCompanion;
use open20\amos\events\models\EventPushNotification;
use open20\amos\events\models\EventType;
use open20\amos\events\models\EventWebexAccounts;
use open20\amos\events\models\search\EventSearch;
use open20\amos\events\models\search\SpecificUserEventSearch;
use open20\amos\events\models\search\UserEventSearch;
use open20\amos\events\utility\EventMailUtility;
use open20\amos\events\utility\EventsUtility;
use open20\amos\events\utility\OrderElementsUtility;
use open20\amos\news\models\search\NewsSearch;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class EventDashboardController extends CrudController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge([], [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'view',
                            'info-event',
                            'invite',
                            'update-invite',
                            'community',
                            'landing-manage-contents',
                            'landing-settings',
                            'template-emails',
                            'send-email-test',
                            'send-invitation',
                            'delete-internal-list',
                            'summary-sending',
                            'publish',
                            'un-publish',
                            'delete-event',
                            'suspend-event',
                            'export-participants',
                            'communications',
                            'communications-update',
                            'send-communication',
                            'send-communication-test',
                            'delete-communication',
                            'statistics-invitation',
                            'statistics-communication',
                            'stop-sending',
                            'stop-sending-communication',
                            'try-mailup',
                            'try-import',
                            'push-notification-test',
                            'duplicate',
                            'time',
                            'children',
                            'web-meeting-participants',
                            'web-meeting-webex',
                            'new-webmeeting-invitation',
                            'location-entrances',
                            'nominal-invitation',
                        ],
                        'roles' => ['EVENTS_ADMINISTRATOR', 'EVENT_UPDATE', 'SUPER_USER_EVENT']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'manage-events',
                            'index-highlights',
                            'add-to-highlights',
                            'order-highlights',
                            'remove-highlights'
                        ],
                        'roles' => ['SUPER_USER_EVENT', 'EVENT_DG_OPERATOR', 'EVENTS_ADMINISTRATOR']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'manage-all-events',
                        ],
                        'roles' => ['SUPER_USER_EVENT']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'my-invitations',
                            'my-events',
                            'own-interest',
                            'all',
                            'unsubscribe',
                            'confirm-unsubscribe',
                            'get-preview-landing',
                            'join-community',
                            'description-type-event',
                            'my-events',
                            'my-events-home',
                            'my-registrations-home',
                            'my-invitation-home',
                            'time',
                            'join',
                            'my-tickets',
                            'my-tickets-home',
                        ],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'hour-by-date'
                        ]
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

    /**
     * @var string $layout
     */
    public $layout = 'list';
    public
        $moduleCwh,
        $scope;

    /**
     * @var AmosEvents $eventsModule
     */
    public $eventsModule = null;

    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->eventsModule = AmosEvents::instance();

        $this->setModelObj($this->eventsModule->createModel('Event'));
        $this->setModelSearch($this->eventsModule->createModel('EventSearch'));

//        EventsAsset::register(\Yii::$app->view);

        $this->scope = null;
        $this->moduleCwh = \Yii::$app->getModule('cwh');

        if (!empty($this->moduleCwh)) {
            $this->scope = $this->moduleCwh->getCwhScope();
        }

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosEvents::t('amosevents', '{tableIcon}' . Html::tag('p', AmosEvents::t('amosevents', 'Table')), [
                    'tableIcon' => AmosIcons::show('view-list-alt')
                ]),
                'url' => '?currentView=grid'
            ],
            'list' => [
                'name' => 'list',
                'label' => AmosEvents::t('amosevents', '{iconaLista}' . Html::tag('p', 'Lista'), [
                    'iconaLista' => AmosIcons::show('view-list')
                ]),
                'url' => '?currentView=list'
            ], /**
             * 'icon' => [
             * 'name' => 'icon',
             * 'label' => AmosEvents::t('amosevents', '{iconaElenco}'.Html::tag('p','Icone'), [
             * 'iconaElenco' => AmosIcons::show('grid')
             * ]),
             * 'url' => '?currentView=icon'
             * ],
             * 'map' => [
             * 'name' => 'map',
             * 'label' => AmosEvents::t('amosevents', '{iconaMappa}'.Html::tag('p','Mappa'), [
             * 'iconaMappa' => AmosIcons::show('map')
             * ]),
             * 'url' => '?currentView=map'
             * ], */
//            'calendar' => [
//                'name' => 'calendar',
//                'intestazione' => '', //codice HTML per l'intestazione che verrà caricato prima del calendario,
//                //per esempio si può inserire una funzione $model->getHtmlIntestazione() creata ad hoc
//                'label' => AmosEvents::t('amosevents', '{calendarIcon}' . Html::tag('p', AmosEvents::t('amosevents', 'Calendar')), [
//                    'calendarIcon' => AmosIcons::show('calendar')
//                ]),
//                'url' => '?currentView=calendar'
//            ],
        ]);

        $language = \Yii::$app->request->get('language');
        if (!empty($language)) {
            \Yii::$app->language = $language;
        }
        \Yii::$app->params['disableAfterFindPurify'] = true;
        parent::init();


        $isEventDashboard = strpos(\Yii::$app->request->absoluteUrl, '/events/') !== false;
        if (
            $isEventDashboard || (!empty(\Yii::$app->controller->module->id) && \Yii::$app->controller->module->id == 'events')
        ) {
            \Yii::$app->params['bsVersion'] = '4.x';
            \Yii::$app->view->params['customClassMainContent'] = 'box-container sidebar-setting';
            $this->setUpLayout('bootstrap-italia-layout-with-sidebar', []);
        }
    }

    /**
     * @param null $layout
     * @return string|\yii\web\Response
     */
    public function actionIndex($layout = null)
    {
        return $this->redirect('index');
    }

    /**
     *
     */
    public function actionView($id)
    {
        $this->model = $this->findModel($id);
        $this->setMenuSidebar($this->model);
        EventsUtility::resetScope();
        return $this->render('view', ['model' => $this->model]);
    }

    /**
     * @return string
     */
    public function actionManageEvents()
    {
        $dataProvider = $this->modelSearch->searchMyGroup(\Yii::$app->request->get());
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['enableChangeView'] = true;
        \Yii::$app->view->params['createNewBtnParams'] = ['urlCreateNew' => ['/events/wizard']];

        $title = AmosEvents::t('amosevents', 'Gestisci i miei eventi');
        return $this->render('manage-events', [
            'model' => $this->modelSearch,
            'dataProvider' => $dataProvider,
            'currentView' => $this->getCurrentView(),
            'title' => $title
        ]);
    }

    /**
     * @return string
     */
    public function actionManageAllEvents()
    {
        $dataProvider = $this->modelSearch->searchAllAdmin(\Yii::$app->request->get());
        $title = AmosEvents::t('amosevents', 'Gestisci tutti gli eventi');

        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['enableChangeView'] = true;
        \Yii::$app->view->params['createNewBtnParams'] = ['urlCreateNew' => ['/events/wizard']];
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarManageEvents();

        return $this->render('manage-events', [
            'model' => $this->modelSearch,
            'dataProvider' => $dataProvider,
            'currentView' => $this->getCurrentView(),
            'title' => $title
        ]);
    }

    /**
     * @return string
     */
    public function actionMyEvents()
    {

        \Yii::$app->view->params['customClassMainContent'] = 'box-container';
        $dataProvider = $this->modelSearch->searchMyRegistrations(\Yii::$app->request->get());
        \Yii::$app->view->params['createNewBtnParams'] = ['layout' => ''];
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['enableChangeView'] = true;

        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarEventsUser();

        $title = AmosEvents::t('amosevents', 'Eventi a cui sono iscritto');
        return $this->render('index-events', [
            'model' => $this->modelSearch,
            'dataProvider' => $dataProvider,
            'currentView' => $this->getCurrentView(),
            'title' => $title
        ]);
    }

    /**
     * @return string
     */
    public function actionMyRegistrationsHome()
    {
        \Yii::$app->view->params['customClassMainContent'] = 'box-container';
//        $this->setUpLayout('bootstrap-italia-layout-no-sidebar');
        $this->setUpLayout('@backend/views/layouts/landing-cms');

        $dataProvider = $this->modelSearch->searchMyRegistrations(\Yii::$app->request->get());
        \Yii::$app->view->params['createNewBtnParams'] = ['layout' => ''];
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['enableChangeView'] = true;

        $title = AmosEvents::t('amosevents', 'Eventi a cui sono iscritto');
        return $this->render('index-events', [
            'model' => $this->modelSearch,
            'dataProvider' => $dataProvider,
            'currentView' => $this->getCurrentView(),
            'title' => $title
        ]);
    }

    /**
     * @return string
     */
    public function actionMyInvitationHome()
    {
        \Yii::$app->view->params['customClassMainContent'] = 'box-container';
//        $this->setUpLayout('bootstrap-italia-layout-no-sidebar');
        $this->setUpLayout('@backend/views/layouts/landing-cms');

        $dataProvider = $this->modelSearch->searchMyInvitations(\Yii::$app->request->get());
        \Yii::$app->view->params['createNewBtnParams'] = ['layout' => ''];
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['enableChangeView'] = true;

        $title = AmosEvents::t('amosevents', 'Eventi in attesa di risposta');
        return $this->render('index-events', [
            'model' => $this->modelSearch,
            'dataProvider' => $dataProvider,
            'currentView' => $this->getCurrentView(),
            'title' => $title
        ]);
    }

    /**
     * @return string
     */
    public function actionMyTicketsHome()
    {
        \Yii::$app->view->params['customClassMainContent'] = 'box-container';
//        $this->setUpLayout('bootstrap-italia-layout-no-sidebar');
        $this->setUpLayout('@backend/views/layouts/landing-cms');

        $dataProvider = $this->modelSearch->searchMyTickets(\Yii::$app->request->get());
        \Yii::$app->view->params['createNewBtnParams'] = ['layout' => ''];
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['enableChangeView'] = true;

        $title = AmosEvents::t('amosevents', 'Eventi con biglietto a cui sono iscritto');
        return $this->render('index-events', [
            'model' => $this->modelSearch,
            'dataProvider' => $dataProvider,
            'currentView' => $this->getCurrentView(),
            'title' => $title
        ]);
    }

    /**
     * @return string
     */
    public function actionMyEventsHome()
    {
        EventsUtility::resetScope();

        \Yii::$app->view->params['customClassMainContent'] = 'box-container';
//        $this->setUpLayout('bootstrap-italia-layout-no-sidebar');
        $this->setUpLayout('@backend/views/layouts/landing-cms');

        $dataProviderReg = $this->modelSearch->searchMyRegistrations(\Yii::$app->request->get());
        $dataProviderReg->query->limit(11);

        $dataProviderInvite = $this->modelSearch->searchMyInvitations(\Yii::$app->request->get());
        $dataProviderInvite->query->limit(11);

        $dataProviderOwnInterest = $this->modelSearch->searchCalendarView(\Yii::$app->request->get());
        $dataProviderOwnInterest->query->limit(11);

        $dataProviderTickets = $this->modelSearch->searchMyTickets(\Yii::$app->request->get());
        $dataProviderTickets->query->limit(11);

        \Yii::$app->view->params['createNewBtnParams'] = ['layout' => ''];
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['enableChangeView'] = true;

        $title = AmosEvents::t('amosevents', 'Eventi a cui sono iscritto');
        return $this->render('my-events-home', [
            'model' => $this->modelSearch,
            'dataProviderReg' => $dataProviderReg,
            'dataProviderInvite' => $dataProviderInvite,
            'dataProviderOwnInterest' => $dataProviderOwnInterest,
            'dataProviderTickets' => $dataProviderTickets,
            'title' => $title
        ]);
    }

    /**
     * @return string
     */
    public function actionMyInvitations()
    {
        \Yii::$app->view->params['customClassMainContent'] = 'box-container';
        $dataProvider = $this->modelSearch->searchMyInvitations(\Yii::$app->request->get());
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['createNewBtnParams'] = ['layout' => ''];
        \Yii::$app->view->params['enableChangeView'] = true;

        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarEventsUser();
        $title = AmosEvents::t('amosevents', 'Eventi a cui sono stato invitato');

        return $this->render('index-events', [
            'model' => $this->modelSearch,
            'dataProvider' => $dataProvider,
            'currentView' => $this->getCurrentView(),
            'title' => $title
        ]);
    }

    /**
     * @return string
     */
    public function actionOwnInterest()
    {
        \Yii::$app->view->params['customClassMainContent'] = 'box-container';
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['createNewBtnParams'] = ['layout' => ''];
        \Yii::$app->view->params['enableChangeView'] = true;

        $dataProvider = $this->modelSearch->searchCalendarView(\Yii::$app->request->get());
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarEventsUser();
        $title = AmosEvents::t('amosevents', 'Eventi di mio interesse');

        $currentView = \Yii::$app->request->get('currentView');
        if (empty($currentView)) {
            $this->setCurrentView([
                'name' => 'list',
                'label' => AmosEvents::t('amosevents', '{iconaLista}' . Html::tag('p', 'Lista'), [
                    'iconaLista' => AmosIcons::show('view-list')
                ]),
                'url' => '?currentView=list'
            ]);
        }

        return $this->render('index-events', [
            'model' => $this->modelSearch,
            'dataProvider' => $dataProvider,
            'currentView' => $this->getCurrentView(),
            'title' => $title
        ]);
    }

    /**
     * @return string
     */
    public function actionAll()
    {
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['createNewBtnParams'] = ['layout' => ''];
        \Yii::$app->view->params['enableChangeView'] = true;

        $dataProvider = $this->modelSearch->searchAll(\Yii::$app->request->get());
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarEventsUser();
        $title = AmosEvents::t('amosevents', 'Tutti');

        $currentView = \Yii::$app->request->get('currentView');
        if (empty($currentView)) {
            $this->setCurrentView([
                'name' => 'list',
                'label' => AmosEvents::t('amosevents', '{iconaLista}' . Html::tag('p', 'Lista'), [
                    'iconaLista' => AmosIcons::show('view-list')
                ]),
                'url' => '?currentView=list'
            ]);
        }

        return $this->render('index-events', [
            'model' => $this->modelSearch,
            'dataProvider' => $dataProvider,
            'currentView' => $this->getCurrentView(),
            'title' => $title
        ]);
    }

    /**
     * @return string
     */
    public function actionMyTickets()
    {
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['createNewBtnParams'] = ['layout' => ''];
        \Yii::$app->view->params['enableChangeView'] = true;

        $dataProvider = $this->modelSearch->searchMyTickets(\Yii::$app->request->get());
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarEventsUser();
        $title = AmosEvents::t('amosevents', 'Eventi con biglietto a cui sono scritto');


        return $this->render('index-events', [
            'model' => $this->modelSearch,
            'dataProvider' => $dataProvider,
            'currentView' => $this->getCurrentView(),
            'title' => $title
        ]);
    }

    /**
     * @return string
     */
    public function actionIndexHighlights()
    {
        \Yii::$app->view->params['customClassMainContent'] = 'box-container';
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['createNewBtnParams'] = [
            'urlCreateNew' => [
                '/events/event-dashboard/add-to-highlights'],
            'createNewBtnLabel' => AmosEvents::t('amosevents', "Aggiungi evento")
        ];
        \Yii::$app->view->params['enableChangeView'] = false;
        \Yii::$app->controller->module->params['searchParams'] = null;

        if (EventHighlights::find()->count() >= 11) {
            \Yii::$app->view->params['createNewBtnParams'] = ['layout' => ''];
        }


        $dataProvider = $this->modelSearch->searchHighlights();
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarManageEvents();
        $title = AmosEvents::t('amosevents', 'In evidenza');

        return $this->render('index-highlights', [
            'model' => $this->modelSearch,
            'dataProvider' => $dataProvider,
            'currentView' => $this->getCurrentView(),
            'title' => $title
        ]);
    }

    //------------------------------------------

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionInfoEvent($id)
    {
        $this->model = $this->findModel($id);
        $this->model->enable_cache = $this->model->getIsCacheableNavItem();

        $this->setMenuSidebar($this->model);

        $this->model->loadTagPreferences();
        $this->model->loadCustomTags();
        $preferenceCenterTags = EventsUtility::getPreferenceCenterTags();

        $locations = EventLocation::find()->andWhere(['OR',
            ['hidden' => false],
            ['hidden' => true, 'id' => $this->model->event_location_id]
        ])->all();
        $locationsForRadio = [];
        foreach ($locations as $location) {
            $locationsForRadio[$location->id] = $location->name;
        }
        $locationsForRadio['other'] = AmosEvents::t('amosevents', 'Other');
        $eventLocation = new EventLocation();
        $eventLocationEntrance = new EventLocationEntrances();

        if (empty($this->model->begin_date_hour)) {
            $this->model->beginDate = date("Y-m-d");
            $this->model->beginHour = date("H:i:s");
        } else {
            $beginDate = new \DateTime($this->model->begin_date_hour);
            $this->model->beginDate = $beginDate->format("Y-m-d");
            $this->model->beginHour = $beginDate->format("H:i:s");
        }

        if (empty($this->model->end_date_hour)) {
            $this->model->endDate = date("Y-m-d");
            $this->model->endHour = date("H:i:s", strtotime('3 hour'));
        } else {
            $endDate = new \DateTime($this->model->end_date_hour);
            $this->model->endDate = $endDate->format("Y-m-d");
            $this->model->endHour = $endDate->format("H:i:s");
        }

        $eventLanding = $this->model->eventLanding;
        $templates = EventsUtility::getCmsTemplates();

        $arrayTemplates = [];
        $htmlTemplate = [];

        foreach ($templates as $template) {
            if ($template['title'] != 'neutro') {
                $arrayTemplates [$template['id']] = $template['title'];
                $htmlTemplate[$template['id']] = EventsUtility::getClassPreviewLanding($template['title']);
            }
        }
        if (empty($eventLanding)) {
            $eventLanding = new EventLanding();
            $eventLanding->event_id;
        }


//        $this->setUpLayout('form');
        $post = \Yii::$app->request->post();
        if ($post && $this->model->load($post)/** && $eventLanding->load($post) && $eventLanding->validate()* */) {
//            $eventLanding->event_id = $this->model->id;
//            $eventLanding->save(false);

            if (!empty($this->model->beginDate)) {
                $this->model->begin_date_hour = $this->model->beginDate . ' ' . $this->model->beginHour;
            }
            if (!empty($this->model->endDate)) {
                $this->model->end_date_hour = $this->model->endDate . ' ' . $this->model->endHour;
            }

            if ($this->model->validate() && $this->model->checkLocationRequired()) {
                $isLocationValid = true;
                $locationStreaming = \open20\amos\events\models\EventLocation::find()->andWhere(['is_location_streaming' => true])->one();

                if ($this->model->event_location_id == 'other') {
                    $isLocationValid = false;
                    $eventLocation->load(\Yii::$app->request->post());
                    $eventLocationEntrance->load(\Yii::$app->request->post());

                    if ($eventLocation->validate() && $eventLocationEntrance->validate()) {
                        $isLocationValid = true;
                        $eventLocation->hidden = true;
                        $eventLocation->save(false);

                        $eventLocationEntrance->event_location_id = $eventLocation->id;
                        $eventLocationEntrance->save(false);
                        $this->model->event_location_id = $eventLocation->id;
                        $this->model->event_location_entrance_id = $eventLocationEntrance->id;
                    }
                } else if ($this->model->event_location_id == $locationStreaming->id) {
                    $this->model->event_location_entrance_id = null;
                }

                if ($isLocationValid && $this->model->save()) {
                    \Yii::$app->session->addFlash('success', \Yii::t('amoscore', "Elemento salvato correttamente."));
                    $this->model->savePreferencesTags();
                    $this->model->saveCustomTags();
                    $this->model->createDefaultAccreditationList();
                    return $this->redirect(['info-event', 'id' => $id]);
                }
            }
        }

        return $this->render('info-event', [
            'model' => $this->model,
            'moduleEvents' => $this->eventsModule,
            'preferenceCenterTags' => $preferenceCenterTags,
            'locations' => $locations,
            'locationsForRadio' => $locationsForRadio,
            'eventLocation' => $eventLocation,
            'eventLocationEntrance' => $eventLocationEntrance,
            'arrayTemplates' => $arrayTemplates,
            'eventLanding' => $eventLanding,
            'htmlTemplate' => $htmlTemplate
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionInvite($id)
    {
        Url::remember();
        $internalList = new \open20\amos\events\models\EventInternalList();
        $modelSearch = new UserEventSearch();

        $this->model = $this->findModel($id);
        EventsUtility::throwAccessDeniedSuspended($this->model);

        $this->model->detachBehavior('seoBehavior');

        $this->setMenuSidebar($this->model);

        $dataProviderLists = new ActiveDataProvider([
            'query' => $this->model->getEventInternalLists()
        ]);

//        if(!empty($internalList)) {
//            parse_str(($internalList->search_params), $query_params);
////            pr($query_params);
//            $modelSearch->load($query_params);
////            pr($modelSearch->attributes);
//        } else {
        $modelSearch->event_id = $id;
        $modelSearch->type = UserEventSearch::SEARCH_TYPE_ALL;
//        }

        $this->model->setScenario(Event::SCENARIO_WIZARD_INVITE);

        $post = \Yii::$app->request->post();
        if ($post && $internalList->load($post) && $internalList->validate()) {
            $internalList->search_params = $post['EventInternalList']['search_params'];
            if ($internalList->save(false)) {
                return $this->redirect(['invite', 'id' => $id]);
            }
        }

        return $this->render('invite', [
            'model' => $this->model,
            'moduleEvents' => $this->eventsModule,
            'modelSearch' => $modelSearch,
            'internalList' => $internalList,
            'dataProviderLists' => $dataProviderLists
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdateInvite($eid, $idList)
    {
        $this->model = $this->findModel($eid);
        $this->model->detachBehavior('seoBehavior');

        $this->setMenuSidebar($this->model);
        $internalList = \open20\amos\events\models\EventInternalList::findOne($idList);
        $internalList->event_id = $eid;

        if (!$internalList) {
            throw new NotFoundHttpException('La pagina non esiste');
        }
        $modelSearch = new UserEventSearch();

        if (!empty($internalList)) {
            parse_str(($internalList->search_params), $query_params);
//            pr($query_params);
            $modelSearch->load($query_params);
//            pr($modelSearch->attributes);
        } else {
            $modelSearch->event_id = $eid;
            $modelSearch->type = UserEventSearch::SEARCH_TYPE_ALL;
        }

        $this->model->setScenario(Event::SCENARIO_WIZARD_INVITE);

        $post = \Yii::$app->request->post();
        if ($post && $internalList->load($post) && $internalList->validate()) {
            $internalList->search_params = $post['EventInternalList']['search_params'];
            if ($internalList->save(false)) {
                return $this->redirect(['update-invite', 'idList' => $idList, 'eid' => $eid]);
            }
        }
        return $this->render('update_invite', [
            'model' => $this->model,
            'moduleEvents' => $this->eventsModule,
            'modelSearch' => $modelSearch,
            'internalList' => $internalList,
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCommunity($id)
    {
        Url::remember();

        $this->model = $this->findModel($id);
        $this->model->detachBehavior('seoBehavior');

        $this->setMenuSidebar($this->model);
//        $this->model->setScenario(Event::SCENARIO_WIZARD_COMMUNITY);

        $defaultWidgets = AmosWidgets::find()
            ->andWhere(['classname' => [
                'open20\amos\news\widgets\icons\WidgetIconNewsDashboard',
                'open20\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard',
                'open20\amos\documenti\widgets\graphics\WidgetGraphicsUltimiDocumenti',
                'open20\amos\news\widgets\graphics\WidgetGraphicsUltimeNews'
            ]])
            ->andWhere(['module' => 'community'])
            ->all();

        $isCreate = false;
        if (is_null($this->model->community_id)) {
            $managerStatus = CommunityUserMm::STATUS_ACTIVE; //$this->getManagerStatus($model, $oldAttributes);
            $ok = EventsUtility::createCommunity($this->model, $managerStatus);
            $isCreate = true;
        } else {
            $ok = EventsUtility::updateCommunity($this->model);
        }

        $community = Community::find()->andWhere(['id' => $this->model->community_id])->one();
        if ($isCreate) {
            $community->force_workflow = 0;
        } else {
            $community->force_workflow = $community->force_workflow;
        }

//        $this->model->setScenario(Event::SCENARIO_DESCRIPTION);

        if (\Yii::$app->getRequest()->post() && $this->model->load(\Yii::$app->getRequest()->post())) {
            $this->model->show_community = \Yii::$app->request->post()['Event']['show_community'];
            $community->force_workflow = \Yii::$app->request->post()['Community']['force_workflow'];
            $community->save(false);

            if ($this->model->save(false)) {
                \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', "Elemento salvato correttamente"));
                $community->saveDashboardCommunity();
            }
            return $this->redirect(['community', 'id' => $id]);
        }

        return $this->render('community', [
            'model' => $this->model,
            'moduleEvents' => $this->eventsModule,
            'community' => $community,
            'defaultWidgets' => $defaultWidgets
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionLandingSettings($id)
    {
        $this->model = $this->findModel($id);
        EventsUtility::throwAccessDeniedSuspended($this->model);
        $this->model->detachBehavior('seoBehavior');

        $this->setMenuSidebar($this->model);

        $eventLanding = $this->model->eventLanding;
        $this->model->setScenario(Event::SCENARIO_WIZARD_LANDING);

        $utility = new \amos\cmsbridge\utility\CmsUtility();
        $templates = $utility->getCmsTemplates();
        $arrayTemplates = [];
        $htmlTemplate = [];
        foreach ($templates as $template) {
            if ($template['title'] != 'Neutro' || \Yii::$app->user->can('SUPER_USER_EVENT')) {
                $arrayTemplates [$template['id']] = $template['title'];
                $htmlTemplate[$template['id']] = EventsUtility::getClassPreviewLanding($template['title']);
            }
        }
        if (empty($eventLanding)) {
            $eventLanding = new EventLanding();
            $eventLanding->event_id;
        }

        $post = \Yii::$app->getRequest()->post();
        if ($post && $eventLanding->load($post) && $eventLanding->validate()) {
            $eventLanding->event_id = $id;
            $eventLanding->save(false);
            //$isOk = $eventLanding->createLandingPage($this->model);
            $this->model->callLyua = true;
            if ($this->model->save()) {
                \Yii::$app->session->addFlash('success', \Yii::t('amoscore', "Elemento salvato correttamente."));
                return $this->redirect(['landing-settings', 'id' => $id]);
            }
        }
        return $this->render('landing-settings', [
            'model' => $this->model,
            'moduleEvents' => $this->eventsModule,
            'arrayTemplates' => $arrayTemplates,
            'eventLanding' => $eventLanding,
            'htmlTemplate' => $htmlTemplate
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     *
     */
    public function actionLandingManageContents($id, $urlRedirect = null)
    {
        $this->model = $this->findModel($id);
        EventsUtility::throwAccessDeniedSuspended($this->model);

        $this->model->detachBehavior('seoBehavior');

        $this->setMenuSidebar($this->model);
        $this->setScope($this->model->community_id);

        $dataProviderProtagonists = null;
        $seoData = $this->model->getContentSeoData();

        $eventLanding = $this->model->eventLanding;
        if (empty($eventLanding)) {
            $eventLanding = new EventLanding();
            $eventLanding->event_id = $id;
        }
        $eventLanding->setScenario(EventLanding::SCENARIO_MANAGE_CONTENT);
        $dataProviderProtagonists = new ActiveDataProvider([
            'query' => $eventLanding->getEventLandingProtagonists()
        ]);

        $dataProviderNews = new ActiveDataProvider([
            'query' => $eventLanding->getNews()
        ]);

        $dataProviderDocs = new ActiveDataProvider([
            'query' => $eventLanding->getDocuments()
        ]);

        $dataProviderRelatedEvents = new ActiveDataProvider([
            'query' => $this->model->getEventRelateds()->orderBy('n_order ASC')

        ]);

        $slider = $this->model->eventLanding->imageSlider;
        if (empty($slider)) {
            $slider = new \amos\sitemanagement\models\SiteManagementSlider();
            $slider->section_id = null;
            $slider->title = $eventLanding->event->getTitle();
            $slider->save(false);
            $eventLanding->image_slider_id = $slider->id;
            $eventLanding->save(false);
        }

        $sliderVideo = $this->model->eventLanding->videoSlider;
        if (empty($sliderVideo)) {
            $sliderVideo = new \amos\sitemanagement\models\SiteManagementSlider();
            $sliderVideo->section_id = null;
            $sliderVideo->title = $eventLanding->event->getTitle();
            $sliderVideo->save(false);
            $eventLanding->video_slider_id = $sliderVideo->id;
            $eventLanding->save(false);
        }

        $sliderInstagramVideo = $this->model->eventLanding->instagramVideoSlider;
        if (empty($sliderInstagramVideo)) {
            $sliderInstagramVideo = new \amos\sitemanagement\models\SiteManagementSlider();
            $sliderInstagramVideo->section_id = null;
            $sliderInstagramVideo->title = $eventLanding->event->getTitle();
            $sliderInstagramVideo->save(false);
            $eventLanding->instagram_video_slider_id = $sliderInstagramVideo->id;
            $eventLanding->save(false);
        }

        $dataProviderSliderElemImage = new ActiveDataProvider(['query' => $slider->getSliderElems()->orderBy('order ASC')]);
        $dataProviderSliderElemVideo = new ActiveDataProvider(['query' => $sliderVideo->getSliderElems()->orderBy('order ASC')]);
        $dataProviderSliderElemInstagram = new ActiveDataProvider(['query' => $sliderInstagramVideo->getSliderElems()->orderBy('order ASC')]);

        if (\Yii::$app->request->post() && $eventLanding->load(\Yii::$app->request->post())) {
            if ($eventLanding->save()) {


                if ($seoData->load(\Yii::$app->request->post())) {
                    $seoData->save(false);
                }
                \Yii::$app->session->addFlash('success', \Yii::t('amoscore', "Elemento salvato correttamente."));
                $this->model->save(false);
                if (\Yii::$app->request->post('save_and_redirect_to')) {
                    return $this->redirect(\Yii::$app->request->post('save_and_redirect_to'));
                }

                return $this->redirect(['landing-manage-contents', 'id' => $this->model->id]);
            }
        }

        return $this->render('landing-manage-contents', [
            'model' => $this->model,
            'eventLanding' => $eventLanding,
            'dataProviderProtagonists' => $dataProviderProtagonists,
            'dataProviderSliderElemImage' => $dataProviderSliderElemImage,
            'dataProviderSliderElemVideo' => $dataProviderSliderElemVideo,
            'dataProviderSliderElemInstagram' => $dataProviderSliderElemInstagram,
            'dataProviderNews' => $dataProviderNews,
            'dataProviderDocs' => $dataProviderDocs,
            'dataProviderRelatedEvents' => $dataProviderRelatedEvents
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     *
     */
    public function actionTemplateEmails($id)
    {
        $this->model = $this->findModel($id);
        EventsUtility::throwAccessDeniedSuspended($this->model);

        $this->model->detachBehavior('seoBehavior');

        $this->setMenuSidebar($this->model);
        $emailTemplates = $this->model->eventEmailTemplates;
        if (empty($emailTemplates)) {
            $emailTemplates = new EventEmailTemplates();
            $emailTemplates->event_id = $id;
            // salvo i default all'atterraggio sulla pagina
            if ($emailTemplates->save(false)) {
                $emailTemplates->saveDefaultTextEng();
            }
        }
        //  quando si fa il validate() viene fatto l'escape di [URL_LANDING} , quindi faccio il validate della copia e salvo l'originale
        $emailTemplatesClone = clone $emailTemplates;
        if ($emailTemplates->load(\Yii::$app->request->post()) && $emailTemplatesClone->load(\Yii::$app->request->post()) && $emailTemplatesClone->validate()) {
            $emailTemplates->event_id = $id;
            if ($emailTemplates->save(false)) {
                return $this->redirect(['template-emails', 'id' => $id]);
            }
        }

        return $this->render('emails', ['model' => $this->model, 'emailTemplates' => $emailTemplates]);
    }

    /**
     * @param $model
     */
    public function setMenuSidebar($model)
    {
        \Yii::$app->getView()->params['bi-menu-sidebar'] = EventsUtility::getSidebarEvents($model);
    }

    /**
     * @param $id
     */
    public function setScope($id)
    {
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh)) {
            $moduleCwh->setCwhScopeInSession([
                'community' => $id,
            ],
                [
                    'mm_name' => 'community_user_mm',
                    'entity_id_field' => 'community_id',
                    'entity_id' => $id
                ]);
        }
    }

    /**
     * @param $attr_text
     * @param $attr_subject
     * @param $field_email
     * @return mixed|string
     */
    public function actionSendEmailTest($attr_text, $attr_subject, $field_email, $eid)
    {
        $this->model = $this->findModel($eid);
        $post = \Yii::$app->request->post();
        $ok = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $templateEmail = new EventEmailTemplates();
        if ($templateEmail->load($post)) {
            $attrText = $attr_text;
            $attrSubject = $attr_subject;
            $attrField = $field_email;
            $from = \Yii::$app->params['email-assistenza'];;
//            $to = [$templateEmail->$attrField];
            $to = $templateEmail->$attrField;
            $subject = $templateEmail->$attrSubject;
            $message = $templateEmail->$attrText;
            $footerType = $templateEmail->getFooterText()[$attrText];

            $subjectWithParams = EventMailUtility::parseEmailWithParams($this->model, \Yii::$app->user->id, $subject, false);
            $textWithParms = EventMailUtility::parseEmailWithParams($this->model, \Yii::$app->user->id, $message);

            if ($this->model->multilanguage) {
                if (\Yii::$app->language == 'it-IT') {
                    \Yii::$app->language = 'en-GB';
                    $templateEmailEng = EventEmailTemplates::find()->andWhere(['event_id' => $eid])->one();
                    $messageEng = $templateEmailEng->$attrText;
                    $textWithParmsEng = EventMailUtility::parseEmailWithParams($this->model, \Yii::$app->user->id, $messageEng);
                    $textWithParms .= "<hr><br>" . $textWithParmsEng;
                    \Yii::$app->language = 'it-IT';
                }
            }

            $newsletterModule = \Yii::$app->getModule('newsletter');
//            if ($newsletterModule) {
//                $mailServiceClassname = $newsletterModule->mail_service_driver;
//                $mailService = new $mailServiceClassname();
//                $user = User::findOne(\Yii::$app->user->id);
//                $decode = $mailService->sendTransationalEmail(2, $subjectWithParams, $textWithParms, ['user' => $user->userProfile->nomeCognome, 'email' => $to]);
//                $ok = true;
//            }
            $ok = EventMailUtility::sendEmailTest($from, $to, $subjectWithParams, $textWithParms, $this->model, $footerType);
//            $ok = EventMailUtility::sendEmailGeneral($from, $to, $subjectWithParams, $textWithParms);
        }
        if ($ok) {
            return $templateEmail->$attrField;
        } else
            return 'Nessuno';
    }

    /**
     * @param $idInternalList
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSummarySending($idInternalList, $errors = null, $post = null)
    {
        $internalList = EventInternalList::findOne($idInternalList);
        $this->model = $this->findModel($internalList->event_id);
        $this->setMenuSidebar($this->model);

        $query = $internalList->query_sql;
        $invitationSent = new EventInvitationSent();

        $result = \Yii::$app->db->createCommand($query)->queryAll();
        $count = count($result);
        if (empty($internalList)) {
            throw new NotFoundHttpException('Pagina non trovata');
        }
        if (!empty($errors)) {
            $errorMessages = json_decode($errors);
            foreach ($errorMessages as $attribute => $message) {
                $internalList->addError($attribute, implode('<br>', $message));
            }
            if (!empty($post)) {
                $post = json_decode($post);
                if (!empty($post->EventInternalList)) {
                    $internalList->time_schedule_date = $post->EventInternalList->time_schedule_date;
                    $internalList->time_schedule_type = $post->EventInternalList->time_schedule_type;
                }
            }
        }
        return $this->render('summary_sending', [
                'internalList' => $internalList,
                'count' => $count,
                'invitationSent' => $invitationSent,
                'event' => $this->model,
                'post' => $post
            ]
        );
    }

    /**
     * @param $idInternalList
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     * @deprecated
     */
    public function actionSendInvitationOld($idInternalList)
    {
        $internalList = EventInternalList::findOne($idInternalList);
        if (empty($internalList)) {
            throw new NotFoundHttpException('Pagina non trovata');
        }

        $event = $internalList->event;
        if (\Yii::$app->request->post()) {
            $i = 0;
            $template = \Yii::$app->request->post('EventInvitationSent')['template'];
            $query = $internalList->query_sql;
            $result = \Yii::$app->db->createCommand($query)->queryAll();
            $rows = [];
            $internalList->n_user_found = count($result);
            $internalList->save(false);
            foreach ($result as $row) {
                $rows [] = [
                    'name' => $row['nome'],
                    'surname' => $row['cognome'],
                    'email' => $row['email'],
                    'fiscal_code' => $row['codice_fiscale'],
                    'user_id' => $row['user_id'],
                    'event_internal_list_id' => $internalList->id,
                    'template' => $template,
                ];
                if ($template) {
                    $ok = EventMailUtility::sendEmailTemplate($event, $row['user_id'], $row['email'], $template);
                    if ($ok) {
                        $i++;
                        $intivationSent = new EventInvitationSent();
                        $intivationSent->event_internal_list_id = $internalList->id;
                        $intivationSent->user_id = $row['user_id'];
                        $intivationSent->email = $row['email'];
                        $intivationSent->send_at = date('Y-m-d H:i:s');
                        $intivationSent->template = $template;
                        $intivationSent->save();
                    }
                }
            }
            \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', 'Sono state inviate {n}/{tot} inviti', ['n' => $i, 'tot' => $internalList->n_user_found]));

//            }else {
//                $results = EventsUtility::doSendInvitations($internalList->event_id, $rows, false, true);
//                \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', 'Sono stati invitati {cnt}', ['cnt' => $results['cnt']]));
//                \Yii::$app->session->addFlash('danger', $results['errs']);
//            }
        }
        return $this->redirect(['invite', 'id' => $internalList->event_id]);
    }

    /**
     * @param $idList
     * @param $eid
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteInternalList($idList, $eid)
    {
        $internalList = EventInternalList::findOne($idList);
        if ($internalList) {
            $internalList->delete();
        }
        if (EventInternalList::findOne($idList)) {
            \Yii::$app->session->addFlash('danger', "Errore nell'eliminazione");
        } else {
            \Yii::$app->session->addFlash('success', "Elemento eliminato correttamente");
        }
        return $this->redirect(['/events/event-dashboard/invite', 'id' => $eid]);
    }

    /**
     * @param $event
     * @return string
     */
    public function getFromMail($event)
    {
        $from = '';
        if (!empty(trim($event->email_ticket_sender))) {
            $from = $event->email_ticket_sender;
        } else if (isset(\Yii::$app->params['adminEmail'])) {
            $from = \Yii::$app->params['adminEmail'];
        } else if (isset(\Yii::$app->params['email-assistenza'])) {
            $from = \Yii::$app->params['email-assistenza'];
        }
        return $from;
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPublish($id)
    {
        $this->setUpLayout('bootstrap-italia-layout-no-sidebar');

        $this->model = $this->findModel($id);
        $this->model->validated_at_least_once = true;
        $this->model->publish_in_the_calendar = true;
        $this->model->visible_in_the_calendar = true;
        $titleTemplate = '';
        $icons = null;

        /** @var  $landing EventLanding */
        $landing = $this->model->eventLanding;
        if ($landing) {
            $this->setScope($this->model->community_id);
            $utility = new \amos\cmsbridge\utility\CmsUtility();
            $templates = $utility->getCmsTemplates();
            $titleTemplate = '';
            foreach ($templates as $template) {
                if ($template['id'] == $this->model->eventLanding->luya_template_id) {
                    $titleTemplate = $template['title'];
                }
            }

            $presentazione = $this->getStringCompiled(!empty($landing->description) ? 1 : 0);
            $program = $this->getStringCompiled(!empty($landing->schedule) ? 1 : 0);
            $protagonists = $this->getStringCompiled($landing->getEventLandingProtagonists()->count());
            $pics = $this->getStringCompiled($landing->imageSlider ? $landing->imageSlider->getSliderElems()->count() : 0);
            $video = $this->getStringCompiled($landing->videoSlider ? $landing->videoSlider->getSliderElems()->count() : 0);
            $streaming = $this->getStringCompiled(!empty($landing->streaming_url) ? 1 : 0);
            $documenti = $this->getStringCompiled($landing->getDocuments()->count());
            $news = $this->getStringCompiled($landing->getNews()->count());
//            $modelSearch = new NewsSearch();
//            $dataProviderNews = $modelSearch->searchOwnNews([]);
//            $news = $this->getStringCompiled($dataProviderNews->getTotalCount());
            $contactInfoOrganizator = $this->getStringCompiled(!empty($landing->contact_info_organizator) ? 1 : 0);

            $community = Community::findOne($this->model->community_id);
            if ($community) {
                $icons = $community->amosWidgetsIcons;
            }
        }

        if (\Yii::$app->request->isPost) {
            if ($this->model->status == Event::EVENTS_WORKFLOW_STATUS_DRAFT) {
                $this->model->status = Event::EVENTS_WORKFLOW_STATUS_PUBLISHREQUEST;
                $this->model->save(false);
                $this->model->status = Event::EVENTS_WORKFLOW_STATUS_PUBLISHED;
                $this->model->save(false);
                EventMailUtility::sendEventPublishedEmail($this->model);
                $this->addFlash('success', AmosEvents::t('amosevents', 'Evento pubblicato correttamente'));
            } else if ($this->model->status == Event::EVENTS_WORKFLOW_STATUS_PUBLISHREQUEST) {
                $this->model->status = Event::EVENTS_WORKFLOW_STATUS_PUBLISHED;
                $this->model->save(false);
                EventMailUtility::sendEventPublishedEmail($this->model);
                $this->addFlash('success', AmosEvents::t('amosevents', 'Evento pubblicato correttamente'));
            }
            return $this->redirect(['/events/event-dashboard/view', 'id' => $id]);
        }
        return $this->render('publish', [
            'model' => $this->model,
            'presentazione' => $presentazione,
            'program' => $program,
            'protagonists' => $protagonists,
            'pics' => $pics,
            'video' => $video,
            'contact_info_organizator' => $contactInfoOrganizator,
            'streaming' => $streaming,
            'news' => $news,
            'documenti' => $documenti,
            'titleTemplate' => $titleTemplate,
            'icons' => $icons
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUnPublish($id)
    {
        $this->model = $this->findModel($id);
        if ($this->model->status == Event::EVENTS_WORKFLOW_STATUS_PUBLISHED) {
            $this->model->status = Event::EVENTS_WORKFLOW_STATUS_DRAFT;
            if ($this->model->save(false)) {
                \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', "Evento rimosso dalla pubblicazione con successo."));
            }
        }
        return $this->redirect(['/events/event-dashboard/view', 'id' => $id]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteEvent($id)
    {
        $this->model = $this->findModel($id);
        $this->setMenuSidebar($this->model);
        if (\Yii::$app->request->post()) {
            $step1 = \Yii::$app->request->post('step_1');
//            pr(\Yii::$app->request->post());die;
            if (!empty($step1)) {
                return $this->render('delete_event', ['model' => $this->model, 'step_2' => true]);
            } else {
                $community = Community::findOne($this->model->community_id);
                if ($community) {
                    $community->delete();
                }
                $landing = $this->model->eventLanding;
                if ($landing) {
                    $landing->deleteLandingCms();
                    $landing->delete();
                }
                $this->model->delete();
                \Yii::$app->session->addFlash('success', 'Evento cancellato correttamente');
                return $this->redirect('/events/event-dashboard/manage-events');
            }
        }

        return $this->render('delete_event', ['model' => $this->model]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionSuspendEvent($id)
    {
        $this->model = $this->findModel($id);
        $this->setMenuSidebar($this->model);
        if (\Yii::$app->request->post()) {
            $step1 = \Yii::$app->request->post('step_1');
//            pr(\Yii::$app->request->post());die;
            if (!empty($step1)) {
                return $this->render('suspend_event', ['model' => $this->model, 'step_2' => true]);
            } else {
                $this->model->status = Event::EVENTS_WORKFLOW_STATUS_SUSPENDED;
                $this->model->save(false);
                $hash = hash('md5', date('Y-m-d H:i:s'));

                // salva  il log
                $changeEvent = new EventChangeAttributesEvent();
                $changeEvent->logDeleteAction(null, $this->model);
                $changeEvent->sendEmailSuspendedEvent($this->model);

                \Yii::$app->session->addFlash('success', 'Evento annullato correttamente');
                return $this->redirect('/events/event-dashboard/manage-events');
            }
        }

        return $this->render('suspend_event', ['model' => $this->model]);
    }

    /**
     * @param $id
     * @return \yii\console\Response|Response
     * @throws NotFoundHttpException
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionExportParticipants($id)
    {
        $this->model = $this->findModel($id);

//        $communityMembers = CommunityUserMm::find()->andWhere(['community_id' => $this->model->community_id])->all();
        $query = new Query();

        $query->from('event_invitation')
            ->select(new Expression("user.id as 'user_id', user_profile.nome as 'nome', user_profile.cognome as 'cognome', user_profile.codice_fiscale as 'codice_fiscale', user.email as 'email', user_profile.created_at as 'user_profile_created_at', 
            user_profile.sesso as 'sesso', user_profile.azienda as 'azienda', user_profile.telefono as 'telefono', istat_comuni.nome as 'comune', istat_province.nome as 'provincia', user_profile_age_group.age_group as 'eta', 
            community_user_mm.status as 'status', community_user_mm.role as 'role', community_user_mm.created_by as 'member_created_by', user_profile.is_imported as 'is_imported',
            event_invitation.created_at as 'invitation_created_at', event_invitation.is_ticket_sent, event_invitation.ticket_downloaded_at, ticket_downloaded_by, event_invitation.id as 'event_invitation_id', presenza,
            event_accreditation_list.title as 'accreditation_list' , community_user_mm.community_id"))
            ->distinct('user_id')
            ->innerJoin('event', 'event_invitation.event_id = event.id')
            ->innerJoin('community_user_mm', 'community_user_mm.community_id = event.community_id AND event_invitation.user_id=community_user_mm.user_id')
            ->innerJoin('user_profile', 'user_profile.user_id = event_invitation.user_id')
            ->innerJoin('user', 'user_profile.user_id = user.id')
            ->leftJoin('event_accreditation_list', 'event_accreditation_list.id = event_invitation.accreditation_list_id')
            ->leftJoin('user_profile_age_group', 'user_profile_age_group.id = user_profile.user_profile_age_group_id')
            ->leftJoin('istat_comuni', 'istat_comuni.id = user_profile.nascita_comuni_id')
            ->leftJoin('istat_province', 'istat_province.id = user_profile.nascita_province_id')
            ->andWhere(['event.community_id' => $this->model->community_id])
            ->andWhere(['event_invitation.event_id' => $id])
            ->andWhere(['community_user_mm.deleted_at' => null])
            ->andWhere(['event_invitation.deleted_at' => null])
            ->andWhere(['user_profile.deleted_at' => null])
            ->andWhere(['in', 'community_user_mm.status', [CommunityUserMm::STATUS_ACTIVE, CommunityUserMm::STATUS_WAITING_OK_COMMUNITY_MANAGER]])
            ->all();
        //array per il file
        $xlsData = [];
        $formFieldLabel = [];
        $formFieldValues = [];
        if ($this->model->eventLanding) {
            /** @var  $landing EventLanding */
            $landing = $this->model->eventLanding;
            if ($landing->ask_sex) {
                $formFieldLabel [] = "Genere";
                $formFieldValues[] = "sesso";
            }
            if ($landing->ask_age) {
                $formFieldLabel [] = "Età";
                $formFieldValues[] = "eta";
            }
            if ($landing->ask_county) {
                $formFieldLabel [] = "Provincia";
                $formFieldValues[] = "provincia";
            }
            if ($landing->ask_city) {
                $formFieldLabel [] = "Comune";
                $formFieldValues[] = "comune";
            }
            if ($landing->ask_telefon) {
                $formFieldLabel [] = "Telefono";
                $formFieldValues[] = "telefono";
            }
            if ($landing->ask_fiscal_code) {
                $formFieldLabel [] = "Codice fiscale";
                $formFieldValues[] = "codice_fiscale";
            }
            if ($landing->ask_company) {
                $formFieldLabel [] = "Azienda";
                $formFieldValues[] = "azienda";
            }
        }

        $xlsData[0] = ["Nome", "Cognome", "Email"];
        $xlsData[0] = ArrayHelper::merge($xlsData[0], $formFieldLabel);
        $xlsData[0] = ArrayHelper::merge($xlsData[0],
            ["In lista di attesa", "Ruolo", "Lista accreditamento", "Numero partecipanti aggiuntivi", "Utente importato?", "Data importazione", "Data creazione utente manuale", "Data di iscrizione da form", "Biglietto inviato?", "Biglietto scaricato il", "Presente all'evento"]
        );
        //recupera i dati
        $members = $query->all();
        /** @var $member CommunityUserMm */
        foreach ($members as $member) {
            $companions = '-';
            if ($this->model->enable_companions) {
                $companions = EventParticipantCompanion::find()->andWhere(['event_invitation_id' => $member['event_invitation_id']])->count();
            }
            $tmpIscrizione = [
                $member['nome'],
                $member['cognome'],
                $member['email'],
            ];

            foreach ($formFieldValues as $val) {
                $tmpIscrizione [] = $member[$val];
            }

            $waitingList = false;
            if ($member['status'] == CommunityUserMm::STATUS_WAITING_OK_COMMUNITY_MANAGER) {
                $waitingList = true;
            }
            $tmpIscrizione = ArrayHelper::merge($tmpIscrizione, [
                \Yii::$app->formatter->asBoolean($waitingList),
                $member['role'],
                $member['accreditation_list'],
                $companions,
                $member['is_imported'] ? \Yii::$app->formatter->asBoolean(true) : \Yii::$app->formatter->asBoolean(false),
                $member['is_imported'] ? \Yii::$app->formatter->asDate($member['user_profile_created_at']) : '',
                !$member['is_imported'] ? \Yii::$app->formatter->asDate($member['user_profile_created_at']) : '',
                $member['invitation_created_at'] ? \Yii::$app->formatter->asDate($member['invitation_created_at'], 'php:d-m-Y') . " " . \Yii::$app->getFormatter()->asTime($member['invitation_created_at'], 'php:H:i:s') : '',
                \Yii::$app->formatter->asBoolean($member['is_ticket_sent']),
                $member['ticket_downloaded_at'] ? \Yii::$app->formatter->asDate($member['ticket_downloaded_at'], 'php:d-m-Y') . " " . \Yii::$app->getFormatter()->asTime($member['ticket_downloaded_at'], 'php:H:i:s') : '',
                \Yii::$app->formatter->asBoolean($member['presenza']),
//                ($member->created_at) ? \Yii::$app->formatter->asDate($member->created_at, 'php:d-m-Y') . " " . \Yii::$app->getFormatter()->asTime($member->created_at, 'php:H:i:s') : '',
            ]);

            $xlsData[] = $tmpIscrizione;
        }


        //inizializza l'oggetto excel

        $objPHPExcel = new \PHPExcel();
        //li pone nella tab attuale del file xls
        $objPHPExcel->getActiveSheet()->fromArray($xlsData, NULL, 'A1');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('/tmp/Event_participants.xls');
        return \Yii::$app->response->sendFile('/tmp/Event_participants.xls');
        die;
    }

    /**
     * @param $id
     * @param $title
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionGetPreviewLanding($id = null, $nav_id)
    {
        $this->model = Event::findOne($id);
        $utility = new \amos\cmsbridge\utility\CmsUtility();
        $templates = $utility->getCmsTemplates();
        foreach ($templates as $template) {
            if ($nav_id == $template['id']) {
                $class = EventsUtility::getClassPreviewLanding($template['title']);
            }
        }

        if (!empty($this->model->eventLanding)) {
            $eventLanding = $this->model->eventLanding;
            $htmlTemplate[$eventLanding->luya_template_id] = $class;
        } else {
            $htmlTemplate[$nav_id] = $class;
        }
        return $this->renderAjax('@vendor/open20/amos-events/src/views/event-dashboard/_parts/_preview_landing', [
            'model' => $this->model,
            'eventLanding' => $eventLanding, 'htmlTemplate' => $htmlTemplate, 'nav_id' => $nav_id, 'classTemplate' => $class
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionCommunications($id)
    {
        $this->model = $this->findModel($id);
        $this->model->detachBehavior('seoBehavior');
        $eventCommunication = new EventCommunication();

        $this->setMenuSidebar($this->model);

        $dataProviderLists = new ActiveDataProvider([
            'query' => $this->model->getEventCommunications()
        ]);
        $eventCommunicationClone = clone $eventCommunication;
        if (\Yii::$app->request->post() && $eventCommunicationClone->load(\Yii::$app->request->post()) && $eventCommunication->load(\Yii::$app->request->post()) && $eventCommunication->validate()) {
            if ($eventCommunicationClone->communication_type != EventCommunication::TYPE_NOMINAL) {
                $query = $eventCommunication->searchUsersQuery($id);
                $eventCommunication->n_users = $query->count();
                if ($eventCommunicationClone->save(false)) {
                    \Yii::$app->session->addFlash('success', 'Comunicazione salvata correttamente');
                    return $this->redirect(['communications', 'id' => $id]);
                }
            } else {
                $eventCommunicationClone->save(false);
                return $this->redirect(['nominal-invitation', 'id' => $id, 'communication_id' => $eventCommunicationClone->id]);
            }
        }
        return $this->render('communications', [
            'eventCommunication' => $eventCommunication,
            'model' => $this->model,
            'dataProviderLists' => $dataProviderLists
        ]);
    }

    /**
     * @param $idCommunication
     * @return string
     */
    public function actionCommunicationsUpdate($idCommunication)
    {
        $eventCommunication = EventCommunication::findOne($idCommunication);
        $model = $eventCommunication->event;
        $this->setMenuSidebar($model);

        if (\Yii::$app->request->post() && $eventCommunication->load(\Yii::$app->request->post()) && $eventCommunication->validate()) {
            $query = $eventCommunication->searchUsersQuery($model->id);
            $eventCommunication->n_users = $query->count();
            if ($eventCommunication->save()) {
                \Yii::$app->session->addFlash('success', 'Comunicazione salvata correttamente');
                return $this->redirect(['communications-update', 'idCommunication' => $eventCommunication->id, 'eid' => $model->id]);
            }
        }

        if ($eventCommunication->communication_type == EventCommunication::TYPE_NOMINAL) {
            $n_users = $eventCommunication->n_users;
        } else {
            $n_users = '' . $eventCommunication->searchUsersQuery($model->id)->count();
        }

        return $this->render('communications_update', [
            'eventCommunication' => $eventCommunication,
            'model' => $model,
            'n_users' => $n_users
        ]);
    }

    /**
     * @return string
     */
    public function actionSendCommunicationTest()
    {
        $post = \Yii::$app->request->post();
        $ok = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $eventCommuication = new EventCommunication();
        if ($eventCommuication->load($post)) {

            $from = \Yii::$app->params['email-assistenza'];
            $to = [$eventCommuication->email];
            $subject = $eventCommuication->subject_email;
            $message = $eventCommuication->text_email;

            $event = $eventCommuication->event;
            $subjectWithParams = EventMailUtility::parseEmailWithParams($event, \Yii::$app->user->id, $subject, false);
            $textWithParms = EventMailUtility::parseEmailWithParams($event, \Yii::$app->user->id, $message);

            $ok = EventMailUtility::sendEmailTest($from, $to, $subjectWithParams, $textWithParms, $event, EventEmailTemplates::FOOTER_TYPE_TAG_AND_UNSUBSCRIBE);

//            $ok = EventMailUtility::sendEmailGeneral($from, $to, $subjectWithParams, $textWithParms);
        }
        if ($ok) {
            return $eventCommuication->email;
        } else
            return 'Nessuno';
    }

    /**
     * @param $idCommunication
     * @return string|Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSendCommunication($idCommunication, $eid)
    {
        $eventCommunication = EventCommunication::findOne($idCommunication);
        $event = $eventCommunication->event;
        $this->setMenuSidebar($event);
        $i = 0;

        $query = $eventCommunication->searchUsersQuery($eventCommunication->event_id);
        if (\Yii::$app->request->post()) {
            $users = $query->all();
            if ($eventCommunication->communication_type != EventCommunication::TYPE_NOMINAL) {
                $eventCommunication->n_users = count($users);
            }
            $eventCommunication->status = EventCommunication::STATUS_USERS_TO_IMPORT;
            $eventCommunication->save(false);

//            foreach ($users as $user) {
//                $ok = EventMailUtility::sendCommunication($event, $user->id, $user->email, $eventCommunication->text_email, $eventCommunication->subject_email);
//                if ($ok) {
//                    $i++;
//                    $intivationSent = new EventCommunicationSent();
//                    $intivationSent->event_communication_id = $eventCommunication->id;
//                    $intivationSent->user_id = $user->id;
//                    $intivationSent->email = $user->email;
//                    $intivationSent->sent_at = date('Y-m-d H:i:s');
//                    $intivationSent->save();
//                }
//            }
            \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', 'Sono state inviate {tot} comunicazioni', ['tot' => $eventCommunication->n_users]));

            return $this->redirect(['/events/event-dashboard/communications', 'id' => $event->id]);
        }

        if ($eventCommunication->communication_type == EventCommunication::TYPE_NOMINAL) {
            $n_users = $eventCommunication->n_users;
        } else {
            //pr($query->createCommand()->rawSql);
            $n_users = $query->count();
        }

        return $this->render('communications_send', [
            'n_users' => $n_users,
            'model' => $event,
            'eventCommunication' => $eventCommunication
        ]);
    }

    /**
     * @param $idCommunication
     * @return Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteCommunication($idCommunication, $urlRedrect = null)
    {
        $eventCommunication = EventCommunication::findOne($idCommunication);
        $event_id = $eventCommunication->event_id;
        $eventCommunication->delete();
        \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', 'Comunicazione eliminata correttamente'));

        if (!empty($urlRedrect)) {
            return $this->redirect($urlRedrect);
        }
        return $this->redirect(['/events/event-dashboard/communications', 'id' => $event_id]);
    }

    /**
     * @param $id
     * @param null $urlRedirect
     * @return Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUnsubscribe($id, $urlRedirect = null)
    {
        $this->model = $this->findModel($id);
        $member = CommunityUserMm::find()
            ->andWhere(['community_id' => $this->model->community_id])
            ->andWhere(['user_id' => \Yii::$app->user->id])->one();
        if ($member) {
//            $member->status = CommunityUserMm::STATUS_REJECTED;
//            $member->save(false);
            $member->delete();
            $invitation = EventInvitation::find()->andWhere(['event_id' => $this->model->id, 'user_id' => \Yii::$app->user->id])->one();
            if ($invitation) {
                $companions = $invitation->companions;
                foreach ($companions as $companion) {
                    $companion->delete();
                }
                $invitation->delete();
                EventsUtility::setCookieForCache();
            }
            \open20\amos\core\models\UserActivityLog::registerLog(AmosEvents::t('amosevents', 'Disiscrizione da evento'), $this->model, Event::LOG_TYPE_UNSUBSCRIBE_EVENT);
            \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', "Ti sei discritto all'evento con successo"));
        }
        if ($urlRedirect) {
            return $this->redirect($urlRedirect);
        }
        return $this->redirect(['/events/event-dashboard/my-events']);
    }

    /**
     * @param $idInternalList
     * @return Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionSendInvitation($idInternalList)
    {
        $internalList = EventInternalList::findOne($idInternalList);
        if (empty($internalList)) {
            throw new NotFoundHttpException('Pagina non trovata');
        }
        $event = $internalList->event;
        $this->model = $event;

        if (\Yii::$app->request->post()) {
            if ($internalList->load(\Yii::$app->request->post()) && $internalList->validate()) {
                $template = \Yii::$app->request->post('EventInvitationSent')['template'];
                $schedule_type = \Yii::$app->request->post('EventInternalList')['time_schedule_type'];
                $schedule_date = \Yii::$app->request->post('EventInternalList')['time_schedule_date'];
                $query = $internalList->query_sql;

                $result = \Yii::$app->db->createCommand($query)->queryAll();
                $internalList->n_user_found = count($result);
                $internalList->template = $template;
                $internalList->time_schedule_type = $schedule_type;
                if ($schedule_type == EventInternalList::SENDING_TIME_SCHEDULED) {
                    $internalList->time_schedule_date = $schedule_date;
                }
                $internalList->status = EventInternalList::STATUS_USERS_TO_IMPORT;
                $internalList->save(false);

                /** @var  $newsletterModule */
//            $newsletterModule = \Yii::$app->getModule('newsletter');
//            if ($newsletterModule) {
//                $mailServiceClassname = $newsletterModule->mail_service_driver;
//                $mailService = new $mailServiceClassname();
//                $data = ['Name' => $this->model->id . '_' . $idInternalList . '_' . $this->model->title, ' Notes' => ''];
//                $groupDecoded = $mailService->createGroup($mailupListId, $data);
//                if ($groupDecoded) {
//                    $groupId = $groupDecoded->idGroup;
//                    $internalList->mailup_group_id = $groupId;
//                    $internalList->mailup_list_id = $mailupListId;
//                    $internalList->save(false);
//                }
//            }


                if ($this->model->eventType->event_type != EventType::TYPE_UPON_INVITATION && $internalList->type != UserEventSearch::SEARCH_TYPE_COMMUNICATION) {
                    $pushnotification = new EventPushNotification();
                    $pushnotification->event_id = $this->model->id;
                    $pushnotification->content_class = EventInternalList::className();
                    $pushnotification->content_id = $internalList->id;
                    $pushnotification->type = $template;
                    $pushnotification->status = EventPushNotification::STATUS_DRAFT;
                    $pushnotification->save(false);
                }

                \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', 'Sono stati invitati {tot} utenti', ['tot' => $internalList->n_user_found]));
            } else {

                return $this->redirect(['summary-sending',
                    'idInternalList' => $internalList->id,
                    'errors' => json_encode($internalList->getErrors()),
                    'post' => json_encode(\Yii::$app->request->post())
                ]);
            }
        }
        return $this->redirect(['invite', 'id' => $internalList->event_id]);
    }

    /**
     * @param $internalListId
     * @param $eid
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionStatisticsInvitation($internalListId, $eid)
    {
        $internalList = EventInternalList::find()->andWhere(['id' => $internalListId])->one();
        if (empty($internalList)) {
            throw new NotFoundHttpException("Pagina non trovata");
        }

        $idMessage = $internalList->mailup_message_id;
        $idList = $internalList->mailup_list_id;
        $moduleNewsletter = \Yii::$app->getModule('newsletter');
        if ($moduleNewsletter && $idMessage && $idList) {
            $mailServiceClassname = $moduleNewsletter->mail_service_driver;
            $mailService = new $mailServiceClassname();

            $lists = [];
            $listsHistory = [];
            $listsRecipients = [];
            $queryParams = \Yii::$app->request->queryParams;

            $decodedOpened = $mailService->getStatisticOpened($idMessage, true, $queryParams);
            $decodedClicks = $mailService->getStatisticClicks($idMessage, true, $queryParams);
            $decodedBounces = $mailService->getStatisticBounces($idMessage, true, $queryParams);
            $decodedUnsubscribed = $mailService->getStatisticUnsubscribed($idMessage, true, $queryParams);
            $decodedClickedLinks = $mailService->getStatisticClickedLinks($idMessage, true, $queryParams);
            $decodedRecipients = $mailService->getEmailRecipients($idMessage, $queryParams);

            if ($idList) {
                $decodedHistory = $mailService->getEmailSendHistory($idList, $idMessage, $queryParams);
            }


            $counts = [
                'idmessage' => $idMessage,
                'opened' => $decodedOpened,
                'clicks' => $decodedClicks,
                'bounces' => $decodedBounces,
                'unsubscribed' => $decodedUnsubscribed,
            ];

            if (!empty($decodedClickedLinks->Items)) {
                $lists = $decodedClickedLinks->Items;
            }

            if (!empty($decodedHistory->Items)) {
                $listsHistory = $decodedHistory->Items;
            }

            if (!empty($decodedRecipients->Items)) {
                $listsRecipients = $decodedRecipients->Items;
            }

            $dataProvider = new ArrayDataProvider([
                'allModels' => $lists
            ]);
            $dataProviderHistory = new ArrayDataProvider([
                'allModels' => $listsHistory
            ]);

            $dataProviderRecipients = new ArrayDataProvider([
                'allModels' => $listsRecipients
            ]);

            $pagination = $this->setPaginationMailup($mailService, $decodedRecipients);

            return $this->render('statistics_invitation', [
                'dataProvider' => $dataProvider,
                'dataProviderHistory' => $dataProviderHistory,
                'dataProviderRecipients' => $dataProviderRecipients,
                'counts' => $counts,
                'pagination' => $pagination,
                'model' => $internalList,
                'event' => $internalList->event
            ]);
        }
        throw new NotFoundHttpException("Pagina non trovata");
    }

    /**
     * @param $internalListId
     * @param $eid
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionStatisticsCommunication($communicationId, $eid = null, $urlRedirect = null)
    {
        $communication = EventCommunication::find()->andWhere(['id' => $communicationId])->one();
        if (empty($communication)) {
            throw new NotFoundHttpException("Pagina non trovata");
        }

        $idMessage = $communication->mailup_message_id;
        $idList = $communication->mailup_list_id;
        $moduleNewsletter = \Yii::$app->getModule('newsletter');
        if ($moduleNewsletter && $idMessage && $idList) {
            $mailServiceClassname = $moduleNewsletter->mail_service_driver;
            $mailService = new $mailServiceClassname();

            $lists = [];
            $listsHistory = [];
            $listsRecipients = [];
            $queryParams = \Yii::$app->request->queryParams;

            $decodedOpened = $mailService->getStatisticOpened($idMessage, true, $queryParams);
            $decodedClicks = $mailService->getStatisticClicks($idMessage, true, $queryParams);
            $decodedBounces = $mailService->getStatisticBounces($idMessage, true, $queryParams);
            $decodedUnsubscribed = $mailService->getStatisticUnsubscribed($idMessage, true, $queryParams);
            $decodedClickedLinks = $mailService->getStatisticClickedLinks($idMessage, true, $queryParams);
            $decodedRecipients = $mailService->getEmailRecipients($idMessage, $queryParams);

            if ($idList) {
                $decodedHistory = $mailService->getEmailSendHistory($idList, $idMessage, $queryParams);
            }


            $counts = [
                'idmessage' => $idMessage,
                'opened' => $decodedOpened,
                'clicks' => $decodedClicks,
                'bounces' => $decodedBounces,
                'unsubscribed' => $decodedUnsubscribed,
            ];

            if (!empty($decodedClickedLinks->Items)) {
                $lists = $decodedClickedLinks->Items;
            }

            if (!empty($decodedHistory->Items)) {
                $listsHistory = $decodedHistory->Items;
            }

            if (!empty($decodedRecipients->Items)) {
                $listsRecipients = $decodedRecipients->Items;
            }

            $dataProvider = new ArrayDataProvider([
                'allModels' => $lists
            ]);
            $dataProviderHistory = new ArrayDataProvider([
                'allModels' => $listsHistory
            ]);

            $dataProviderRecipients = new ArrayDataProvider([
                'allModels' => $listsRecipients
            ]);

            $pagination = $this->setPaginationMailup($mailService, $decodedRecipients);

            return $this->render('statistics_communication', [
                'dataProvider' => $dataProvider,
                'dataProviderHistory' => $dataProviderHistory,
                'dataProviderRecipients' => $dataProviderRecipients,
                'counts' => $counts,
                'pagination' => $pagination,
                'model' => $communication,
                'event' => $communication->event
            ]);
        }
        throw new NotFoundHttpException("Pagina non trovata");
    }

    /**
     * @param $mailService
     * @param $decoded
     * @return \yii\data\Pagination
     */
    public function setPaginationMailup($mailService, $decoded)
    {
        $currentPage = 0;
        $totElement = 0;;

        $paginationConfig = $mailService->getPaginationConfigs();
        $pagination = new \yii\data\Pagination();

        if (!empty($paginationConfig['totalCount'])) {
            $totalCount = $paginationConfig['totalCount'];
            if (property_exists($decoded, $totalCount)) {
                $totElement = $decoded->$totalCount;
                $pagination->totalCount = $totElement;
            }
        }

        if (!empty($paginationConfig['pageParam'])) {
            $pageParam = $paginationConfig['pageParam'];
            if (property_exists($decoded, $pageParam)) {
                $currentPage = $decoded->$pageParam;
                $pagination->pageParam = $pageParam;
                $pagination->setPage($currentPage);
            }
        }

        return $pagination;
    }

    /**
     * @param $internalList
     * @param $eid
     */
    public function actionStopSending($internalListId, $eid)
    {
        $internalList = EventInternalList::findOne($internalListId);
        if ($internalList->mailup_sending_id && $internalList->status != \open20\amos\events\models\EventInternalList::STATUS_SENT) {
            $moduleNewsletter = \Yii::$app->getModule('newsletter');
            if ($moduleNewsletter) {
                $mailServiceClassname = $moduleNewsletter->mail_service_driver;
                $mailService = new $mailServiceClassname();
                $result = $mailService->stopSending($internalList->mailup_sending_id);
                if (!empty($result->ErrorCode) && $result->ErrorCode == 429) {
                    \Yii::$app->session->addFlash('warning', AmosEvents::t('amosevents', "E' possibile effettuare Max: 1 chiamata ogni 30 secondi"));
                    return $this->redirect(['invite', 'id' => $eid]);
                }
                if (!empty($result->IsDeleted) && $result->IsDeleted == true) {
                    $internalList->mailup_sending_id = null;
                    $internalList->mailup_message_id = null;
                    $internalList->mailup_list_id = null;
                    $internalList->status = null;
                    $internalList->save(false);
                    \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', "Invio annullato con successo"));
                } else {
                    \Yii::$app->session->addFlash('danger', AmosEvents::t('amosevents', "L'annullamento dell'invio è fallito"));
                }
            }
        }

        if (!$internalList->mailup_sending_id && $internalList->status != \open20\amos\events\models\EventInternalList::STATUS_SENT) {
            $internalList->mailup_sending_id = null;
            $internalList->mailup_message_id = null;
            $internalList->mailup_list_id = null;
            $internalList->status = null;
            $internalList->save(false);
            \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', "Invio annullato con successo"));
        }
        return $this->redirect(['invite', 'id' => $eid]);
    }

    /**
     * @param $internalList
     * @param $eid
     */
    public function actionStopSendingCommunication($communicationId, $eid = null, $urlRedirect = null)
    {
        $communication = EventCommunication::findOne($communicationId);
        if ($communication->mailup_sending_id && $communication->status != \open20\amos\events\models\EventCommunication::STATUS_SENT) {
            $moduleNewsletter = \Yii::$app->getModule('newsletter');
            if ($moduleNewsletter) {
                $mailServiceClassname = $moduleNewsletter->mail_service_driver;
                $mailService = new $mailServiceClassname();
                $result = $mailService->stopSending($communication->mailup_sending_id);
                if (!empty($result->ErrorCode) && $result->ErrorCode == 429) {
                    \Yii::$app->session->addFlash('warning', AmosEvents::t('amosevents', "E' possibile effettuare Max: 1 chiamata ogni 30 secondi"));
                    if (!empty($urlRedirect)) {
                        return $this->redirect($urlRedirect);
                    }
                    return $this->redirect(['communications', 'id' => $eid]);
                }
                if (!empty($result->IsDeleted) && $result->IsDeleted == true) {
                    $communication->mailup_sending_id = null;
                    $communication->mailup_message_id = null;
                    $communication->mailup_list_id = null;
                    $communication->status = null;
                    $communication->save(false);
                    \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', "Invio annullato con successo"));
                } else {
                    \Yii::$app->session->addFlash('danger', AmosEvents::t('amosevents', "L'annullamento dell'invio è fallito"));
                }
            }
        }

        if (!$communication->mailup_sending_id && $communication->status != \open20\amos\events\models\EventCommunication::STATUS_SENT) {
            $communication->mailup_sending_id = null;
            $communication->mailup_message_id = null;
            $communication->mailup_list_id = null;
            $communication->status = null;
            $communication->save(false);
            \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', "Invio annullato con successo"));
        }
        if (!empty($urlRedirect)) {
            return $this->redirect($urlRedirect);
        }
        return $this->redirect(['communications', 'id' => $eid]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \open20\amos\community\exceptions\CommunityException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionJoinCommunity($id)
    {
        $this->model = $this->findModel($id);
        if (CommunityUtil::loggedUserIsParticipant($this->model->community_id)) {
            return $this->redirect(['/community/join', 'id' => $this->model->community_id]);
        } else {
            $dg = EventsUtility::getCurrentDg();
            //if is an operator subscribe as as staff directly
            if (\Yii::$app->user->can('SUPER_USER_EVENT') || ($dg && $this->model->event_group_referent_id == $dg->id)) {
                /** @var  $moduleCommunity AmosCommunity */
                $moduleCommunity = \Yii::$app->getModule('community');
                if ($moduleCommunity) {
                    $moduleCommunity->createCommunityUser($this->model->community_id, CommunityUserMm::STATUS_ACTIVE, 'EVENT_MANAGER', \Yii::$app->user->id);
                    return $this->redirect(['/community/join', 'id' => $this->model->community_id]);
                }
            }
        }
        \Yii::$app->session->addFlash('warning', AmosEvents::t('amosevents', 'Non sei abilitato ad accedere a questa community'));
        return $this->redirect('/dashboard');
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionJoin($id)
    {
        $this->model = $this->findModel($id);
        return $this->redirect(['/community/join', 'id' => $this->model->community_id]);
    }

    /**
     * @param $event_type_id
     * @return string
     */
    public function actionDescriptionTypeEvent($event_type_id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $type = EventType::findOne($event_type_id);
        return $type->description;
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionConfirmUnsubscribe($id)
    {
        $this->model = $this->findModel($id);
        $invitation = EventInvitation::find()->andWhere(['event_id' => $id, 'user_id' => \Yii::$app->user->id])->one();
        $countCompanions = 0;
        if ($invitation) {
            $countCompanions = $invitation->getCompanions()->andWhere(['event_participant_companion.deleted_at' => null])->count();
        }
        return $this->render('confirm-unsubscribe', ['model' => $this->model, 'countCompanions' => $countCompanions]);
    }

    /**
     * @param $string
     * @return string
     */
    public function getStringCompiled($n)
    {
        if ($n > 0) {
            return AmosEvents::t('amosevents', "Compilato") . " " . AmosIcons::show('check', ['style' => 'color:#28a745']);
        } else {
            return AmosEvents::t('amosevents', "Non compilato") . " " . AmosIcons::show('close', ['class' => 'text-danger']);
        }
    }

    /**
     * @return mixed
     */
    public function actionPushNotificationTest()
    {
        $notificationController = new \open20\amos\mobile\bridge\controllers\NotificationController('notification', \Yii::$app->getModule('mobilebridge'));
        return $notificationController->sendNotification(1, 'EVENTI - titolo', "Notifica di prova\n testo lungo", 'event', 1);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionDuplicate($id)
    {
        $this->model = $this->findModel($id);
        $dataProvider = null;
        $duplicateRecursiveForm = new DuplicateRecursiveForm();
        $duplicateRecursiveForm->event_id = $this->model->id;

        if (\Yii::$app->request->post() && $duplicateRecursiveForm->load(\Yii::$app->request->post())) {
            $dataProvider = $duplicateRecursiveForm->generateDates();
            $confirm = \Yii::$app->request->post('confirm');
            if (!empty($confirm)) {
                $dates = \Yii::$app->request->post('date');
                $datesEnd = \Yii::$app->request->post('date-end');
                $i = 0;

                foreach ($dates as $key => $dateBegin) {
                    $dateEnd = null;
                    if (!empty($datesEnd[$key])) {
                        $dateEnd = $datesEnd[$key];
                    }
                    EventsUtility::copyEvent($this->model, $dateBegin, $dateEnd, $duplicateRecursiveForm->enableTimePeriod);
                    if ($duplicateRecursiveForm->enableTimePeriod) {
                        $this->model->is_time_period = true;
                        $this->model->save(false);
                    }
                    $i++;
                }
                \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', "Sono stati creati <strong>{x}</strong> eventi con successo", [
                    'x' => $i
                ]));

                return $this->redirect('/events/event-dashboard/manage-events');
//                EventsUtility::copyEvent($this->model, $dates[1]);
            }
        }

        return $this->render('duplicate', [
            'model' => $this->model,
            'modelForm' => $duplicateRecursiveForm,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @return string
     */
    public function actionTime()
    {
        $n = \Yii::$app->request->post('n');
        $form = new \yii\bootstrap4\ActiveForm();
        $modelForm = new DuplicateRecursiveForm();
        return $this->renderAjax('@vendor/open20/amos-events/src/views/event-dashboard/_parts/_time_period', [
            'form' => $form,
            'modelForm' => $modelForm,
            'n' => $n
        ]);
    }

    /**
     * @param $id
     */
    public function actionChildren($id)
    {
        $this->model = $this->findModel($id);
        $this->setMenuSidebar($this->model);

        $dataProviderTimePeriods = new ActiveDataProvider([
            'query' => $this->model->getEventTimePeriods()
        ]);
        $dataProviderChildren = new ActiveDataProvider([
            'query' => $this->model->getEventChildren()
        ]);
        return $this->render('children', [
            'model' => $this->model,
            'dataProviderTimePeriods' => $dataProviderTimePeriods,
            'dataProviderChildren' => $dataProviderChildren
        ]);
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionHourByDate()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $event_id = $parents[0];
                $event = Event::findOne($event_id);
                if ($event) {
                    $explode = explode(' ', $event->begin_date_hour);
                    $events = Event::find()
                        ->andWhere(['event_id' => $event->event_id])
                        ->andWhere(['is_time_period' => 1])
                        ->andWhere(['status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED])
                        ->andWhere(['like', 'begin_date_hour', $explode[0]])->all();
                    foreach ($events as $ev) {
                        $url = EventsUtility::getUrlLanding($ev);
                        $beginDate = new \DateTime($ev->begin_date_hour);
                        $endDate = new \DateTime($ev->end_date_hour);
                        $out[] = ['id' => $url, 'name' => $beginDate->format('H:i') . ' - ' . $endDate->format('H:i')];
                    }
                }
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                return ['output' => $out, 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    /**
     * @param $id
     * @param $slider_id
     * @param $direction
     * @return \yii\web\Response
     */
    public function actionOrderHighlights($id, $direction)
    {
        $elements = EventHighlights::find()->orderBy('n_order ASC')->all();
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

        return $this->redirect(['/events/event-dashboard/index-highlights']);
    }

    /**
     * @return string
     */
    public function actionAddToHighlights($event_id = null)
    {
        $model = new EventSearch();
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['enableChangeView'] = false;
        \Yii::$app->view->params['createNewBtnParams'] = ['layout' => ''];
        if (!empty($event_id)) {
            $highlight = EventHighlights::findOne($event_id);
            if (empty($highlight)) {
                $lastOrder = EventHighlights::getLastOrder();
                $newHighLigh = new EventHighlights();
                $newHighLigh->event_id = $event_id;
                $newHighLigh->n_order = $lastOrder + 1;
                $newHighLigh->save(false);
                \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', "Evento aggiunto correttamente"));
            } else {
                \Yii::$app->session->addFlash('warning', AmosEvents::t('amosevents', "Evento già presente"));
            }
            return $this->redirect('index-highlights');
        }

        $dataProvider = $model->searchPublished(\Yii::$app->request->get());
        $dataProvider->query->andWhere(['not in', 'event.id', EventHighlights::find()->select('event_id')]);
        return $this->render('add-to-highlights', [
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

    /**
     * @param $event_id
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionRemoveHighlights($id)
    {
        $highlight = EventHighlights::findOne($id);
        if ($highlight) {
            EventHighlights::deleteAll(['id' => $id]);

            //reset ids
            $elements = EventHighlights::find()->orderBy('n_order ASC')->all();
            $orderList = [];
            foreach ($elements as $element) {
                $orderList [] = $element->id;
            }
            $this->resetNumberOrder($orderList);

        }
        \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', "Evento rimosso correttamente"));
        return $this->redirect(['/events/event-dashboard/index-highlights']);
    }


    /**
     * @param $orderList
     */
    public function resetNumberOrder($orderList)
    {
        $i = 1;
        foreach ($orderList as $id) {
            $containerElemMm = EventHighlights::findOne($id);
            $containerElemMm->n_order = $i;
            $containerElemMm->save(false);
            $i++;
        }
    }

    /**
     * @param $id
     * @return string|Response
     * @throws \yii\db\Exception
     */
    public function actionWebMeetingWebex($id)
    {
        $module = \Yii::$app->getModule('events');
        $this->model = $this->findModel($id);
        $this->setMenuSidebar($this->model);

        $modelWebexAccount = EventWebexAccounts::findOne(['user_id' => \Yii::$app->user->id]);
        $modelUserProfile = UserProfile::findOne(['user_id' => \Yii::$app->user->id]);

        if ($this->model->webmeeting_webex_id) {
            $webmeeting = \open20\webmeeting\models\WebMeetingModel::findOne($this->model->webmeeting_webex_id);
        } else {
            $webmeeting = new \open20\webmeeting\models\WebMeetingModel();
        }
        $webmeeting->timezone = \open20\webmeeting\WebMeetingModule::WEBMEETING_DEFAULT_TIMEZONE;
        $connectionWebex = new \open20\webmeeting\models\ConnectionWebex();

        if (\Yii::$app->request->post() && $webmeeting->load(\Yii::$app->request->post()) && EventsUtility::checkDatesWebmeeting($webmeeting, $this->model)) {
//            $webmeeting->password = 'Elite2020!';
            $webmeeting->host_email = 'info@open20.it';
            $webmeeting->save(false);
            $module->hostsWebmeeting['coHosts'] = [
                [
                    "email" => $modelWebexAccount->email_webex,
                    "display_name" => $modelUserProfile->nomecognome,
                    "coHost" => true
                ]
            ];

            $transaction = ActiveRecord::getDb()->beginTransaction();
            EventsUtility::setHostsWebmeeting($webmeeting);

            $apiParams = $webmeeting->getAttributes();

//            pr($apiParams); die;
            $apiResponse = (empty($this->model->webmeeting_webex_id)) ? $connectionWebex->apiToCall->createMeeting($apiParams) : $connectionWebex->apiToCall->updateMeeting($apiParams);

            //if response ok
            if (!isset($apiResponse->message)) {
                $connectionWebex->saveResponse($apiResponse, $webmeeting);
                $this->model->webmeeting_webex_id = $webmeeting->id;
                $this->model->save(false);

                \Yii::$app->getSession()->addFlash('success', AmosEvents::t('amosevents', "Elemento salvato correttamente"));
                $transaction->commit();
                return $this->redirect(['/events/event-dashboard/web-meeting-webex', 'id' => $id]);
            } else {
                $connectionWebex->showErrors($apiResponse);
                $transaction->rollBack();
            }
        }
        return $this->render('webmeeting_webex', [
            'model' => $this->model,
            'webmeeting' => $webmeeting,
            'coHostUserEmail' => $connectionWebex->coHostUserEmail,
            'timezone' => $connectionWebex->timezone,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionWebMeetingParticipants($id)
    {
        $this->model = $this->findModel($id);
        $webmeeting = $this->model->webMeetingWebex;
        $this->setMenuSidebar($this->model);

        $modelSearch = new SpecificUserEventSearch();
        $dataProvider = $modelSearch->search(\Yii::$app->request->get());
        $dataProviderInvitationUsers = new ActiveDataProvider([
            'query' => $this->model->getEventSpecificUsers()
        ]);

        $arrayUsers = $this->model->getEventSpecificUsers()->asArray()->select('user_id')->all();
        $participants_user_ids = [];
        foreach ($arrayUsers as $user_id) {
            $participants_user_ids [] = $user_id['user_id'];
        }
        $countInvitationUsers = $this->model->getEventSpecificUsers()->count();

        $readOnly = false;
        if (!EventsUtility::canModifyWebexParticipant($this->model)) {
            $readOnly = true;
        }
        $connectionWebex = new \open20\webmeeting\models\ConnectionWebex();

        if (\Yii::$app->request->isPost) {
            // INSERT NEW PARTICIPANTS
            $users_ids_str = \Yii::$app->request->post('selected_users_ids');
            EventInvitationSpecificUsers::saveInvitedUsers($users_ids_str, $webmeeting, $connectionWebex, $id);

            // DELETE PARTICIPANTS
            $users_ids_str = \Yii::$app->request->post('deleted_users_ids');
            EventInvitationSpecificUsers::deleteInvitedUsers($users_ids_str, $webmeeting, $connectionWebex, $id);

            $this->model->generateInternalListWebmeeting();
            return $this->redirect(['/events/event-dashboard/web-meeting-participants', 'id' => $id]);
        }

        return $this->render('webmeeting_participant', [
            'model' => $this->model,
            'modelSearch' => $modelSearch,
            'dataProvider' => $dataProvider,
            'dataProviderInvitationUsers' => $dataProviderInvitationUsers,
            'countInvitationUsers' => $countInvitationUsers,
            'participants_user_ids' => $participants_user_ids,
            'readOnly' => $readOnly
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionLocationEntrances($id)
    {
        $this->model = $this->findModel($id);
        $this->setMenuSidebar($this->model);

        $eventLocationEntrance = EventLocationEntrances::findOne($this->model->event_location_entrance_id);
        $eventLocation = EventLocation::findOne($this->model->event_location_id);

        $oldLocation = $eventLocation->name;
        $oldEntrance = $eventLocationEntrance->name;
        if ($eventLocation) {
            if ($eventLocationEntrance->load(\Yii::$app->request->post()) && $eventLocationEntrance->validate() && $eventLocation->load(\Yii::$app->request->post()) && $eventLocation->validate()) {
                if ($eventLocation->save() && $eventLocationEntrance->save()) {
                    $landing = $this->model->eventLanding;
                    EventsUtility::updateLuyaPageTimestampForCache($landing);
                    EventChangeAttributesEvent::saveChangeLocationOther($id, $eventLocation, $eventLocationEntrance, $oldLocation, $oldEntrance);

                    \Yii::$app->getSession()->addFlash('success', \Yii::t('amoscore', 'Item updated'));
                    return $this->redirect(['/events/event-dashboard/info-event', 'id' => $this->model->id]);
                } else {
                    \Yii::$app->getSession()->addFlash('danger', \Yii::t('amoscore', 'Item not updated, check data'));
                }
            }

            return $this->render('location_entrances', [
                'model' => $this->model,
                'eventLocationEntrance' => $eventLocationEntrance,
                'eventLocation' => $eventLocation,
            ]);
        } else {
            return $this->redirect(['/events/event-dashboard/info-event', 'id' => $this->model->id]);
        }
    }


    /**
     * @param $id
     * @return Response
     */
    public function actionNewWebmeetingInvitation($id)
    {
        $this->model = $this->findModel($id);
        $this->model->generateInternalListWebmeeting(false);
        \Yii::$app->session->addFlash('success', AmosEvents::t("amosevents", "L'invito è stato creato correttamente"));
        return $this->redirect(['/events/event-dashboard/invite', 'id' => $id]);
    }


    /**
     * @param $id
     * @param null $list_id
     * @param null $communication_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionNominalInvitation($id, $list_id = null, $communication_id = null)
    {
        $this->model = $this->findModel($id);

        $internalList = null;
        if (!empty($list_id)) {
            $internalList = EventInternalList::findOne($list_id);
        }

        $communication = null;
        if (!empty($communication_id)) {
            $communication = EventCommunication::findOne($communication_id);
        }


        if (\Yii::$app->request->isPost) {
            $modelSearch = new SpecificUserEventSearch();
            $modelSearch->load(\Yii::$app->request->post());

            // INSERT NEW PARTICIPANTS
            $users_ids_str = \Yii::$app->request->post('selected_users_ids');
            $users_ids_str_delete = \Yii::$app->request->post('deleted_users_ids');

            $this->model->generateInternalList($modelSearch->invitationName, $users_ids_str, $users_ids_str_delete, $internalList, $communication);

            if ($communication) {
                return $this->redirect(['communications', 'id' => $id]);
            }
            return $this->redirect(['invite', 'id' => $id]);
        }


        return $this->render('nominal_invitation', [
            'model' => $this->model,
            'internalList' => $internalList,
            'communication' => $communication
        ]);
    }




}
