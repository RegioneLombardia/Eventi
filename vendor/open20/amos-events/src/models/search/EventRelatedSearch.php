<?php

namespace open20\amos\events\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use open20\amos\events\models\EventRelated;

/**
 * EventRelatedSearch represents the model behind the search form about `open20\amos\events\models\EventRelated`.
 */
class EventRelatedSearch extends EventRelated
{

//private $container; 

    public function __construct(array $config = [])
    {
        $this->isSearch = true;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['event_id', 'related_event_id', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
// bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = EventRelated::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        $dataProvider->setSort([
            'attributes' => [
                'template' => [
                    'asc' => ['event_related.template' => SORT_ASC],
                    'desc' => ['event_related.template' => SORT_DESC],
                ],
                'vendorPath' => [
                    'asc' => ['event_related.vendorPath' => SORT_ASC],
                    'desc' => ['event_related.vendorPath' => SORT_DESC],
                ],
                'providerList' => [
                    'asc' => ['event_related.providerList' => SORT_ASC],
                    'desc' => ['event_related.providerList' => SORT_DESC],
                ],
                'actionButtonClass' => [
                    'asc' => ['event_related.actionButtonClass' => SORT_ASC],
                    'desc' => ['event_related.actionButtonClass' => SORT_DESC],
                ],
                'viewPath' => [
                    'asc' => ['event_related.viewPath' => SORT_ASC],
                    'desc' => ['event_related.viewPath' => SORT_DESC],
                ],
                'pathPrefix' => [
                    'asc' => ['event_related.pathPrefix' => SORT_ASC],
                    'desc' => ['event_related.pathPrefix' => SORT_DESC],
                ],
                'savedForm' => [
                    'asc' => ['event_related.savedForm' => SORT_ASC],
                    'desc' => ['event_related.savedForm' => SORT_DESC],
                ],
                'formLayout' => [
                    'asc' => ['event_related.formLayout' => SORT_ASC],
                    'desc' => ['event_related.formLayout' => SORT_DESC],
                ],
                'accessFilter' => [
                    'asc' => ['event_related.accessFilter' => SORT_ASC],
                    'desc' => ['event_related.accessFilter' => SORT_DESC],
                ],
                'generateAccessFilterMigrations' => [
                    'asc' => ['event_related.generateAccessFilterMigrations' => SORT_ASC],
                    'desc' => ['event_related.generateAccessFilterMigrations' => SORT_DESC],
                ],
                'singularEntities' => [
                    'asc' => ['event_related.singularEntities' => SORT_ASC],
                    'desc' => ['event_related.singularEntities' => SORT_DESC],
                ],
                'modelMessageCategory' => [
                    'asc' => ['event_related.modelMessageCategory' => SORT_ASC],
                    'desc' => ['event_related.modelMessageCategory' => SORT_DESC],
                ],
                'controllerClass' => [
                    'asc' => ['event_related.controllerClass' => SORT_ASC],
                    'desc' => ['event_related.controllerClass' => SORT_DESC],
                ],
                'modelClass' => [
                    'asc' => ['event_related.modelClass' => SORT_ASC],
                    'desc' => ['event_related.modelClass' => SORT_DESC],
                ],
                'searchModelClass' => [
                    'asc' => ['event_related.searchModelClass' => SORT_ASC],
                    'desc' => ['event_related.searchModelClass' => SORT_DESC],
                ],
                'baseControllerClass' => [
                    'asc' => ['event_related.baseControllerClass' => SORT_ASC],
                    'desc' => ['event_related.baseControllerClass' => SORT_DESC],
                ],
                'indexWidgetType' => [
                    'asc' => ['event_related.indexWidgetType' => SORT_ASC],
                    'desc' => ['event_related.indexWidgetType' => SORT_DESC],
                ],
                'enableI18N' => [
                    'asc' => ['event_related.enableI18N' => SORT_ASC],
                    'desc' => ['event_related.enableI18N' => SORT_DESC],
                ],
                'enablePjax' => [
                    'asc' => ['event_related.enablePjax' => SORT_ASC],
                    'desc' => ['event_related.enablePjax' => SORT_DESC],
                ],
                'messageCategory' => [
                    'asc' => ['event_related.messageCategory' => SORT_ASC],
                    'desc' => ['event_related.messageCategory' => SORT_DESC],
                ],
                'formTabs' => [
                    'asc' => ['event_related.formTabs' => SORT_ASC],
                    'desc' => ['event_related.formTabs' => SORT_DESC],
                ],
                'tabsFieldList' => [
                    'asc' => ['event_related.tabsFieldList' => SORT_ASC],
                    'desc' => ['event_related.tabsFieldList' => SORT_DESC],
                ],
                'relFiledsDynamic' => [
                    'asc' => ['event_related.relFiledsDynamic' => SORT_ASC],
                    'desc' => ['event_related.relFiledsDynamic' => SORT_DESC],
                ],
            ]]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
        ]);

        $query->andFilterWhere(['like', 'event_id', $this->event_id])
            ->andFilterWhere(['like', 'related_event_id', $this->related_event_id]);

        return $dataProvider;
    }
}
