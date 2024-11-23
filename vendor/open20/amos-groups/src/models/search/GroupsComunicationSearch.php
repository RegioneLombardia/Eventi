<?php

namespace open20\amos\groups\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use open20\amos\groups\models\GroupsComunication;

/**
 * GroupsComunicationSearch represents the model behind the search form about `open20\amos\groups\models\GroupsComunication`.
 */
class GroupsComunicationSearch extends GroupsComunication
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
            [['id', 'groups_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['subject', 'text', 'layout_path', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            ['Groups', 'safe'],
        ];
    }

    public function scenarios()
    {
// bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = GroupsComunication::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith('groups');

        $dataProvider->setSort([
            'attributes' => [
                'template' => [
                    'asc' => ['groups_comunication.template' => SORT_ASC],
                    'desc' => ['groups_comunication.template' => SORT_DESC],
                ],
                'vendorPath' => [
                    'asc' => ['groups_comunication.vendorPath' => SORT_ASC],
                    'desc' => ['groups_comunication.vendorPath' => SORT_DESC],
                ],
                'providerList' => [
                    'asc' => ['groups_comunication.providerList' => SORT_ASC],
                    'desc' => ['groups_comunication.providerList' => SORT_DESC],
                ],
                'actionButtonClass' => [
                    'asc' => ['groups_comunication.actionButtonClass' => SORT_ASC],
                    'desc' => ['groups_comunication.actionButtonClass' => SORT_DESC],
                ],
                'viewPath' => [
                    'asc' => ['groups_comunication.viewPath' => SORT_ASC],
                    'desc' => ['groups_comunication.viewPath' => SORT_DESC],
                ],
                'pathPrefix' => [
                    'asc' => ['groups_comunication.pathPrefix' => SORT_ASC],
                    'desc' => ['groups_comunication.pathPrefix' => SORT_DESC],
                ],
                'savedForm' => [
                    'asc' => ['groups_comunication.savedForm' => SORT_ASC],
                    'desc' => ['groups_comunication.savedForm' => SORT_DESC],
                ],
                'formLayout' => [
                    'asc' => ['groups_comunication.formLayout' => SORT_ASC],
                    'desc' => ['groups_comunication.formLayout' => SORT_DESC],
                ],
                'accessFilter' => [
                    'asc' => ['groups_comunication.accessFilter' => SORT_ASC],
                    'desc' => ['groups_comunication.accessFilter' => SORT_DESC],
                ],
                'generateAccessFilterMigrations' => [
                    'asc' => ['groups_comunication.generateAccessFilterMigrations' => SORT_ASC],
                    'desc' => ['groups_comunication.generateAccessFilterMigrations' => SORT_DESC],
                ],
                'singularEntities' => [
                    'asc' => ['groups_comunication.singularEntities' => SORT_ASC],
                    'desc' => ['groups_comunication.singularEntities' => SORT_DESC],
                ],
                'modelMessageCategory' => [
                    'asc' => ['groups_comunication.modelMessageCategory' => SORT_ASC],
                    'desc' => ['groups_comunication.modelMessageCategory' => SORT_DESC],
                ],
                'controllerClass' => [
                    'asc' => ['groups_comunication.controllerClass' => SORT_ASC],
                    'desc' => ['groups_comunication.controllerClass' => SORT_DESC],
                ],
                'modelClass' => [
                    'asc' => ['groups_comunication.modelClass' => SORT_ASC],
                    'desc' => ['groups_comunication.modelClass' => SORT_DESC],
                ],
                'searchModelClass' => [
                    'asc' => ['groups_comunication.searchModelClass' => SORT_ASC],
                    'desc' => ['groups_comunication.searchModelClass' => SORT_DESC],
                ],
                'baseControllerClass' => [
                    'asc' => ['groups_comunication.baseControllerClass' => SORT_ASC],
                    'desc' => ['groups_comunication.baseControllerClass' => SORT_DESC],
                ],
                'indexWidgetType' => [
                    'asc' => ['groups_comunication.indexWidgetType' => SORT_ASC],
                    'desc' => ['groups_comunication.indexWidgetType' => SORT_DESC],
                ],
                'enableI18N' => [
                    'asc' => ['groups_comunication.enableI18N' => SORT_ASC],
                    'desc' => ['groups_comunication.enableI18N' => SORT_DESC],
                ],
                'enablePjax' => [
                    'asc' => ['groups_comunication.enablePjax' => SORT_ASC],
                    'desc' => ['groups_comunication.enablePjax' => SORT_DESC],
                ],
                'messageCategory' => [
                    'asc' => ['groups_comunication.messageCategory' => SORT_ASC],
                    'desc' => ['groups_comunication.messageCategory' => SORT_DESC],
                ],
                'formTabs' => [
                    'asc' => ['groups_comunication.formTabs' => SORT_ASC],
                    'desc' => ['groups_comunication.formTabs' => SORT_DESC],
                ],
                'tabsFieldList' => [
                    'asc' => ['groups_comunication.tabsFieldList' => SORT_ASC],
                    'desc' => ['groups_comunication.tabsFieldList' => SORT_DESC],
                ],
                'relFiledsDynamic' => [
                    'asc' => ['groups_comunication.relFiledsDynamic' => SORT_ASC],
                    'desc' => ['groups_comunication.relFiledsDynamic' => SORT_DESC],
                ],
            ]]);

        $query->andFilterWhere(['groups_id' => $this->groups_id]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            'id' => $this->id,
            'groups_id' => $this->groups_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
        ]);

        $query->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'layout_path', $this->layout_path]);

        return $dataProvider;
    }
}
