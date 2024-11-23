<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Models
 */

namespace open20\webmeeting\models\search;

use open20\webmeeting\WebMeeting;
use open20\webmeeting\models\WebMeetingModel;

use open20\amos\core\record\ContentModel;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

/**
 * Class WebMeetingModel 
 * This is the class for controller "WebMeetingModel".
 */
class WebMeetingModelSearch extends WebMeetingModel
{
    /**
     * @var type 
     */
    public $isSearch;
    
    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->isSearch = true;
        parent::__construct($config);
        
        $this->modelClassName = WebMeetingModel::class;
    }

    /**
     */
    public function rules()
    {
        return [
            [['title', 'agenda', 'start', 'end', 'created_by'], 'safe'],
        ];
    }

    /**
     * bypass scenarios() implementation in the parent class
     * @return type
     */
    public function scenarios()
    {
        return parent::scenarios();
    }

    /**
     * @param type $params
     * @return ActiveDataProvider
     */
    public function search($params, $queryType = null, $limit = null, $onlyDrafts = false, $pageSize = null)
    {
        $query = $this->baseSearch($params);
        
        $query->andFilterWhere(['>=', 'end', $params['end']]);
        $query->orderBy(['start' => SORT_ASC]);
        
        return $this->returnActiveDataProvider($params, $query);
    }
    
    /**
     * 
     * @param type $params
     * @param type $queryType
     * @param type $limit
     * @param type $onlyDrafts
     * @return ActiveDataProvider
     */
    public function returnActiveDataProvider($params, $query)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'start' => $this->start,
            'end' => $this->end,
            'created_by' => $this->created_by,
            'context' => $params['context'],
            'context_id' => $params['context_id'],
        ]);

        if (isset($params['WebMeetingModelSearch']['title'])) {
            $title = trim($params['WebMeetingModelSearch']['title']);
            if ($title) {
                $lookingfor = explode(" ", $title);
                $query->andWhere(['OR',
                    ['LIKE', 'title', $lookingfor],
                ]);
            }
        }

        if (isset($params['WebMeetingModelSearch']['agenda'])) {
            $agenda = trim($params['WebMeetingModelSearch']['agenda']);
            if ($agenda) {
                $lookingfor = explode(" ", $agenda);

                $query->andWhere(['OR',
                    ['LIKE', 'agenda', $lookingfor],
                ]);
            }
        }
        
        return $dataProvider;
    }
    
    /**
     * @param type $params
     * @return ActiveDataProvider
     */
    public function searchActiveWebMeeting($params, $queryType = null, $limit = null, $onlyDrafts = false)
    {
        $query = $this->baseSearch($params);
        $query->andFilterWhere(['>=', 'end', $params['end']]);
        $query->andFilterWhere(['=', 'context', $params['context']]);
        $query->andFilterWhere(['=', 'context_id', $params['context_id']]);
        $query->orderBy(['start' => SORT_ASC]);
        
        return $this->returnActiveDataProvider($params, $query);
    }
    
    /**
     * @param type $params
     * @return ActiveDataProvider
     */
    public function searchPassedWebMeeting($params, $queryType = null, $limit = null, $onlyDrafts = false)
    {
        $query = $this->baseSearch($params);
        $query->andFilterWhere(['<=', 'end', $params['end']]);
        $query->orderBy(['end' => SORT_DESC]);
        
        return $this->returnActiveDataProvider($params, $query);
    }
}
