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

/**
 * Class WebMeetingModel 
 * This is the class for controller "WebMeetingModel".
 */
class WebMeetingTeamModelSearch extends \open20\webmeeting\models\WebMeetingTeamModel
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

}
