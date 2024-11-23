<?php

namespace amos\userimporter\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use amos\userimporter\models\UserImportEditList;

/**
 * UserImportEditListSearch represents the model behind the search form about `amos\userimporter\models\UserImportEditList`.
 */
class UserImportEditListSearch extends UserImportEditList
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
            [['name', 'surname', 'fiscal_code', 'email', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
// bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = UserImportEditList::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
            'attributes' => [
                'id' => [
                    'asc' => ['user_import_edit_list.id' => SORT_ASC],
                    'desc' => ['user_import_edit_list.id' => SORT_DESC],
                ],
                'name' => [
                    'asc' => ['user_import_edit_list.name' => SORT_ASC],
                    'desc' => ['user_import_edit_list.name' => SORT_DESC],
                ],
                'surname' => [
                    'asc' => ['user_import_edit_list.surname' => SORT_ASC],
                    'desc' => ['user_import_edit_list.surname' => SORT_DESC],
                ],
                'email' => [
                    'asc' => ['user_import_edit_list.email' => SORT_ASC],
                    'desc' => ['user_import_edit_list.email' => SORT_DESC],
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

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'surname', $this->surname])
            ->andFilterWhere(['like', 'fiscal_code', $this->fiscal_code])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
