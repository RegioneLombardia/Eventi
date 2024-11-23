<?php

namespace open20\amos\events\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use open20\amos\events\models\EventLandingSection;

/**
* EventLandingSectionSearch represents the model behind the search form about `open20\amos\events\models\EventLandingSection`.
*/
class EventLandingSectionSearch extends EventLandingSection
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
[['id', 'event_landing_id', 'n_order', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['section', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
    ['EventLanding', 'safe'],
    ];
}

public function scenarios()
{
// bypass scenarios() implementation in the parent class
return Model::scenarios();
}

public function search($params)
{
$query = EventLandingSection::find();

$dataProvider = new ActiveDataProvider([
'query' => $query,
]);

    $query->joinWith('eventLanding');

$dataProvider->setSort([
'attributes' => [
    'event_landing_id' => [
    'asc' => ['event_landing_section.event_landing_id' => SORT_ASC],
    'desc' => ['event_landing_section.event_landing_id' => SORT_DESC],
    ],
    'section' => [
    'asc' => ['event_landing_section.section' => SORT_ASC],
    'desc' => ['event_landing_section.section' => SORT_DESC],
    ],
    'n_order' => [
    'asc' => ['event_landing_section.n_order' => SORT_ASC],
    'desc' => ['event_landing_section.n_order' => SORT_DESC],
    ],
]]);

if (!($this->load($params) && $this->validate())) {
return $dataProvider;
}



$query->andFilterWhere([
            'id' => $this->id,
            'event_landing_id' => $this->event_landing_id,
            'n_order' => $this->n_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
        ]);

        $query->andFilterWhere(['like', 'section', $this->section]);

return $dataProvider;
}
}
