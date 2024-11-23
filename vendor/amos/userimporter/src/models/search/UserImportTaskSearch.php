<?php

namespace amos\userimporter\models\search;

use amos\userimporter\models\UserImportTask;
use open20\amos\events\utility\EventsUtility;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * UserImporterTaskSearch represents the model behind the search form about `amos\userimporter\models\UserImportTask`.
 */
class UserImportTaskSearch extends UserImportTask
{
    public $taskFrom;
    public $taskTo;
//private $container;

    public function __construct(array $config = [])
    {
        $this->isSearch = true;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['id', 'user_id', 'event_group_referent_id', 'tot_input_processed',
                'tot_input_imported', 'file_input', 'file_success_input', 'file_errors_input'],
                'integer'],
            [['taskFrom', 'taskTo', 'name', 'task_date'], 'safe'],
            ['EventGroupReferent', 'safe'],
        ];
    }

    public function scenarios()
    {
// bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = UserImportTask::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith('eventGroupReferent');
        $event_group_referent = EventsUtility::getCurrentDG();
        if (!is_null($event_group_referent)) {
            $query->andWhere(['event_group_referent_id' => $event_group_referent->id]);
        }

        $dataProvider->setSort([
            'defaultOrder' => ['task_date'=>SORT_DESC],
            'attributes' => [
                'name' => [
                    'asc' => ['user_import_task.name' => SORT_ASC],
                    'desc' => ['user_import_task.name' => SORT_DESC],
                ],
                'task_date' => [
                    'asc' => ['user_import_task.task_date' => SORT_ASC],
                    'desc' => ['user_import_task.task_date' => SORT_DESC],
                ],
                'status' => [
                    'asc' => ['user_import_task.status' => SORT_ASC],
                    'desc' => ['user_import_task.status' => SORT_DESC],
                ],
                'eventGroupReferent' => [
                    'asc' => ['event_group_referent.id' => SORT_ASC],
                    'desc' => ['event_group_referent.id' => SORT_DESC],
                ],]]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }



        $query->andFilterWhere([
            'id' => $this->id,
            'task_date' => $this->task_date,
            'user_id' => $this->user_id,
            'event_group_referent_id' => $this->event_group_referent_id,
            'tot_input_processed' => $this->tot_input_processed,
            'tot_input_imported' => $this->tot_input_imported,
            'file_input' => $this->file_input,
            'file_success_input' => $this->file_success_input,
            'file_errors_input' => $this->file_errors_input,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['=', new Expression('event_group_referent.id'), $this->eventGroupReferent]);
        $query->andFilterWhere(['>=', 'task_date', $this->taskFrom]);
        $query->andFilterWhere(['<=', 'task_date', $this->taskTo]);


        return $dataProvider;
    }
}