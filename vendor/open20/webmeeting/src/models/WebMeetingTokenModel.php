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

use open20\amos\admin\AmosAdmin;
use open20\amos\core\record\ContentModel;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WebMeetingModel
 * This is the class for controller "WebMeetingModel".
 */
class WebMeetingTokenModel extends base\WebMeetingTokenModel
{
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
     * 
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
     * 
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
     * 
     * @return type
     */
    public static function getEditFields()
    {
        $labels = self::attributeLabels();

        return [
            [
                'slug' => 'access_token',
                'label' => $labels['access_token'],
                'type' => 'string'
            ],
            [
                'slug' => 'expires_in',
                'label' => $labels['expires_in'],
                'type' => 'integer'
            ],
            [
                'slug' => 'refresh_token',
                'label' => $labels['refresh_token'],
                'type' => 'string'
            ],
            [
                'slug' => 'refresh_token_expires_in',
                'label' => $labels['refresh_token_expires_in'],
                'type' => 'integer'
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
