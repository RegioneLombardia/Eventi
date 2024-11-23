<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi\models\search
 * @category   CategoryName
 */

namespace open20\amos\sondaggi\models\search;

use open20\amos\core\interfaces\CmsModelInterface;
use open20\amos\notificationmanager\AmosNotify;
use open20\amos\notificationmanager\base\NotifyWidget;
use open20\amos\notificationmanager\base\NotifyWidgetDoNothing;
use open20\amos\notificationmanager\models\NotificationChannels;
use open20\amos\sondaggi\models\Sondaggi;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\di\Container;
use yii\di\NotInstantiableException;

/**
 * SondaggiSearch represents the model behind the search form about `open20\amos\sondaggi\models\Sondaggi`.
 */
class SondaggiSearch extends Sondaggi implements CmsModelInterface
{
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
        $this->isSearch = true;
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
            $notify->notificationOff(Yii::$app->getUser()->id, Sondaggi::className(), $query, NotificationChannels::CHANNEL_READ);
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'filemanager_mediafile_id', 'created_by', 'updated_by', 'deleted_by', 'version'], 'integer'],
            [['titolo', 'descrizione', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

//    /**
//     * @param array $params Search parameters
//     * @return \yii\db\ActiveQuery
//     */
//    public function baseSearch($params)
//    {
//        // Init the default search values
//        $this->initOrderVars();
//
//        // Check params to get orders value
//        $this->setOrderVars($params);
//
//        /** @var \yii\db\ActiveQuery $baseQuery */
//        $baseQuery = Sondaggi::find();
//
//        return $baseQuery;
//    }

    /**
     * Base filter.
     * @param ActiveQuery $query
     * @return mixed
     */
    public function baseFilter($query)
    {
        $query->andFilterWhere([
            'id' => $this->id,
            'filemanager_mediafile_id' => $this->filemanager_mediafile_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'titolo', $this->titolo]);
        $query->andFilterWhere(['like', 'descrizione', $this->descrizione]);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function searchFieldsMatch()
    {
        return [
            'id',
            'filemanager_mediafile_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'created_by',
            'updated_by',
            'deleted_by',
            'version',
        ];
    }

    /**
     * @inheritdoc
     */
    public function searchFieldsLike()
    {
        return [
            'titolo',
            'descrizione',
        ];
    }

    /**
     * @inheritdoc
     */
    public function searchOwnInterest($params, $limit = null)
    {
        $dataProvider = parent::searchOwnInterest($params, $limit);
        $dataProvider->query->orderBy(['created_at' => SORT_DESC]);
        return $dataProvider;
    }

    /**
     * @param array $params Search parameters
     * @return ActiveDataProvider
     */
    public function searchDominio($params)
    {
        $query = $this->baseSearch($params);

//        $configurazioneAccessi = \backend\modules\peipoint\models\PeiAccessiServiziFacilitazioneConfigurazioneSondaggi::find();
//        $sondaggiAccessi = [];
//        if($configurazioneAccessi->count()){
//            foreach ($configurazioneAccessi->all() as $ConfAccesso){
//                $sondaggiAccessi[] = $ConfAccesso['sondaggi_id'];
//            }
//        }        
//        $query->andWhere(['NOT IN', 'id', $sondaggiAccessi]);
        $utente = Yii::$app->getUser();
        $ruoli_utente = Yii::$app->authManager->getRolesByUser($utente->getId());

        //ruolo pubblico sempre visibile
        $Ruoli = ['PUBBLICO'];
        foreach ($ruoli_utente as $Ruolo) {
            $Ruoli[] = $Ruolo->name;
        }

        $query->orderBy('sondaggi.created_at DESC');
        $query->andWhere(['status' => self::WORKFLOW_STATUS_VALIDATO]);
        $query->innerJoinWith('sondaggiPubblicaziones');
        if (!$utente->can('AMMINISTRAZIONE_SONDAGGI')) {
            $query->andWhere(['IN', 'ruolo', $Ruoli]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->notificationOff($query);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->baseFilter($query);

        return $dataProvider;
    }

    /**
     * @param array $params Search parameters
     * @return ActiveDataProvider
     */
    public function searchPartecipato($params)
    {
        $query = $this->baseSearch($params);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->notificationOff($query);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $this->baseFilter($query);
        return $dataProvider;
    }

    /**
     * @param array $params Search parameters
     * @return ActiveQuery
     */
    public function searchSondaggiNonPartecipato($params)
    {
        //$query = $this->baseSearch($params);
        $query = $this->buildQuery($params, 'admin-scope');
        //$query->joinWith('sondaggiRisposteSessionis');
        //$query->andWhere(['begin_date' => null]);
        $this->notificationOff($query);
        return $query;
    }
    
    /**
     * 
     * @param type $params
     * @param type $limit
     * @return type
     */
    public function ultimiSondaggi($params, $limit = null) {
        $dataProvider = $this->searchAll($params, $limit);
        return $dataProvider;
    }

    /**
     *
     * @param type $params
     * @param type $limit
     * @return type
     */
    public function ultimiSondaggiLive($params, $limit = null) {
        $dataProvider = $this->searchAll($params, $limit);

        $dataProvider->query
            ->andWhere(['sondaggi.sondaggio_type' => \open20\amos\sondaggi\models\base\SondaggiTypes::getLiveType()])
           // ->andWhere(['sondaggi.status' => Sondaggi::WORKFLOW_STATUS_VALIDATO])
            ->orderBy('sondaggi.id desc');
     
        return $dataProvider;
    }

    /**
     * 
     * @param type $params
     * @param type $limit
     * @return type
     */
    public function searchAll($params, $limit = null) {
        return $this->search($params, "all", $limit);
    }

    public function cmsIsVisible($id): boolean
    {
        $retValue = true;
        return $retValue;
    }

    public function cmsSearch($params, $limit): \open20\amos\core\interfaces\ActiveDataProvider
    {
        $params = array_merge($params, Yii::$app->request->get());
        $this->load($params);
        $query  = $this->baseSearch ($params);
        $this->applySearchFilters($query);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'titolo' => SORT_DESC,
                ],
            ],
        ]);
        if ($params["withPagination"]) {
            $dataProvider->setPagination(['pageSize' => $limit]);
            $query->limit(null);
        } else {
            $query->limit($limit);
        }
        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $query->andWhere(eval("return ".$command.";"));
            }
        }
        return $dataProvider;
    }

    public function cmsSearchFields(): array
    {
        $searchFields = [];

        array_push($searchFields, new CmsField("titolo", "TEXT"));
        array_push($searchFields, new CmsField("descrizione", "TEXT"));

        return $searchFields;
    }

    public function cmsViewFields(): array
    {
        return [
            new CmsField('titolo', 'TEXT', 'amossondaggi', $this->attributeLabels()['titolo']),
            new CmsField('descrizione', 'TEXT', 'amossondaggi', $this->attributeLabels()['descrizione']),
        ];
    }
}
