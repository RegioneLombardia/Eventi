<?php

namespace open20\amos\events\models;

use open20\amos\events\AmosEvents;
use open20\amos\events\utility\EventsUtility;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "event_invitation_specific_users".
 */
class EventInvitationSpecificUsers extends \open20\amos\events\models\base\EventInvitationSpecificUsers
{
    public function representingColumn()
    {
        return [
//inserire il campo o i campi rappresentativi del modulo
        ];
    }

    public function attributeHints()
    {
        return [
        ];
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

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
        ]);
    }

    public function attributeLabels()
    {
        return
            ArrayHelper::merge(
                parent::attributeLabels(),
                [
                ]);
    }


    public static function getEditFields()
    {
        $labels = self::attributeLabels();

        return [
            [
                'slug' => 'event_id',
                'label' => $labels['event_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'user_id',
                'label' => $labels['user_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'email',
                'label' => $labels['email'],
                'type' => 'string'
            ],
            [
                'slug' => 'send_at',
                'label' => $labels['send_at'],
                'type' => 'datetime'
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
        return NULL; //TODO
    }

    /**
     * @return url event (calendar of activities)
     */
    public function getUrlEvent()
    {
        return NULL; //TODO e.g. Yii::$app->urlManager->createUrl([]);
    }

    /**
     * @return color event
     */
    public function getColorEvent()
    {
        return NULL; //TODO
    }

    /**
     * @return title event
     */
    public function getTitleEvent()
    {
        return NULL; //TODO
    }

    /**
     * @param $users_ids_str
     * @param $webmeeting
     * @param $connectionWebex
     * @param $event_id
     * @throws \yii\base\InvalidConfigException
     */
    public static function deleteInvitedUsers($users_ids_str, $webmeeting = null, $connectionWebex = null, $event_id)
    {
        $user_ids_delete = explode(',', $users_ids_str);

        if (!empty($users_ids_str)) {
            //delete in webex
            foreach ($user_ids_delete as $user_id) {
                $invitationUser = EventInvitationSpecificUsers::find()
                    ->andWhere(['user_id' => $user_id])
                    ->andWhere(['event_id' => $event_id])->one();
                if ($invitationUser && $connectionWebex) {
                    $apiParamsDelete = [
                        'meetingInviteeId' => $invitationUser->webmeeting_invitee_id,
                        'query' => 'sendEmail=false&hostEmail=' . $webmeeting->host_email
                    ];
                    $connectionWebex->callApi('\open20\webmeeting\providers\api\WebExMeetingInvitees', 'deleteMeetingInvitee', $apiParamsDelete);
                }

            }
            //delete from table
            EventInvitationSpecificUsers::deleteAll(['user_id' => $user_ids_delete, 'event_id' => $event_id]);
        }
    }

    /**
     * @param $users_ids_str
     * @param $webmeeting
     * @param $connectionWebex
     * @param $event_id
     */
    public static function saveInvitedUsers($users_ids_str, $webmeeting = null, $connectionWebex = null, $event_id)
    {
        $user_ids = explode(',', $users_ids_str);
        if (!empty($users_ids_str)) {
            //save on database
            foreach ($user_ids as $user_id) {
                $userInvited = new EventInvitationSpecificUsers();
                $userInvited->user_id = $user_id;
                $userInvited->event_id = $event_id;
                $userInvited->save(false);
            }

            //-------------- SAVE PARTICIPANTS TO WEBEX
            if($connectionWebex) {
                //$allInvitedUsers = $this->model->getEventSpecificUsers()->select('user_profile.user_id');
                $apiParams = EventsUtility::prepareUserToImportInWebex($webmeeting, $user_ids);
                $apiResponse = $connectionWebex->callApi('\open20\webmeeting\providers\api\WebExMeetingInvitees', 'createMeetingInviteeBulkInsert', $apiParams);
                //if response ok
                if (!isset($apiResponse->message)) {
                    // save meeting_invitee_id
                    EventsUtility::saveUserInvitedFromWebex($apiResponse, $event_id);
                    \Yii::$app->getSession()->addFlash('success', AmosEvents::t('amosevents', "Utenti inseriti nella lista di invitati correttamente."));
                } else {
                    $connectionWebex->showErrors($apiResponse);
                }
            }
        }
    }


}
