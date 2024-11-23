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

use open20\amos\core\record\ContentModel;
use open20\amos\admin\models\UserProfile;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WebMeetingModel
 * This is the class for controller "WebMeetingModel".
 */
class WebMeetingInviteeModel extends base\WebMeetingInviteeModel
{
    /**
     * @inheritdoc
     */
    public function getWebMeeting() {
        return $this->hasOne(WebMeetingModel::class, ['id' => 'webmeeting_id']);
    }

    /**
     * @inheritdoc
     */
    public function getUserProfile() {
        return $this->hasOne(UserProfile::class, ['id' => 'user_id']);
    }

    /**
     * 
     * @return system
     */
    public function getClassName()
    {
        $className = explode('\\', __CLASS__);
        
        return array_pop($className);
    }

    /**
     * Inserire il campo o i campi rappresentativi del modulo
     * @return type
     */
    public function representingColumn()
    {
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
                'slug' => 'coHost',
                'label' => $labels['coHost'],
                'type' => 'char'
            ],
        ];
    }

    /**
     * @return string marker path
     */
    public function getIconMarker()
    {
        return null;
    }

    /**
     * If events are more than one, set 'array' => true in the calendarView in the index.
     * @return array events
     */
    public function getEvents()
    {
        return null;
    }

    /**
     * @return url event (calendar of activities)
     */
    public function getUrlEvent()
    {
        return null;
    }

    /**
     * @return color event 
     */
    public function getColorEvent()
    {
        return null;
    }

    /**
     * @return title event
     */
    public function getTitleEvent()
    {
        return null;
    }

}
