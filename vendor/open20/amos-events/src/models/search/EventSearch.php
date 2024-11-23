<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events
 * @category   CategoryName
 */

namespace open20\amos\events\models\search;

use open20\amos\chat\DataProvider;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\comuni\models\IstatComuni;
use open20\amos\comuni\models\IstatProvince;
use open20\amos\core\interfaces\CmsModelInterface;
use open20\amos\core\interfaces\SearchModelInterface;
use open20\amos\core\record\CachedActiveQuery;
use open20\amos\core\record\CmsField;
use open20\amos\core\record\SearchResult;
use open20\amos\cwh\AmosCwh;
use open20\amos\cwh\query\CwhActiveQuery;
use open20\amos\events\AmosEvents;
use open20\amos\events\models\base\EventRelated;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventLanding;
use open20\amos\events\models\EventLocationEntrances;
use open20\amos\events\models\EventMembershipType;
use open20\amos\events\models\EventPlaces;
use open20\amos\events\models\EventType;
use open20\amos\events\utility\EventsUtility;
use open20\amos\news\models\News;
use open20\amos\news\models\search\NewsSearch;
use open20\amos\notificationmanager\AmosNotify;
use open20\amos\notificationmanager\base\NotifyWidget;
use open20\amos\notificationmanager\base\NotifyWidgetDoNothing;
use open20\amos\notificationmanager\models\NotificationChannels;
use open20\amos\tag\models\Tag;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\console\Application;
//use open20\amos\core\forms\ActiveDataProvider;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\di\Container;
use yii\di\NotInstantiableException;
use yii\helpers\ArrayHelper;

/**
 * Class EventSearch
 * EventSearch represents the model behind the search form about `open20\amos\events\models\Event`.
 * @package open20\amos\events\models\search
 */
class EventSearch extends Event implements SearchModelInterface, CmsModelInterface
{
    public $showHistory = 0;
    public $tagPreference;
    public $showTimePeriods = 0;
    public $currentComune;
    public $day;
    public $tag_id;
    public $multilanguageSearch;
    public $statusEvent;

    public $enableCount = false;

    /**
     * @var Container $container
     */
    private $container;

    /**
     * @inheritdoc
     */
    public function __construct(array $config = [])
    {
        $this->container = new Container();
        $this->container->set('notify', new NotifyWidgetDoNothing());

        parent::__construct($config);
    }

    /**
     * @return object
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function getNotifier()
    {
        return $this->container->get('notify');
    }

    /**
     * @param $notifier
     */
    public function setNotifier(NotifyWidget $notifier)
    {
        $this->container->set('notify', $notifier);
    }

    /**
     * @param ActiveQuery $query
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    private function notificationOff($query)
    {
        $notify = $this->getNotifier();

        if ($notify) {
            /** @var AmosNotify $notify */
            $notify->notificationOff(
                \Yii::$app->getUser()->id, $this->eventsModule->model('Event'),
                $query, NotificationChannels::CHANNEL_READ
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'primo_piano', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [
                [
                    'title',
                    'description',
                    'begin_date_hour',
                    'end_date_hour',
                    'event_type_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'showHistory',
                    'showTimePeriods',
                    'tagPreference',
                    'currentComune',
                    'day',
                    'tag_id',
                    'multilanguageSearch',
                    'statusEvent'
                ],
                'safe'
            ],
            [
                [
                    'begin_date_hour_from',
                    'begin_date_hour_to',
                    'end_date_hour_from',
                    'end_date_hour_to',
                ],
                'safe'
            ],
        ];
    }

    /**
     * bypass scenarios() implementation in the parent class
     *
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     */
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();

        $behaviors = [];
        //if the parent model News is a model enabled for tags, NewsSearch will have TaggableBehavior too
        $moduleTag = \Yii::$app->getModule('tag');
        if (isset($moduleTag) && in_array(
                $this->eventsModule->model('Event'), $moduleTag->modelsEnabled
            ) && $moduleTag->behaviors) {
            $behaviors = ArrayHelper::merge($moduleTag->behaviors, $behaviors);
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * @param $params
     * @return ActiveQuery $query
     */
    public function baseSearch($params)
    {
        //init the default search values
        $this->initOrderVars();

        if (is_string($params)) {
            $params = [$params];
        }

        //check params to get orders value
        $this->setOrderVars($params);

        $module = AmosEvents::instance();
        /** @var Event $eventModel */
        $eventModel = $module->model('Event');

        return $eventModel::find()->distinct();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params, $queryType = null, $limit = null, $onlyDrafts = false, $pageSize = NULL)
    {
        $query = $this->buildQuery($params, $queryType, $onlyDrafts);

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
            ]
        );

//        $dataProvider = $this->searchDefaultOrder($dataProvider);
        $dataProvider->setSort([
            'attributes' => ['begin_date_hour', 'title', 'event_type_id', 'status', 'multilanguage'],
            'defaultOrder' => [
                'begin_date_hour' => SORT_ASC
            ]
        ]);

        if (!empty($params["withPagination"])) {
            $dataProvider->setPagination(['pageSize' => $limit]);
            $query->limit(null);
        } else {
            $query->limit($limit);
        }
        $this->notificationOff($query);

        $query->joinWith('eventType');


        $loaded = $this->load($params);

        if (!in_array($queryType, ['my-registrations', 'my-tickets', 'my-invitations'])) {
            if (!$this->showTimePeriods) {
                $query->andWhere(['event.is_time_period' => 0]);
            }
        }

        // show history or not
//        if(!in_array($queryType,['all-admin','my-group'])){
        if (!$this->showHistory) {
            $query->andWhere(['>', 'event.end_date_hour', new Expression('NOW()')]);
        }

//        }

        if (!($loaded) && $this->validate()) {
            return $dataProvider;
        }


        if ($this->tagPreference) {
            $query->leftJoin("entitys_tags_mm as tag_preference", 'tag_preference.record_id = event.id')
                ->andWhere(['tag_preference.classname' => Event::className()])
                ->andFilterWhere(['tag_preference.tag_id' => $this->tagPreference]);
        }

        if (!empty($this->multilanguageSearch)) {
            $query->andFilterWhere([self::tableName() . '.multilanguage' => $this->multilanguageSearch]);
        }

        $query
            ->andFilterWhere(
                [
                    self::tableName() . '.id' => $this->id,
                    self::tableName() . '.event_type_id' => $this->event_type_id,
                    self::tableName() . '.created_at' => $this->created_at,
                    self::tableName() . '.updated_at' => $this->updated_at,
                    self::tableName() . '.deleted_at' => $this->deleted_at,
                    self::tableName() . '.created_by' => $this->created_by,
                    self::tableName() . '.updated_by' => $this->updated_by,
                    self::tableName() . '.deleted_by' => $this->deleted_by,
                ]
            )
            ->andFilterWhere(['like', self::tableName() . '.title', $this->title])
            ->andFilterWhere(['like', self::tableName() . '.description', $this->description])
            ->andFilterWhere(['>=', self::tableName() . '.begin_date_hour', $this->begin_date_hour])
            ->andFilterWhere(['<=', self::tableName() . '.end_date_hour', $this->end_date_hour])
            ->andFilterWhere(['=', self::tableName() . '.status', $this->statusEvent]);

        return $dataProvider;
    }

    /**
     * Search for events created by the logged user
     *
     * @param array $params $_GET search parametrs
     * @param int|null $limit Query limit
     * @return ActiveDataProvider
     */
    public function searchCreatedBy($params, $limit = null)
    {
        return $this->search($params, 'created-by', $limit);
    }

    /**
     * @param ActiveQuery $query
     */
    private function filterByMembershipType($query)
    {
        $loggedUserId = Yii::$app->getUser()->id;
        $query
            ->leftJoin(
                'community_user_mm',
                'community_user_mm.community_id = event.community_id AND community_user_mm.user_id=' . $loggedUserId
            )
            ->andWhere(['OR',
                ['!=', 'event_type.event_type', EventType::TYPE_UPON_INVITATION],
                ['AND', ['event_type.event_type' => EventType::TYPE_UPON_INVITATION], ['community_user_mm.user_id' => $loggedUserId], ['community_user_mm.deleted_at' => null]]
            ]);
//        pr($query->createCommand()->rawSql);
    }

    /**
     * Search for all events
     *
     * @param array $params $_GET search parametrs
     * @param int|null $limit Query limit
     * @return ActiveDataProvider
     */
    public function searchAllEvents($params = [], $limit = null)
    {
        return $this->search($params, 'all', $limit);
    }

    /**
     * Search for all events
     *
     * @param array $params $_GET search parametrs
     * @param int|null $limit Query limit
     * @return ActiveDataProvider
     */
    public function searchAllAdmin($params, $limit = null)
    {
        return $this->search($params, 'all-admin', $limit);
    }

    /**
     * Search for events visible by the logged user and published on the calendar
     *
     * @param array $params $_GET search parametrs
     * @param int|null $limit Query limit
     * @return ActiveDataProvider
     */
    public function searchCalendarView($params, $limit = null)
    {
        return $this->search($params, 'own-interest', $limit);
    }

    /**
     * Search for events that by the logged user has permission to validate
     *
     * @param array $params $_GET search parametrs
     * @param int|null $limit Query limit
     * @return ActiveDataProvider
     */
    public function searchToPublish($params, $limit = null)
    {
        return $this->search($params, 'to-validate', $limit);
    }

    /**
     * Search for events where the the logged user is part of the staff
     *
     * @param array $params $_GET search parametrs
     * @param int|null $limit Query limit
     * @return ActiveDataProvider
     */
    public function searchManagement($params, $limit = null)
    {
        return $this->search($params, 'all', $limit);
    }

    /**
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function searchMyGroup($params, $limit = null)
    {
        return $this->search($params, 'my-group', $limit);
    }

    /**
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function searchMyInvitations($params, $limit = null)
    {
        return $this->search($params, 'my-invitations', $limit);
    }

    /**
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function searchMyRegistrations($params, $limit = null)
    {
        return $this->search($params, 'my-registrations', $limit);
    }

    /**
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function searchMyTickets($params, $limit = null)
    {
        return $this->search($params, 'my-tickets', $limit);
    }

    /**
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function searchPublished($params, $limit = null)
    {
        return $this->search($params, 'published', $limit);
    }


    /**
     * @param string $queryType
     * @param array $params
     * @return ActiveQuery $query
     */
    public function buildQuery($params, $queryType, $onlyDrafts = false)
    {
        $user_id = \Yii::$app->user->id;
        if (!empty($params['user_id'])) {
            $user_id = $params['user_id'];
        }

        $moduleEvents = \Yii::$app->getModule('events');
        $query = $this->baseSearch($params);
        $classname = $this->eventsModule->model('Event');

        /** @var  AmosCwh $moduleCwh */
        $moduleCwh = \Yii::$app->getModule('cwh');
        $cwhActiveQuery = null;

        $isSetCwh = isset($moduleCwh) && in_array($classname,
                $moduleCwh->modelsEnabled);
        if ($isSetCwh) {
            $moduleCwh->setCwhScopeFromSession();
            $cwhActiveQuery = new CwhActiveQuery(
                $classname,
                [
                    'queryBase' => $query
                ]
            );
        }

        switch ($queryType) {
            case 'my-group':
                $group = EventsUtility::getCurrentDg();
                if ($group) {
                    $query->innerJoin('event_group_referent', 'event_group_referent.id = event.event_group_referent_id')
                        ->andWhere(['is', 'event_group_referent.deleted_at', null])
                        ->andWhere(['event_group_referent.id' => $group->id]);
                } else {
                    $query->andWhere(0);
                }
                break;
            case 'my-registrations':
                $query->innerJoin('community_user_mm', 'community_user_mm.community_id = event.community_id')
                    ->innerJoin('event_invitation', 'event_invitation.event_id = event.id')
                    ->andWhere(['community_user_mm.status' => CommunityUserMm::STATUS_ACTIVE])
                    ->andWhere(['is', 'community_user_mm.deleted_at', null])
                    ->andWhere(['community_user_mm.user_id' => $user_id])
                    ->andWhere(['event_invitation.user_id' => $user_id])
                    ->andWhere(['!=', 'event.status', Event::EVENTS_WORKFLOW_STATUS_SUSPENDED]);
                break;
            case 'my-tickets':
                $query->innerJoin('community_user_mm', 'community_user_mm.community_id = event.community_id')
                    ->innerJoin('event_invitation', 'event_invitation.event_id = event.id')
                    ->andWhere(['community_user_mm.status' => CommunityUserMm::STATUS_ACTIVE])
                    ->andWhere(['is', 'community_user_mm.deleted_at', null])
                    ->andWhere(['community_user_mm.user_id' => \Yii::$app->user->id])
                    ->andWhere(['event_invitation.user_id' => \Yii::$app->user->id])
                    ->andWhere(['event.has_tickets' => 1])
                    ->andWhere(['OR',
                        ['AND', ['event.seats_management' => 1], ['event_invitation.is_ticket_sent' => 1]],
                        ['event.seats_management' => 0],
                    ])
                    ->andWhere(['!=', 'event.status', Event::EVENTS_WORKFLOW_STATUS_SUSPENDED]);
//                    ->andWhere(['event_invitation.is_ticket_sent' => 1]);
                break;
            case 'my-invitations':
                $query2 = clone $query;
                $query2
                    ->select('event.id')
                    ->innerJoin('community_user_mm', 'community_user_mm.community_id = event.community_id')
                    ->andWhere(['community_user_mm.user_id' => \Yii::$app->user->id])
                    ->andWhere(['is', 'community_user_mm.deleted_at', null])
                    ->andWhere(['community_user_mm.status' => CommunityUserMm::STATUS_ACTIVE])
                    ->andWhere(['!=', 'event.status', Event::EVENTS_WORKFLOW_STATUS_SUSPENDED]);
//                pr($query2->createCommand()->rawSql);
//                pr($query->createCommand()->rawSql);die;

                $query->innerJoinWith('eventInternalLists.eventInvitationSent')
                    ->andWhere(['event_invitation_sent.user_id' => \Yii::$app->user->id])
                    ->andWhere(['is', 'event_invitation_sent.deleted_at', null])
                    ->andWhere(['not in', 'event.id', $query2])
                    ->andWhere(['!=', 'event.status', Event::EVENTS_WORKFLOW_STATUS_SUSPENDED]);
//                pr($query->createCommand()->rawSql);die;
                break;
            case 'created-by':
                if ($isSetCwh) {
                    $query = $cwhActiveQuery->getQueryCwhOwn();
                } else {
                    $query->andFilterWhere(
                        [
                            self::tableName() . '.created_by' => Yii::$app->getUser()->id
                        ]
                    );
                }
                break;
            case 'all':
                if ($moduleEvents && $moduleEvents->enableNewWizard) {
                    $query->andWhere(
                        [
                            self::tableName() . '.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED,
                        ]
                    );
                    $this->filterByMembershipType($query);
                } else if ($isSetCwh) {
                    $query = $cwhActiveQuery->getQueryCwhAll();
                } else {
                    $query->andWhere(
                        [
                            self::tableName() . '.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED,
                        ]
                    );
                }
                break;
            case'to-validate':
                if ($isSetCwh) {
                    $query = $cwhActiveQuery->getQueryCwhToValidate();
                    $this->filterByMembershipType($query);
                }
                $query->andWhere(
                    [
                        self::tableName() . '.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHREQUEST,
                    ]
                );
                break;
            case'all-admin':
                // show all
                break;
            case 'own-interest':
                if ($isSetCwh) {
                    $query = $cwhActiveQuery->getQueryCwhOwnInterest();
                    $this->filterByMembershipType($query);
                    $query->andFilterWhere(
                        [
                            'validated_at_least_once' => true,
                            'publish_in_the_calendar' => true,
                            'visible_in_the_calendar' => true
                        ]
                    )->andWhere(['!=', 'event.status', Event::EVENTS_WORKFLOW_STATUS_SUSPENDED]);
                } else {
                    $query->andWhere(
                        [
                            self::tableName() . '.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED,
                        ]
                    )->andWhere(['!=', 'event.status', Event::EVENTS_WORKFLOW_STATUS_SUSPENDED]);
                }
                break;
            case 'published':
                $this->getQueryPublished($query);
                break;
        }


        if ($onlyDrafts) {
            $query->joinWith('communityUserMm');

            // MANAGEMENT
            $query->andWhere(
                [
                    'community_user_mm.user_id' => \Yii::$app->getUser()->id,
                    'community_user_mm.status' => CommunityUserMm::STATUS_ACTIVE,
                    'community_user_mm.role' => self::EVENT_MANAGER,
                    'validated_at_least_once' => true,
                    'event_management' => true,
                ]
            );
        }

//        pr($query->createCommand()->rawSql);
        return $query;
    }

    /**
     * @param $query ActiveQuery
     * @param bool $checkDateEnd
     */
    public function getQueryPublished($query, $checkDateEnd = true)
    {
        $now = date('Y-m-d H:i:s');
        $query->innerJoin('event_type et', 'et.id = event.event_type_id')
            ->andWhere(['!= ', 'et.event_type', EventType::TYPE_UPON_INVITATION])
            ->andWhere([
                'event.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED,
            ])
            ->andWhere(['event.event_id' => null])
            ->andWhere(['<=', 'publication_date_begin', $now])
            ->andWhere(['or',
                    ['>=', 'publication_date_end', $now],
                    ['publication_date_end' => null]]
            );

        if ($checkDateEnd) {
            $query->andWhere(['or',
                    ['>=', 'end_date_hour', $now],
                    ['end_date_hour' => null]]
            );
        }
    }

    /**
     * @param $query ActiveQuery
     * @param bool $checkDateEnd
     */
    public function getQueryClosed($query)
    {
        $now = date('Y-m-d H:i:s');
        $query->innerJoin('event_type et', 'et.id = event.event_type_id')
            ->andWhere(['!= ', 'et.event_type', EventType::TYPE_UPON_INVITATION])
            ->andWhere([
                'event.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED,
            ])
            ->andWhere(['event.event_id' => null])
            ->andWhere(['<', 'end_date_hour', $now]);


    }


    /**
     * Search method useful to retrieve events in validated status with both flags
     * publish_in_the_calendar and visible_in_the_calendar true
     *
     * @param array $params Array di parametri
     * @return ActiveDataProvider
     */
    public function searchHighlightedAndHomepageEvents($params)
    {
        $query = $this->highlightedAndHomepageEventsQuery($params);

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                        'publication_date_begin' => SORT_DESC,
                    ],
                ],
            ]
        );

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return ActiveQuery
     */
    public function highlightedAndHomepageEventsQuery($params)
    {
        $tableName = $this->tableName();

        return $this->baseSearch($params)
            ->where([])
            ->andWhere(
                [
                    $tableName . '.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED,
                    $tableName . '.in_evidenza' => 1,
                    $tableName . '.primo_piano' => 1,
                    $tableName . '.deleted_at' => null,
                    $tableName . '.publish_in_the_calendar' => 1,
                    $tableName . '.visible_in_the_calendar' => 1
                ]
            );
    }

    /**
     * Search all validated documents
     *
     * @param array $searchParamsArray Array of search words
     * @param int|null $pageSize
     * @return ActiveDataProvider
     */
    public function globalSearch($searchParamsArray, $pageSize = 5)
    {
        $dataProvider = $this->search([], 'all', null);
        $pagination = $dataProvider->getPagination();
        if (!$pagination) {
            $pagination = new Pagination();
            $dataProvider->setPagination($pagination);
        }
        $pagination->setPageSize($pageSize);

        // Verifico se il modulo supporta i TAG e, in caso, ricerco anche fra quelli
        $moduleTag = \Yii::$app->getModule('tag');
        $enableTagSearch = isset($moduleTag) && in_array(
                $this->eventsModule->model('Event'), $moduleTag->modelsEnabled
            );

        if ($enableTagSearch) {
            $dataProvider->query->leftJoin(
                'entitys_tags_mm e_tag',
                "e_tag.record_id=event.id AND e_tag.deleted_at IS NULL AND e_tag.classname='" . addslashes(
                    $this->eventsModule->model('Event')
                ) . "'"
            );

            if (Yii::$app->db->schema->getTableSchema('tag__translation')) {
                // Esiste la tabella delle traduzioni dei TAG. Uso quella per la ricerca
                $dataProvider->query->leftJoin('tag__translation tt',
                    "e_tag.tag_id=tt.tag_id");
                $tagTranslationSearch = true;
            }

            $dataProvider->query->leftJoin('tag t', "e_tag.tag_id=t.id");
        }

        foreach ($searchParamsArray as $searchString) {
            $orQueries = [
                'or',
                ['like', self::tableName() . '.title', $searchString],
                ['like', self::tableName() . '.summary', $searchString],
                ['like', self::tableName() . '.description', $searchString],
                ['like', self::tableName() . '.event_location', $searchString],
                ['like', self::tableName() . '.event_address', $searchString],
                ['like', self::tableName() . '.event_address_house_number', $searchString],
                ['like', self::tableName() . '.event_address_cap', $searchString],
            ];

            if ($enableTagSearch) {
                if ($tagTranslationSearch) {
                    $orQueries[] = ['like', 'tt.nome', $searchString];
                }
                $orQueries[] = ['like', 't.nome', $searchString];
            }

            $dataProvider->query->andWhere($orQueries);
        }

        $searchModels = [];
        foreach ($dataProvider->models as $m) {
            array_push($searchModels, $this->convertToSearchResult($m));
        }
        $dataProvider->setModels($searchModels);

        return $dataProvider;
    }

    /**
     * @param object $model The model to convert into SearchResult
     * @return SearchResult
     */
    public function convertToSearchResult($model)
    {
        $searchResult = new SearchResult();
        $searchResult->url = $model->getFullViewUrl();
        $searchResult->box_type = "image";
        $searchResult->id = $model->id;
        $searchResult->titolo = $model->title;
        $searchResult->data_pubblicazione = $model->publication_date_begin;
        $searchResult->immagine = $model->getEventLogo();
        $searchResult->abstract = $model->summary;

        return $searchResult;
    }
    /**
     * CmsModelInterface
     * */

    /**
     * get the query used by the related searchHomepageNews method
     * return just the query in case data provider/query itself needs editing
     *
     * @param array $params
     * @return ActiveQuery
     */
    public function homepageEventQuery($params)
    {
        $now = date('Y-m-d');
        $tableName = $this->tableName();
        $query = $this->baseSearch($params)
            ->andWhere([
                $tableName . '.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED,
                $tableName . '.primo_piano' => 1
            ])
            ->andWhere(['<=', 'publication_date_begin', $now])
            ->andWhere(['or',
                    ['>=', 'publication_date_end', $now],
                    ['publication_date_end' => null]]
            );

        return $query;
    }

    /**
     * Search method useful to retrieve news to show in frontend (with cms)
     *
     * @param $params
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function cmsSearch($params, $limit = null)
    {
        $params = array_merge($params, Yii::$app->request->get());
        $this->load($params);
        $query = $this->homepageEventQuery($params);
        $this->applySearchFilters($query);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'publication_date_begin' => SORT_DESC,
                ],
            ],
        ]);

        if (!empty($params["withPagination"])) {
            $dataProvider->setPagination(['pageSize' => $limit]);
            $query->limit(null);
        } else {
            $query->limit($limit);
        }

        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $query->andWhere(eval("return " . $command . ";"));
            }
        }

        return $dataProvider;
    }

    /**
     *
     * @return array
     */
    public function cmsViewFields()
    {
        $viewFields = [];
        array_push($viewFields,
            new CmsField("title", "TEXT", 'amosevents',
                $this->attributeLabels()["title"]));
        array_push($viewFields,
            new CmsField("summary", "TEXT", 'amosevents',
                $this->attributeLabels()['summary']));
        array_push(
            $viewFields,
            new CmsField("eventLogo", "IMAGE", 'amosevents',
                $this->attributeLabels()['eventLogo'])
        );
        array_push(
            $viewFields,
            new CmsField("begin_date_hour", "DATE", 'amosevents',
                $this->attributeLabels()['begin_date_hour'])
        );
        array_push(
            $viewFields,
            new CmsField("end_date_hour", "DATE", 'amosevents',
                $this->attributeLabels()['end_date_hour'])
        );

        return $viewFields;
    }

    /**
     *
     * @return array
     */
    public function cmsSearchFields()
    {
        $searchFields = [];

        array_push($searchFields, new CmsField("title", "TEXT"));
        array_push($searchFields, new CmsField("summary", "TEXT"));
        array_push($searchFields, new CmsField("begin_date_hour", "DATE"));

        return $searchFields;
    }

    /**
     *
     * @param type $id
     * @return boolean
     */
    public function cmsIsVisible($id)
    {
        $retValue = true;
        if (isset($id)) {
            $md = $this->findOne($id);
            if (!is_null($md)) {
                $retValue = $md->visible_in_the_calendar;
            }
        }

        return $retValue;
    }

    /**
     * @param array $params
     * @param int $limit
     * @return ActiveQuery
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function ultimeEventsQuery($params, $limit)
    {
        $query = $this->buildQuery($params, 'own-interest', false);
        $this->notificationOff($query);
        $query
            ->joinWith('eventType')
            ->andFilterWhere(
                [
                    'id' => $this->id,
                    'event_type_id' => $this->event_type_id,
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                    'deleted_at' => $this->deleted_at,
                    'created_by' => $this->created_by,
                    'updated_by' => $this->updated_by,
                    'deleted_by' => $this->deleted_by,
                ]
            )
            ->andFilterWhere(['like', self::tableName() . '.title', $this->title])
            ->andFilterWhere(['like', self::tableName() . '.description', $this->description])
            ->andFilterWhere(['>=', self::tableName() . '.begin_date_hour', $this->begin_date_hour])
            ->andFilterWhere(['<=', self::tableName() . '.end_date_hour', $this->end_date_hour]);
        return $query;
    }

    /**
     * Method that search the latest research events validated, typically limit is $ 3.
     *
     * @param array $params
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function ultimeEvents($params, $limit)
    {
        $query = $this->ultimeEventsQuery($params, $limit);
        $provider = new ActiveDataProvider(
            [
                'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                        'begin_date_hour' => SORT_DESC,
                    ]
                ],
            ]
        );
        return $provider;
    }

    /**
     *
     * @param array $params
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function cmsSearchNews($params, $limit = null)
    {
        $queryParams = [];
        $dataProvider = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);
            //EventsUtility::setScope($model->community_id);
            $eventLanding = $model->eventLanding;
            if ($eventLanding) {
                $dataProvider = new ActiveDataProvider([
                    'query' => $eventLanding->getNews()->andWhere(['status' => News::NEWS_WORKFLOW_STATUS_VALIDATO])
                ]);
            } else {
                $dataProvider = new ActiveDataProvider([
                    'query' => News::find()->andWhere(0)
                ]);
            }
            $query = $dataProvider->query;
            if (count($queryParams) > 0) {
                foreach ($queryParams as $key => $param) {
                    $query->andWhere([$key => $param]);
                }
            }
        }

        return $dataProvider;
    }

    /**
     * @param $params
     * @param null $limit
     * @return null|ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function cmsSearchDocuments($params, $limit = null)
    {
        $queryParams = [];
        $dataProvider = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);
            // EventsUtility::setScope($model->community_id);

            $eventLanding = $model->eventLanding;
            if ($eventLanding) {
                $dataProvider = new ActiveDataProvider([
                    'query' => $eventLanding->getDocuments()
                ]);
            } else {
                $dataProvider = new ActiveDataProvider([
                    'query' => \open20\amos\documenti\models\Documenti::find()->andWhere(0)
                ]);
            }
            $query = $dataProvider->query;
//            pr($query->createCommand()->rawSql);die;

            if (count($queryParams) > 0) {
                foreach ($queryParams as $key => $param) {
                    $query->andWhere([$key => $param]);
                }
            }
        }

        return $dataProvider;
    }

    /**
     *
     * @param array $params
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function cmsSearchProtagonists($params, $limit = null)
    {
        $queryParams = [];
        $dataProvider = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);
            $protagonists = new EventLandingProtagonistSearch();
            $dataProvider = $protagonists->search($params);
            $query = $dataProvider->query;
            $query->andWhere(['event_landing_id' => $model->eventLanding->id]);
            if (count($queryParams) > 0) {
                foreach ($queryParams as $key => $param) {
                    $query->andWhere([$key => $param]);
                }
            }
        }
        return $dataProvider;
    }

    /**
     *
     * @param array $params
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function cmsSearchRelatedEvents($params, $limit = null)
    {
        $queryParams = [];
        $dataProvider = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }

        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);

            $dataProvider = new ActiveDataProvider([
                'query' => $model->getCorrelatedEvents()
            ]);
        }
        return $dataProvider;
    }

    /**
     *
     * @param array $params
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function cmsSearchGallery($params, $limit = null)
    {
        $queryParams = [];
        $dataProviderSliderElemImage = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);
            EventsUtility::setScope($model->community_id);
            $slider = $model->eventLanding->imageSlider;
            if (!is_null($slider)) {
                $dataProviderSliderElemImage = new ActiveDataProvider(['query' => $slider->getSliderElems()->orderBy('order ASC')]);
            }
        }

        return $dataProviderSliderElemImage;
    }

    /**
     *
     * @param array $params
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function cmsSearchVideo($params, $limit = null)
    {
        $queryParams = [];
        $dataProviderSliderElemVideo = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);
            EventsUtility::setScope($model->community_id);
            $sliderVideo = $model->eventLanding->videoSlider;
            if (!is_null($sliderVideo)) {
                $dataProviderSliderElemVideo = new ActiveDataProvider(['query' => $sliderVideo->getSliderElems()->orderBy('order ASC')]);
            }
        }

        return $dataProviderSliderElemVideo;
    }

    /**
     * @param $params
     * @return ActiveQuery
     */
    public function searchMyRegistrationQuery($params)
    {
        return $this->buildQuery($params, 'my-registrations');

    }

    /**
     *
     * @param array $params
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function cmsSearchInstagramVideo($params, $limit = null)
    {
        $queryParams = [];
        $dataProviderSliderElemInstagramVideo = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);
            EventsUtility::setScope($model->community_id);
            $sliderInstagramVideo = $model->eventLanding->instagramVideoSlider;
            if (!is_null($sliderInstagramVideo)) {
                $dataProviderSliderElemInstagramVideo = new ActiveDataProvider(['query' => $sliderInstagramVideo->getSliderElems()->orderBy('order ASC')]);
            }
        }

        return $dataProviderSliderElemInstagramVideo;
    }

    /**
     *
     * @param array $params
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function cmsSearchRating($params, $limit = null)
    {
        $queryParams = [];
        $dataProviderRating = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);
            EventsUtility::setScope($model->community_id);
            $event = $model->eventLanding;
            if (!is_null($event)) {
                $dataProviderRating = new ActiveDataProvider(['query' => $event]);
            }
        }

        return $dataProviderRating;
    }

    /**
     * @return null|ArrayDataProvider
     * @throws InvalidConfigException
     */
    public function cmsMarkerMapSearch($params, $limit = null)
    {
        $queryParams = [];
        $dataProviderEvents = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);

        if (!is_null($model)) {
            // get coordinates of current event
            unset($queryParams["event_id"]);
            $place = $model->eventLocation->eventPlaces;
            $parentCoord = [
                'lat' => $place->latitude,
                'lng' => $place->longitude
            ];
            $coordinates [] = [
                'lat' => $place->latitude,
                'lng' => $place->longitude,
                'model' => $model,
            ];

            // get coordinates of event children
            $children = Event::find()
                ->andWhere(['event_id' => $model->id])
                ->andWhere(['is_time_period' => 0])
                ->andWhere(['status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED])
                ->orderBy('begin_date_hour ASC')->all();
            foreach ($children as $eventChild) {
                $placeChild = $eventChild->eventLocation->eventPlaces;
                if ($placeChild->latitude != $parentCoord['lat'] && $placeChild->longitude != $parentCoord['lng']) {
                    $coordinates [] = [
                        'lat' => $placeChild->latitude,
                        'lng' => $placeChild->longitude,
                        'model' => $eventChild
                    ];
                }

            }
//pr($coordinates);

            $dataProviderMark = new ArrayDataProvider([
                'allModels' => $coordinates
            ]);
        }
        return $dataProviderMark;
    }


    /**
     * @return null|ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function cmsChildrenSearch($params, $limit = null)
    {
        $queryParams = [];
        $dataProviderEvents = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);
            $dataProviderEvents = new ActiveDataProvider([
                'query' => EventsUtility::getChildrenEvent($model->id)
            ]);
        }
        return $dataProviderEvents;
    }

    /**
     * @return null|ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function cmsEntrancesSearch($params, $limit = null)
    {
        $queryParams = [];
        $dataProviderEvents = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = EventLocationEntrances::findOne(['id' => $queryParams['event_location_entrance_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_location_entrance_id"]);
            $query = $this->baseSearch($params);
            $this->getQueryPublished($query);
            $query
                ->andWhere(['event_location_entrance_id' => $model->id])
                ->orderBy('begin_date_hour ASC');

            $dataProviderEvents = new ActiveDataProvider([
                'query' => $query
            ]);
        }
        return $dataProviderEvents;
    }

    /**
     * @return null|ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function cmsTimePeriodsSearch($params, $limit = null)
    {
        $queryParams = [];
        $dataProviderEvents = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);
            $dataProviderEvents = new ActiveDataProvider([
                'query' => Event::find()
                    ->andWhere(['event_id' => $model->id])
                    ->andWhere(['is_time_period' => 1])
                    ->andWhere(['status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED])
                    ->orderBy('begin_date_hour ASC')]);
        }
        return $dataProviderEvents;
    }

    /**
     * @param $params
     * @param null $limit
     * @return null|ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function cmsStreamingSearch($params, $limit = null)
    {
        $queryParams = [];
        $dataProviderEvents = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);
            $dataProviderEvents = new ActiveDataProvider(['query' => EventLanding::find()->andWhere(['event_id' => $model->id])]);
        }
        return $dataProviderEvents;
    }

    /**
     * @param $params
     * @param null $limit
     * @return null|ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function cmsShareSearch($params, $limit = null)
    {
        $queryParams = [];
        $dataProviderEvents = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);
            $dataProviderEvents = new ActiveDataProvider(['query' => EventLanding::find()->andWhere(['event_id' => $model->id])]);
        }
        return $dataProviderEvents;
    }

    /**
     * @param $params
     * @param null $limit
     * @return null|ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function cmsChangeLanguageSearch($params, $limit = null)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Event::find()->andWhere(0)
        ]);
        return $dataProvider;
    }

    /**
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function cmsTodayInStreaming($params, $limit = null)
    {
        $query = EventLanding::find()
            ->innerJoin('event', 'event.id = event_landing.event_id')
            ->andWhere(['event.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED])
//            ->andWhere(['IS NOT','event_landing.streaming_url', null])
//            ->andWhere(['!=','event_landing.streaming_url', ''])
            ->andWhere(['<=', 'date_begin_streaming', new Expression('NOW()')])
            ->andWhere(['>', 'end_date_hour', new Expression('NOW()')])
            ->andWhere(['event.deleted_at' => null])
            ->orderBy('date_begin_streaming DESC')->limit(4);
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        return $dataProvider;
    }

    /**
     * @param $params
     * @param null $limit
     * @return null|ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function cmsContactInfoOrganizatorSearch($params, $limit = null)
    {
        $queryParams = [];
        $dataProviderEvents = null;
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = ArrayHelper::merge($queryParams,
                    eval("return " . $command . ";"));
            }
        }
        $model = $this->findOne(['id' => $queryParams['event_id']]);
        if (!is_null($model)) {
            unset($queryParams["event_id"]);
            $dataProviderEvents = new ActiveDataProvider(['query' => EventLanding::find()->andWhere(['event_id' => $model->id])]);
        }
        return $dataProviderEvents;

    }

    /**
     * Search method useful to retrieve news to show in frontend (with cms)
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider | array
     * @throws \yii\db\Exception
     */
    public function cmsPublishedSearch($params, $limit = null)
    {
        date_default_timezone_set('Europe/Rome');

        if (\Yii::$app instanceof Application) {
            $get = [];
        } else {
            $get = Yii::$app->request->get();
        }
        $params = array_merge($params, $get);
        $this->load($params);

        $query = $this->baseSearch($params);
        $this->eventForDateFrontendQuery($query, $params);
//        if (!$this->enableCount && empty($_GET['day']) && empty($_GET['currentComune']) && empty($_GET['tag_id'])) {

//        } else {
//            $query->orderBy('begin_date_hour DESC');
//        }


        $this->applyFilterCurrentProvincia($query);

        if ($this->enableCount) {
            $query->select(new Expression("tag.id as 'id', tag.nome as 'name', count(*) as 'count'"));
            return $this->getSearchCountForTags($query);
        } else {
            $query->select(new Expression('event.*, IF(event_highlights.n_order is NULL, 9999999, event_highlights.n_order) as n'));
            $query->leftJoin('event_highlights', 'event_highlights.event_id = event.id')
                ->orderBy('n, event.begin_date_hour ASC');
            $this->applyFilterPreferenceTags($query, $params);
        }

        $this->applySearchFilters($query);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'sort' => [
//                'defaultOrder' => [
//                    'begin_date_hour' => SORT_ASC,
//                ],
//            ],
        ]);
        if (!empty($params["withPagination"])) {
            $dataProvider->setPagination(['pageSize' => $limit]);
            $query->limit(null);
        } else {
            $query->limit($limit);
        }

        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $query->andWhere(eval("return " . $command . ";"));
            }
        }
//        $cachedQuery = CachedActiveQuery::instance($query);
//        $cachedQuery->cache();
//        $dataProvider->query = $cachedQuery;
        return $dataProvider;
    }

    /**
     * @param $query ActiveQuery
     * @return mixed
     */
    public function getSearchCountForTags($query)
    {
        $root = Tag::find()->andWhere(['codice' => self::ROOT_TAG_PREFERENCE_CENTER])->one();

        $query->leftJoin("entitys_tags_mm as tag_preference", 'tag_preference.record_id = event.id')
            ->leftJoin('tag', 'tag_preference.tag_id = tag.id')
            ->andWhere(['tag_preference.deleted_at' => null])
            ->andWhere(['tag_preference.classname' => Event::className()])
            ->andWhere(['tag.root' => $root->id]);
        $query->groupBy('tag_preference.tag_id');
        $res = $query->createCommand()->queryAll();
        return $res;
    }

    /**
     * @param $query ActiveQuery
     */
    public function applyFilterPreferenceTags($query, $params = [])
    {
        if (!empty($params['tag_id'])) {
            $tag_id = $params['tag_id'];
            if ($tag_id == 'all') {
                $tag_id = null;
            }
            $query->leftJoin("entitys_tags_mm as tag_preference", 'tag_preference.record_id = event.id')
                ->andWhere(['tag_preference.classname' => Event::className()])
                ->andWhere(['tag_preference.deleted_at' => null])
                ->andFilterWhere(['tag_preference.tag_id' => $tag_id]);
        }
//pr($query->createCommand()->rawSql);
    }

    /**
     * @param $query ActiveQuery
     */
    public function applyFilterCurrentComune($query)
    {
        if (!empty($_GET['currentComune'])) {
            $comune = IstatComuni::findOne($_GET['currentComune']);
            $places = EventPlaces::find()->andWhere(['city' => $comune->nome])->all();
            $places_id = [];
            foreach ($places as $place) {
                $places_id [] = $place->place_id;
            }
            if ($comune) {
                $query->innerJoin('event_location', 'event.event_location_id = event_location.id')
                    ->andWhere(['event_location.event_place_id' => $places_id]);
            }
        }
    }

    /**
     * @param $query ActiveQuery
     */
    public function applyFilterCurrentProvincia($query)
    {
        if (!empty($_GET['currentComune'])) {
            $provincia = IstatProvince::findOne($_GET['currentComune']);
            $places = EventPlaces::find()->andWhere(['province' => $provincia->sigla])->all();
            $places_id = [];
            foreach ($places as $place) {
                $places_id [] = $place->place_id;
            }
            if ($provincia) {
                $query->innerJoin('event_location', 'event.event_location_id = event_location.id')
                    ->andWhere(['event_location.event_place_id' => $places_id]);
            } else {
//                $query->andWhere(0);
            }
        }
    }

    /**
     * @param $query ActiveQuery
     */
    public function eventForDateFrontendQuery($query, $params)
    {
        $day = $params['day'];
        if (empty($day)) {
            $day = 'highlights';
        }
        if (!empty($day)) {
            switch ($day) {
                case 'all':
                    $this->getQueryPublished($query, true);
                    break;
                case 'highlights':
                    $this->getQueryPublished($query, true);
                    $query->innerJoin("event_highlights as evidenza", 'evidenza.event_id = event.id');
                    break;
                case 'closed':
                    $this->getQueryClosed($query);
                    break;
                case 'today':
                    $this->getQueryPublished($query, false);
                    $query->andWhere(['<=', new Expression("DATE(event.begin_date_hour)"), new Expression("date(NOW())")]);
                    $query->andWhere(['OR',
                        ['>=', new Expression("DATE(event.end_date_hour)"), new Expression("date(NOW())")],
                        ["event.end_date_hour" => null],
                    ]);
                    break;
                case 'next_weekend':
                    $this->getQueryPublished($query, false);
                    $now = new \DateTime();
                    $now->modify('next saturday');
                    $saturday = $now->format('Y-m-d');
                    $now->modify('next sunday');
                    $sunday = $now->format('Y-m-d');

                    $query->andWhere(['OR',
                        ['AND',
                            ['<=', new Expression("DATE(event.begin_date_hour)"), $saturday],
                            ['OR', ['>=', new Expression("DATE(event.end_date_hour)"), $saturday], ["event.end_date_hour" => null]]
                        ],
                        ['AND',
                            ['<=', new Expression("DATE(event.begin_date_hour)"), $sunday],
                            ['OR', ['>=', new Expression("DATE(event.end_date_hour)"), $sunday], ["event.end_date_hour" => null]]
                        ],
                    ]);
                    break;
                case 'this_week':
                    $this->getQueryPublished($query, false);
                    $now = new \DateTime();
                    $now->modify('monday this week');
                    $dateStart = $now->format('Y-m-d');
                    $now->modify('sunday this week');
                    $dateEnd = $now->format('Y-m-d');
//                    pr($dateStart, 'START');
//                    pr($dateEnd, 'END');

//                    $query->andWhere(['>=', new Expression("DATE(event.begin_date_hour)"), $dateStart]);
//                    $query->andWhere(['<=', new Expression("DATE(event.begin_date_hour)"), $dateEnd]);

                    $query->andWhere(
                        ['OR',
                            ['AND',
                                ['<=', new Expression("DATE(event.begin_date_hour)"), $dateStart],
                                ['>=', new Expression("DATE(event.end_date_hour)"), $dateStart]
                            ],
                            ['AND',
                                ['>=', new Expression("DATE(event.begin_date_hour)"), $dateStart],
                                ['<=', new Expression("DATE(event.begin_date_hour)"), $dateEnd]
                            ]
                        ]
                    );

//                    $query->andWhere(['OR',
//                        ['>=', new Expression("DATE(event.end_date_hour)"), $dateEnd],
//                        ["event.end_date_hour" => null]
//                    ]);

                    break;
                case 'this_month':
                    $this->getQueryPublished($query, false);
                    $now = new \DateTime();
                    $now->modify('first day of this month');
                    $startMonth = $now->format('Y-m-d');
                    $now->modify('+1 month');
                    $endMonth = $now->format('Y-m-d');
//                    pr($startMonth, 'START');
//                    pr($endMonth, 'END');

//                    $query->andWhere(['>=', new Expression("DATE(event.begin_date_hour)"), $startMonth]);
//                    $query->andWhere(['<', new Expression("DATE(event.begin_date_hour)"), $endMonth]);


                    $query->andWhere(
                        ['OR',
                            ['AND',
                                ['<=', new Expression("DATE(event.begin_date_hour)"), $startMonth],
                                ['>=', new Expression("DATE(event.end_date_hour)"), $startMonth]
                            ],
                            ['AND',
                                ['>=', new Expression("DATE(event.begin_date_hour)"), $startMonth],
                                ['<=', new Expression("DATE(event.begin_date_hour)"), $endMonth]
                            ]
                        ]
                    );

                    break;
                default:
                    $this->getQueryPublished($query, true);
                    $query->innerJoin("event_highlights as 'evidenza'", 'evidenza.event_id = event.id');
            }
        } else $this->getQueryPublished($query);
    }


    /**
     * @param $query ActiveQuery
     */
    public function applyFilterDatePeriod($query)
    {
        if (!empty($_GET['day'])) {
            switch ($_GET['day']) {
                case 'all':
                    break;
                case 'today':
                    $query->andWhere(['=', new Expression("DATE(event.begin_date_hour)"), new Expression("date(NOW())")]);
                    break;
                case 'next_weekend':
                    $now = new \DateTime();
                    $now->modify('next saturday');
                    $saturday = $now->format('Y-m-d');
                    $now->modify('next sunday');
                    $sunday = $now->format('Y-m-d');

                    $query->andWhere(['OR',
                        ['=', new Expression("DATE(event.begin_date_hour)"), $saturday],
                        ['=', new Expression("DATE(event.begin_date_hour)"), $sunday]
                    ]);
                    break;
                case 'this_week':
                    $now = new \DateTime();
                    $now->modify('monday this week');
                    $dateStart = $now->format('Y-m-d');
                    $now->modify('sunday this week');
                    $dateEnd = $now->format('Y-m-d');
//                    pr($dateStart, 'START');
//                    pr($dateEnd, 'END');

                    $query->andWhere(['>=', new Expression("DATE(event.begin_date_hour)"), $dateStart]);
                    $query->andWhere(['<=', new Expression("DATE(event.begin_date_hour)"), $dateEnd]);

                    break;
                case 'this_month':
                    $now = new \DateTime();
                    $now->modify('first day of this month');
                    $startMonth = $now->format('Y-m-d');
                    $now->modify('+1 month');
                    $endMonth = $now->format('Y-m-d');
//                    pr($startMonth, 'START');
//                    pr($endMonth, 'END');

                    $query->andWhere(['>=', new Expression("DATE(event.begin_date_hour)"), $startMonth]);
                    $query->andWhere(['<', new Expression("DATE(event.begin_date_hour)"), $endMonth]);

                    break;
            }
        }
    }


    /**
     * @param $params
     */
    public function getRssFeedQuery($params)
    {
        date_default_timezone_set('Europe/Rome');

        $params = array_merge($params, Yii::$app->request->get());
        $this->load($params);
        $now = date('Y-m-d H:i:s');

//        $publicationDateTime =  new \DateTime();
//        if (!empty($params['publication_date'])) {
//            $publicationDateTime = new \DateTime($params['publication_date']);
//        }
//        $dateInterval = new \DateInterval('PT30D');
//        if (!empty($params['interval'])) {
//            $dateInterval = new \DateInterval('PT' . $params['interval'] . 'D');
//        }
//        $publicationDateTime = $publicationDateTime->sub($dateInterval);

        $tableName = $this->tableName();

        $query = $this->baseSearch($params)
            ->innerJoinWith('eventType')
            ->andWhere(['!= ', 'event_type.event_type', EventType::TYPE_UPON_INVITATION])
            ->andWhere([
                $tableName . '.status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED,
            ])
            ->andWhere(['<=', 'publication_date_begin', $now])
            ->andWhere(['or',
                    ['>=', 'publication_date_end', $now],
                    ['publication_date_end' => null]]
            )
            ->andWhere(['or',
                    ['>=', 'end_date_hour', $now],
                    ['end_date_hour' => null]]
            )->andWhere(['event.event_id' => null]);

        if (!empty($params['code'])) {
            $query
                ->leftJoin('entitys_tags_mm', 'event.id = entitys_tags_mm.record_id')
                ->leftJoin('tag', 'tag.id = entitys_tags_mm.tag_id')
                ->andWhere(['entitys_tags_mm.classname' => Event::className()])
                ->andWhere(['entitys_tags_mm.deleted_at' => null])
                ->andWhere(['tag.codice' => $params['code']]);
        }

        if (!empty($params['limit'])) {
            $query->limit($params['limit']);
        }
        return $query;

//        $this->applySearchFilters($query);
    }


    /**
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function searchHighlights()
    {
        $query = Event::find()
            ->innerJoin('event_highlights', 'event_highlights.event_id = event.id')
            ->andWhere(['event_highlights.deleted_at' => null])
            ->orderBy('n_order');
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        return $dataProvider;
    }


}