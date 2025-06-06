<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\documenti\models\search
 * @category   CategoryName
 */

namespace open20\amos\documenti\models\search;

use open20\amos\core\interfaces\CmsModelInterface;
use open20\amos\core\interfaces\ContentModelSearchInterface;
use open20\amos\core\interfaces\SearchModelInterface;
use open20\amos\core\record\CmsField;
use open20\amos\core\record\SearchResult;
use open20\amos\documenti\AmosDocumenti;
use open20\amos\documenti\models\Documenti;
use open20\amos\documenti\models\DocumentiCartellePath;
use open20\amos\tag\models\EntitysTagsMm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Class DocumentiSearch
 * DocumentiSearch represents the model behind the search form about `open20\amos\documenti\models\Documenti`.
 * @package open20\amos\documenti\models\search
 */
class DocumentiSearch extends Documenti implements SearchModelInterface, ContentModelSearchInterface, CmsModelInterface
{
    private $container;
    public $parentId;
    public $dataPubblicazioneAl;
    public $ricerca_sottocartelle;

    public $data_pubblicazione_from;
    public $data_pubblicazione_to;

    /**
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->isSearch = true;
        parent::__construct($config);
        $this->modelClassName = $this->documentsModule->model('Documenti');
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    
        $this->data_pubblicazione = null;
        $this->data_rimozione = null;
        $this->documenti_categorie_id = null;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'ricerca_sottocartelle' => AmosDocumenti::t('amosdocumenti', 'Ricerca nelle sottocartelle'),
            'data_pubblicazione_from' => AmosDocumenti::t('amosdocumenti', 'Data da'),
            'data_pubblicazione_to' => AmosDocumenti::t('amosdocumenti', 'Data a'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $integer = ['id', 'primo_piano', 'hits', 'abilita_pubblicazione', 'updated_by', 'deleted_by', 'parent_id','ricerca_sottocartelle'];
        if (!isset(\Yii::$app->params['hideListsContentCreatorName']) || (\Yii::$app->params['hideListsContentCreatorName']
                === false)) {
            $integer[] = 'created_by';
        }
        return [
            [$integer, 'integer'],
            [['parentId', 'titolo', 'sottotitolo', 'descrizione_breve', 'descrizione', 'metakey', 'metadesc', 'data_pubblicazione', 'dataPubblicazioneAl',
                'data_rimozione', 'documenti_categorie_id', 'created_at', 'updated_at', 'deleted_at', 'data_pubblicazione_from', 'data_pubblicazione_to','ricerca_sottocartelle'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function searchFieldsMatch()
    {
        $fields = [
            'id',
            'primo_piano',
            'hits',
            'abilita_pubblicazione',
            'created_at',
            'updated_at',
            'deleted_at',
            'updated_by',
            'deleted_by',
            'documenti_categorie_id',
        ];
        if (!isset(\Yii::$app->params['hideListsContentCreatorName']) || (\Yii::$app->params['hideListsContentCreatorName']
                === false)) {
            $fields[] = 'created_by';
        }
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function searchFieldsLike()
    {
        return [
            'titolo',
            'sottotitolo',
            'descrizione_breve',
            'descrizione',
            'metakey',
            'metadesc',
        ];
    }

    /**
     * @inheritdoc
     */
    public function searchFieldsGlobalSearch()
    {
        return [
            'titolo',
            'sottotitolo',
            'descrizione_breve',
            'descrizione',
            'metakey',
            'metadesc',
        ];
    }

    /**
     * Use to add Join condition/add other filtering condition
     *
     * @param ActiveQuery $query
     */
    public function getSearchQuery($query)
    {

        /** @var AmosDocumenti $documentModule */
        $documentModule = \Yii::$app->getModule(AmosDocumenti::getModuleName());
        if (isset($documentModule)) {
            if ($documentModule->hidePubblicationDate == false) {
                $query->andFilterWhere([
                    '>=', 'data_pubblicazione', $this->data_pubblicazione,
                ]);

                $query->andFilterWhere([
                    '<=', 'data_pubblicazione', $this->dataPubblicazioneAl,
                ]);

            }
        }

        if (!empty($this->data_pubblicazione_from)) {
            $query->andFilterWhere(['>=', new Expression("DATE(data_pubblicazione)"), new Expression("DATE('{$this->data_pubblicazione_from}')")]);
        }

        if (!empty($this->data_pubblicazione_to)) {
            $query->andFilterWhere(['<=', new Expression("DATE(data_pubblicazione)"), new Expression("DATE('{$this->data_pubblicazione_to}')")]);
        }

    }

    /**
     * @inheritdoc
     */
    public function search($params, $queryType = null, $limit = null, $onlyDratfs = false, $showAll = false)
    {
        $getParentId = (\Yii::$app instanceof \yii\web\Application) ? \Yii::$app->request->get('parentId') : null;
        if ($getParentId) {
            $params['DocumentiSearch']['parentId'] = $getParentId;
        }
        $query = $this
            ->buildQuery($params, $queryType)
            ->limit($limit);

        /** Switch off notifications - method of NotifyRecord */
        $this->switchOffNotifications($query);

        $dp_params = ['query' => $query,];
        if ($limit) {
            $dp_params ['pagination'] = false;
        }

        if (\Yii::$app->user->isGuest) {
            $query->andWhere(['primo_piano' => 1]);
        }

        //set the data provider
        $dataProvider = new ActiveDataProvider($dp_params);
        $dataProvider = $this->searchDefaultOrder($dataProvider);

        //overwrite default order in case  foldering is enabled
        if (empty($params['DocumentiSearch']['orderAttribute'])) {
            if (AmosDocumenti::instance()->enableFolders) {
                $dataProvider->setSort([
                    'defaultOrder' => [
                        'is_folder' => SORT_DESC,
                        'data_pubblicazione' => SORT_DESC,
                    ]
                ]);
            } else {
                $dataProvider->setSort([
                    'defaultOrder' => [
                        'data_pubblicazione' => SORT_DESC,
                    ]
                ]);
            }
        }

        //if you don't use the seach form, the recursive search is not active
        $this->load($params);
        //if you come from widget grphic LastDocuments Show also documents that are inside the folders
        if ((!in_array($queryType, ['to-validate', 'created-by'])) && !(!empty($params['fromWidgetGraphic']) && $params['fromWidgetGraphic'] == true)) {
            if (!$showAll) {
              if(!$params['DocumentiSearch']['ricerca_sottocartelle']){
                    $query->andWhere([self::tableName() . '.parent_id' => $this->parentId]);
                }else{
                    $query->innerJoin(
                        DocumentiCartellePath::tableName(),
                        self::tableName().'.id = '.DocumentiCartellePath::tableName().'.id_doc_folder')
                            ->andWhere([DocumentiCartellePath::tableName() . '.id_folder' => $this->parentId]);
                }
            }
        }
            
        // recursive search
        if (!empty($this->parentId)) {
            /** @var Documenti $documentiModel */
            $documentiModel = $this->documentsModule->createModel('Documenti');
            $currentFolder = $documentiModel::findOne($this->parentId);
            $listChildrenId = $currentFolder->getAllChildrens();
            $query->andWhere([self::tableName() . '.parent_id' => $listChildrenId]);
        }

        //if parentid empty the search is without (parent_id IS NULL)
        if (isset($params[$this->formName()]['tagValues'])) {

            $tagValues = $params[$this->formName()]['tagValues'];
            $this->setTagValues($tagValues);
            if (is_array($tagValues) && !empty($tagValues)) {
                $orQueries = null;
                $i = 0;
                foreach ($tagValues as $rootId => $tagId) {
                    if (!empty($tagId)) {
                        if ($i == 0) {
                            $query->innerJoin('entitys_tags_mm entities_tag',
                                "entities_tag.classname = '" . addslashes($this->modelClassName) . "' AND entities_tag.record_id=" . static::tableName() . ".id");
                            $orQueries[] = 'or';
                        }
                        $tags = explode(',', $tagId);
                        $tags = array_unique($tags);
                        $orQueries[] = ['and', ["entities_tag.tag_id" => $tags], ['entities_tag.root_id' => $rootId], ['entities_tag.deleted_at' => null]];
                        $i++;
                    }
                }
                if (!empty($orQueries)) {
                    $query->andWhere($orQueries);
                }
            }
        }

        $this->applySearchFilters($query);
        $this->getSearchQuery($query);

        //VarDumper::dump($query->createCommand()->rawSql,3,true);
        return $dataProvider;
    }

    
    /**
     * @param AmosModule $moduleCwh
     * @param string $classname
     * @return bool
     */
    private function isSetCwh($moduleCwh, $classname)
    {
        if (isset($moduleCwh) && in_array($classname, $moduleCwh->modelsEnabled)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Documents base search: all documents matching search parameters and not deleted.
     *
     * @param array $params Search parameters
     * @return \yii\db\ActiveQuery
     */
    public function baseSearch($params)
    {
        //init the default search values
        $this->initOrderVars();

        //check params to get orders value
        $this->setOrderVars($params);

        /** @var \yii\db\ActiveQuery $baseQuery */
        $documentModel = $this->documentsModule->model('Documenti');
        $baseQuery = $documentModel::find()->distinct();

        if ($this->documentsModule->enableDocumentVersioning) {
            $baseQuery->andWhere(['version_parent_id' => null]);
        }

        return $baseQuery;
    }

    /**
     * Search the Documents created by the logged user
     *
     * @param array $params Array di parametri per la ricerca
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function searchOwnDocuments($params, $limit = null)
    {
        return $this->search($params, 'created-by', $limit);
    }

    /**
     * Search documents to validate based on cwh rules if cwh is active, all documents in 'to validate status' otherwise
     *
     * @param array $params Array di parametri per la ricerca
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function searchToValidateDocuments($params, $limit = null)
    {
        return $this->search($params, 'to-validate', $limit);
    }

    /**
     * Search last documents in validated status, generally the limit is set to 3 (by last documents graphic widget)
     *
     * @param array $params Array of search parameters
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function lastDocuments($params, $limit = null)
    {
        $params                      = array_merge($params, Yii::$app->request->getQueryParams());
        $params['fromWidgetGraphic'] = true;
        $dataProvider = $this->searchAll($params, $limit);

        if (!empty($params["conditionSearch"])) {
            $commands = explode(";", $params["conditionSearch"]);
            foreach ($commands as $command) {
                $dataProvider->query->andWhere(eval("return ".$command.";"));
            }
        }
        return $dataProvider;
    }

    /**
     * @param $params
     * @param null $limit
     * @return ActiveDataProvider
     */
    public function searchAdminAll($params, $limit = null)
    {
        return $this->search($params, 'admin-all', $limit);
    }

    /**
     * Search method useful to retrieve all validated documenti (based on publication rule and visibility).
     *
     * @param array $params Array of get parameters for search
     * @param int|null $limit
     * @return ActiveDataProvider
     */
    public function searchOwnInterest($params, $limit = null, $showAll = false)
    {

        return $this->search($params, 'own-interest', $limit, false, $showAll);
    }

    /**
     * Search method useful to retrieve documents in validated status with both flags primo_piano and in_evidenza true
     *
     * @param array $params Array di parametri
     * @return ActiveDataProvider
     */
    public function searchHighlightedAndHomepageDocumenti($params)
    {
        $query = $this->highlightedAndHomepageDocumentiQuery($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'data_pubblicazione' => SORT_DESC,
                ],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    /**
     * Search method useful to retrieve documents in validated status with flag primo_piano = true
     *
     * @param array $params Array di parametri
     * @return ActiveDataProvider
     */
    public function searchHomepageDocuments($params)
    {
        $query = $this->homepageDocumentsQuery($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'data_pubblicazione' => SORT_DESC,
                ],
            ],
        ]);

//        if (!($this->load($params) && $this->validate())) {
//            return $dataProvider;
//        }

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return \yii\db\ActiveQuery
     */
    public function highlightedAndHomepageDocumentiQuery($params)
    {
        return $this
            ->baseSearch($params)
            ->andWhere([
                self::tableName() . '.deleted_at' => null,
                self::tableName() . '.status' => Documenti::DOCUMENTI_WORKFLOW_STATUS_VALIDATO,
                self::tableName() . '.in_evidenza' => 1,
                self::tableName() . '.primo_piano' => 1,
            ]);
    }

    /**
     * @param array $params
     * @return \yii\db\ActiveQuery
     */
    public function homepageDocumentsQuery($params)
    {
        return $this
            ->baseSearch($params)
            ->distinct()->leftJoin(EntitysTagsMm::tableName(), EntitysTagsMm::tableName() . ".classname = '" . str_replace('\\', '\\\\', Documenti::className()) . "' and " . EntitysTagsMm::tableName() . ".record_id = " . Documenti::tableName() . ".id and " . EntitysTagsMm::tableName() . ".deleted_at is NULL")
            ->where([
                self::tableName() . '.deleted_at' => null,
                self::tableName() . '.status' => Documenti::DOCUMENTI_WORKFLOW_STATUS_VALIDATO,
                self::tableName() . '.primo_piano' => 1,
            ]);
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchVersions($params)
    {
        /** @var Documenti $documentiModel */
        $documentiModel = $this->documentsModule->createModel('Documenti');
        $query = $documentiModel::find()
            ->andFilterWhere([
                'version_parent_id' => $params['parent_id'],
            ])
            ->andFilterWhere(['!=', 'id', $params['parent_id']])
            ->orderBy('version DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

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
        $searchResult->box_type = "file";
        $searchResult->id = $model->id;
        $searchResult->titolo = $model->titolo;
        $searchResult->data_pubblicazione = $model->data_pubblicazione;
        $searchResult->documento = $model->getDocumentMainFile();
        $searchResult->abstract = $model->descrizione_breve;

        return $searchResult;
    }

    /**
     * @inheritdoc
     */
    public function searchDefaultOrder($dataProvider)
    {
        // Check if can use the custom module order
        if ($this->canUseModuleOrder()) {
            $dataProvider->setSort($this->createOrderClause());
        } else {
            // For widget graphic last news, order is incorrect without this else
            $dataProvider->setSort([
                'defaultOrder' => ['data_pubblicazione' => SORT_DESC]
            ]);
        }

        return $dataProvider;
    }

    /**
     * @inheritdoc
     */
    public function cmsIsVisible($id)
    {
        $retValue = true;
        return $retValue;
    }

    /**
     * @inheritdoc
     */
    public function cmsSearch($params, $limit)
    {
        $params = array_merge($params, Yii::$app->request->get());
        $this->load($params);
        $query = $this->homepageDocumentsQuery($params);
        $this->applySearchFilters($query);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'data_pubblicazione' => SORT_DESC,
                ],
            ],
        ]);

        $query->andWhere(['primo_piano' => 1]);

        if ($params["withPagination"]) {
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
     * @param $params
     * @param null $limit
     * @return \open20\amos\core\interfaces\ActiveDataProvider|ActiveDataProvider
     */
    public function cmsSearchOwnInterest($params, $limit = null)
    {
        if (\Yii::$app->user->isGuest) {
            $dataProvider = $this->cmsSearch($params, $limit);
        } else {
            $dataProvider = $this->searchOwnInterest($params, $limit);
        }

        if (!empty($params["withPagination"])) {
            $dataProvider->setPagination(['pageSize' => $limit]);
            $dataProvider->query->limit(null);
        } else {
            $dataProvider->query->limit($limit);
        }

        return $dataProvider;
    }

    /**
     * @inheritdoc
     */
    public function cmsSearchFields()
    {
        $searchFields = [];

        array_push($searchFields, new CmsField("titolo", "TEXT"));
        array_push($searchFields, new CmsField("descrizione", "TEXT"));
        array_push($searchFields, new CmsField("descrizione_breve", "TEXT"));

        return $searchFields;
    }

    /**
     * @inheritdoc
     */
    public function cmsViewFields()
    {
        return [
            new CmsField('titolo', 'TEXT', 'amosdocumenti', $this->attributeLabels()['titolo']),
            new CmsField('descrizione', 'TEXT', 'amosdocumenti', $this->attributeLabels()['descrizione']),
            new CmsField('descrizione_breve', 'TEXT', 'amosdocumenti', $this->attributeLabels()['descrizione_breve']),
            new CmsField('documentMainFile', 'IMAGE', 'amosdocumenti', $this->attributeLabels()['documentMainFile'])
        ];
    }
}