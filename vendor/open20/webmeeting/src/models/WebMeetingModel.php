<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Models
 */

namespace open20\webmeeting\models;

use open20\webmeeting\WebMeetingModule;
use open20\webmeeting\providers\api\WebExMeeting;
use open20\webmeeting\utility\WebMeetingUtility;

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\models\UserProfile;
use open20\amos\core\record\ContentModel;
use open20\amos\core\user\User;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WebMeetingModel
 * This is the class for controller "WebMeetingModel".
 */
class WebMeetingModel extends base\WebMeetingModel
{

    /**
     * @return type
     */
    public function getPublishedUserProfile()
    {
        $user = null;
        if ($this->adminInstalled) {
            $user = $this->hasOne(UserProfile::class, ['user_id' => 'published_by']);
        }

        return $user;
    }

    /**
     * 
     * @param type $models
     * @param type $data
     * @param type $formName
     */
    public function load($data, $formName = null)
    {
        if(empty($data['WebMeetingModel']['end_hour'])){
            $data['WebMeetingModel']['end_hour'] = '00:00';
        }
        if(empty($data['WebMeetingModel']['start_hour'])){
            $data['WebMeetingModel']['start_hour'] = '00:00';
        }
        $this->start = $data['WebMeetingModel']['start_date'] . ' ' . $data['WebMeetingModel']['start_hour'];
        if(!empty($data['WebMeetingModel']['end_date'] )) {
            $this->end = $data['WebMeetingModel']['end_date'] . ' ' . $data['WebMeetingModel']['end_hour'];
        }

        $scope = $formName === null ? $this->formName() : $formName;
        if ($scope === '' && !empty($data)) {
            $this->setAttributes($data);

            return true;
        } elseif (isset($data[$scope])) {
            $this->setAttributes($data[$scope]);

            return true;
        }

        return false;
    }

    
    
    /**
     *
     * @param type $insert
     * @return type
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->invitees = json_encode($this->invitees);

        return true;
    }

    /**
     *
     */
    public function afterFind()
    {
        parent::afterFind();
        
        list($this->end_date, $this->end_hour) = explode(" ", $this->end);
        list($this->start_date, $this->start_hour) = explode(" ", $this->start);
        $this->invitees = json_decode($this->invitees);
    }

    /**
     * @return type
     */
    public function representingColumn()
    {
        //inserire il campo o i campi rappresentativi del modulo
        return [];
    }

    /**
     * @return type
     */
    public function attributeHints()
    {
        return [];
    }

    /**
     * Returns the text hint for the specified attribute.
     * @param string $attribute the attribute name
     * @return string the attribute hint
     */
    public function getAttributeHint($attribute)
    {
        $hints = $this->attributeHints();
        return isset($hints[$attribute]) ? $hints[$attribute] : null;
    }

    /**
     * @return type
     */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            []
        );
    }

    /**
     * @return type
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            []
        );
    }

    /**
     * @return type
     */
    public static function getEditFields()
    {
        $labels = self::attributeLabels();

        return [
            [
                'slug' => 'title',
                'label' => $labels['title'],
                'type' => 'char'
            ],
            [
                'slug' => 'description',
                'label' => $labels['description'],
                'type' => 'text'
            ],
            [
                'slug' => 'id_room',
                'label' => $labels['id_room'],
                'type' => 'char'
            ],
        ];
    }

    /**
     * @return string marker path
     */
    public function getIconMarker()
    {
        return null; //TODO
    }

    /**
     * If events are more than one, set 'array' => true in the calendarView in the index.
     * @return array events
     */
    public function getEvents()
    {
        return null; //TODO
    }

    /**
     * @return url event (calendar of activities)
     */
    public function getUrlEvent()
    {
        return null; //TODO e.g. Yii::$app->urlManager->createUrl([]);
    }

    /**
     * @return color event
     */
    public function getColorEvent()
    {
        return null; //TODO
    }

    /**
     * @return title event
     */
    public function getTitleEvent()
    {
        return null; //TODO
    }
}