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
use open20\webmeeting\models\WebMeetingInviteeModel;

use open20\amos\core\record\ContentModel;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

/**
 * Class WebMeetingModel 
 * This is the class for controller "WebMeetingModel".
 */
class WebMeetingInviteeModelSearch extends WebMeetingInviteeModel
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
    }

    /**
     * @param type $params
     * @return ActiveDataProvider
     */
    public function search($params, $queryType = null, $limit = null, $onlyDrafts = false)
    {
        $query = $this->baseSearch($params);

        $query->andFilterWhere([
            'webmeeting_id' => $params['webmeeting_id'],
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
}
