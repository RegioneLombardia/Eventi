<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 04/07/2022
 * Time: 15:08
 */

namespace open20\amos\events\events;

use open20\amos\admin\models\UserProfile;
use open20\amos\core\models\AttributesChangeLog;
use open20\amos\core\models\ModelsClassname;
use open20\amos\core\models\UserActivityLog;
use open20\amos\core\record\Record;
use open20\amos\core\user\User;
use open20\amos\events\models\EventChangeAttributes;
use open20\amos\events\models\EventCommunication;
use open20\amos\events\models\EventPushNotification;
use open20\amos\events\models\EventType;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use open20\amos\core\module\BaseAmosModule;


class EventChangeAttributesEvent
{

    /**
     * @var array $attributesToLog
     */
    public $attributesToLog = [];
    const intervalSendEmail = '+10 minutes';

//    /**
//     * @inheritdoc
//     */
//    public function events()
//    {
//        $events = [
////            ActiveRecord::EVENT_AFTER_INSERT => 'saveChangedValues',
//            \open20\amos\events\models\Event::EVENT_BEFORE_UPDATE => 'saveChangedValues',
//        ];
//
//        return $events;
//    }

    /**
     * @param $attributes
     * @return array
     */
    public function filterWhiteListAttribute($attributes)
    {
        $filteredAttributes = [];
        $whiteList = ['begin_date_hour', 'end_date_hour', 'status', 'event_location_id'];
//        $whiteList = ['begin_date_hour', 'end_date_hour', 'status', 'event_location_id', 'event_location_entrance_id'];
        foreach ($attributes as $attribute => $value) {
            if (in_array($attribute, $whiteList)) {
                $filteredAttributes[$attribute] = $value;
            }
        }
        return $filteredAttributes;
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function saveChangedValues($event)
    {
        if ($event->sender instanceof \open20\amos\events\models\Event) {
            $owner = $event->sender;
        } else {
            /** @var Record $owner */
            $owner = $event->sender->owner;
        }
//        pr($owner->title,'evento');
//        pr($owner->getAttributes(),'evento');
        if (!empty($owner)) {
            $oldAttributes = $owner->getOldAttributes();
            $this->attributesToLog = $owner->getAttributes();

            // parse oldAttributes and  attributeToLog
            $oldAttributes = $this->parseLocation($oldAttributes);
            $this->attributesToLog = $this->filterWhiteListAttribute($this->attributesToLog);

            $hash = hash('md5', date('Y-m-d H:i:s'));
            $changedValue = [];
            if ($this->isChanged($event, $oldAttributes)) {
                foreach ($this->attributesToLog as $attributeName => $value) {
                    $oldValue = (($event->name == ActiveRecord::EVENT_AFTER_INSERT) ? null : $oldAttributes[$attributeName]);
                    $newValue = $this->attributesToLog[$attributeName];
                    if (!$this->skipLog($oldValue, $newValue) && $oldValue != $newValue) {
                        $oldValue = $this->filterLog($oldValue, $newValue, 'old');
                        $newValue = $this->filterLog($oldValue, $newValue, 'new');

                        $changedValue[$attributeName]['oldValue'] = $oldValue;
                        $changedValue[$attributeName]['newValue'] = $newValue;

                        $fieldsLog = new EventChangeAttributes();
                        $fieldsLog->event_id = $owner->id;
                        $fieldsLog->user_id = \Yii::$app->user->id;
                        $fieldsLog->operation_type = EventChangeAttributes::TYPE_CHANGE;
                        $fieldsLog->operation_group = $hash;

                        $fieldsLog->model_classname = $owner->className();
                        $fieldsLog->model_id = $owner->getPrimaryKey();
                        $fieldsLog->model_attribute = $attributeName;

                        $fieldsLog->old_value = $oldValue;
                        $fieldsLog->new_value = $newValue;
                        $fieldsLog->save(false);
                    }
                }
                if (!empty($changedValue)) {
                    self::sendEmailChangedEvent($owner, $changedValue);
                }

            }
        }
        return true;
    }

    /**
     * @param $modelEvent
     */
    public function sendEmailSuspendedEvent($modelEvent)
    {
        $communication = new EventCommunication();
        $communication->event_id = $modelEvent->id;
        $communication->title = \Yii::t('amosevents', 'Evento annullato');
        $communication->communication_type = EventCommunication::TYPE_REGISTERED;
        $communication->time_schedule_type = EventCommunication::SENDING_TIME_SCHEDULED;
        $communication->status = EventCommunication::STATUS_USERS_TO_IMPORT;
//        $communication->time_schedule_date = date("Y-m-d H:i:s", strtotime('+3 hours'));
        $communication->time_schedule_date = date("Y-m-d H:i:s", strtotime(self::intervalSendEmail));
        $nUserFound = $communication->searchUsersQuery($modelEvent->id)->count();
        $communication->n_users = $nUserFound;

        if ($nUserFound > 0) {
            $text = EventCommunication::textEventChanged(EventCommunication::TYPE_OF_CHANGE_SUSPENDED);
            $subject = EventCommunication::subjectEventChanged(EventCommunication::TYPE_OF_CHANGE_SUSPENDED);
            $communication->subject_email = $subject;
            $communication->text_email = $text;
            $communication->save(false);
        }

    }

    /**
     * @param $event \open20\amos\events\models\Event
     * @param $changedValue
     * @throws \Exception
     */
    public static function sendEmailChangedEvent($event, $changedValue)
    {
        $notifiableAttributes = ['begin_date_hour', 'end_date_hour', 'event_location_id'];
//        pr($changedValue, 'attrib');
        $communication = new EventCommunication();
        $communication->event_id = $event->id;
        $communication->title = \Yii::t('amosevents', 'Variazione info evento');
        $communication->communication_type = EventCommunication::TYPE_REGISTERED;
        $communication->time_schedule_type = EventCommunication::SENDING_TIME_SCHEDULED;
        $communication->status = EventCommunication::STATUS_USERS_TO_IMPORT;
        $communication->is_changed_info = 1;
        $communication->time_schedule_date = date("Y-m-d H:i:s", strtotime(self::intervalSendEmail));
        $nUserFound = $communication->searchUsersQuery($event->id)->count();
        $communication->n_users = $nUserFound;
        $subtype = null;

        if ($nUserFound > 0 && $event->eventType->event_type != EventType::TYPE_INFORMATIVE) {
//            pr('uno');
            // se cambio un solo valore
            if (count($changedValue) == 1) {
                $value = reset($changedValue);
                $attributeName = key($changedValue);
//                $communication->title .= ' ' . $event->attributeLabels()[$attributeName];
                if (in_array($attributeName, $notifiableAttributes)) {
                    if ($attributeName == 'begin_date_hour' || $attributeName == 'end_date_hour') {
                        $oldBeginDateTime = new \DateTime($value['oldValue']);
                        $newBeginDateTime = new \DateTime($value['newValue']);

                        if ($newBeginDateTime->format('Y-m-d') != $oldBeginDateTime->format('Y-m-d')) {
                            $text = EventCommunication::textEventChanged(EventCommunication::TYPE_OF_CHANGE_DATE);
                            $subject = EventCommunication::subjectEventChanged(EventCommunication::TYPE_OF_CHANGE_DATE);
                            $communication->title = \Yii::t('amosevents', "Variazione data");
                            $subtype = EventCommunication::TYPE_OF_CHANGE_DATE;

                        } elseif ($newBeginDateTime->format('H:i') != $oldBeginDateTime->format('H:i')) {
                            $text = EventCommunication::textEventChanged(EventCommunication::TYPE_OF_CHANGE_HOUR);
                            $subject = EventCommunication::subjectEventChanged(EventCommunication::TYPE_OF_CHANGE_HOUR);
                            $communication->title = \Yii::t('amosevents', "Variazione orario");
                            $subtype = EventCommunication::TYPE_OF_CHANGE_HOUR;

                        }

                    } else if ($attributeName == 'event_location_id') {
                        $text = EventCommunication::textEventChanged(EventCommunication::TYPE_OF_CHANGE_PLACE);
                        $subject = EventCommunication::subjectEventChanged(EventCommunication::TYPE_OF_CHANGE_PLACE);
                        $communication->title = \Yii::t('amosevents', "Variazione luogo di svolgimento");
                        $subtype = EventCommunication::TYPE_OF_CHANGE_PLACE;

                    }
                    $communication->subject_email = $subject;
                    $communication->text_email = $text;
                    $communication->save(false);
                    self::createPushNotification($communication, $communication->event_id, $subtype );
                }
                // se cambio tanti valori insieme
            } else {
                // l'attributo cambiato deve essere delle tipologie da notificare corrette
                $subtype = EventCommunication::TYPE_OF_CHANGE_OTHER;
                $canSendNotification = false;
                foreach ($changedValue as $attributeName => $values) {
                    if (in_array($attributeName, $notifiableAttributes)) {
                        $canSendNotification = true;
                    }
                }
//                pr('molti');
                if ($canSendNotification) {
                    $text = EventCommunication::textEventChanged();
                    $subject = EventCommunication::subjectEventChanged();
                    $communication->subject_email = $subject;
                    $communication->text_email = $text;
                    $communication->save(false);
                    self::createPushNotification($communication, $communication->event_id, $subtype );
                }
            }
        }
    }

    /**
     * @param $eventCommunication
     * @param $event_id
     * @param $subtype
     */
    public static function createPushNotification($eventCommunication, $event_id, $subtype){
        $pushNotification = new EventPushNotification();
        $pushNotification->type = EventPushNotification::TYPE_EVENT_CHANGED;
        $pushNotification->subtype = $subtype;
        $pushNotification->event_id = $event_id;
        $pushNotification->content_class = EventCommunication::className();
        $pushNotification->content_id = $eventCommunication->id;
        $pushNotification->status = EventPushNotification::STATUS_DONT_SEND;
        $pushNotification->save(false);

    }


    public function parseLocation($oldAttributes)
    {
        $locationNew = [
            'event_location_id' => (integer)$this->attributesToLog['event_location_id'],
            'event_location_entrance_id' => (integer)$this->attributesToLog['event_location_entrance_id']
        ];

        $locationOld = [
            'event_location_id' => (integer)$oldAttributes['event_location_id'],
            'event_location_entrance_id' => (integer)$oldAttributes['event_location_entrance_id']
        ];

        $this->attributesToLog ['event_location_id'] = serialize($locationNew);
        $oldAttributes['event_location_id'] = serialize($locationOld);

        return $oldAttributes;
    }

    /**
     * @param $old
     * @param $new
     * @return bool
     */
    public
    function skipLog($old, $new)
    {
        if ($old == 'EventWorkflow/DRAFT' && $new == 'EventWorkflow/PUBLISHREQUEST') {
            return true;
        }
        if ($old == 'EventWorkflow/DRAFT' && $new == 'EventWorkflow/SUSPENDED') {
            return true;
        }
        if (/*$old == 'EventWorkflow/PUBLISHED' && */ $new == 'EventWorkflow/SUSPENDED') {
            return true;
        }
        return false;
    }

    /**
     * @param $old
     * @param $new
     * @param string $type
     * @return string
     */
    public
    function filterLog($old, $new, $type = 'old')
    {
        if ($new == 'EventWorkflow/PUBLISHED' && $old == 'EventWorkflow/PUBLISHREQUEST') {
            $old = 'EventWorkflow/DRAFT';
        }
        if ($type == 'old') {
            return $old;
        }
        return $new;

    }

    /**
     * @param $event
     */
    public
    function logDeleteAction($event, $modelEvent = null)
    {
        if (!empty($modelEvent)) {
            $owner = $modelEvent;
        } else {
            /** @var Record $owner */
            $owner = $event->sender->owner;
        }
        $hash = hash('md5', date('Y-m-d H:i:s'));

        $fieldsLog = new EventChangeAttributes();
        $fieldsLog->event_id = $owner->id;
        $fieldsLog->user_id = \Yii::$app->user->id;
        $fieldsLog->operation_type = EventChangeAttributes::TYPE_DELETE;
        $fieldsLog->operation_group = $hash;

        $fieldsLog->model_classname = $owner->className();
        $fieldsLog->model_id = $owner->getPrimaryKey();
        $fieldsLog->model_attribute = null;

        $fieldsLog->old_value = null;
        $fieldsLog->new_value = null;

        $fieldsLog->save(false);
    }


    /**
     * @param $event
     * @param $owner
     * @return bool
     */
    public
    function isChanged($event, $oldAttributes)
    {
        foreach ($this->attributesToLog as $attributeName => $value) {
            $oldValue = (($event->name == ActiveRecord::EVENT_AFTER_INSERT) ? null : $oldAttributes[$attributeName]);
            $newValue = $this->attributesToLog[$attributeName];
            if ($oldValue != $newValue) {
                return true;
            }

        }
        return false;
    }


    // visto che nell'aggiornamento della location personalizzata gli id non cambiano è necessario un salvataggio personalizzato

    /**
     * @param $id
     * @param $eventLocation
     * @param $eventLocationEntrance
     * @param $oldLocation
     * @param $oldEntrance
     */
    public static function saveChangeLocationOther($id, $eventLocation, $eventLocationEntrance, $oldLocation, $oldEntrance)
    {
        $event = \open20\amos\events\models\Event::findOne($id);
        $oldValue = self::parseLocationAndEntrance($oldLocation, $oldEntrance);
        $newValue = self::parseLocationAndEntrance($eventLocation->name, $eventLocationEntrance->name);

        $changelog = new EventChangeAttributes();
        $changelog->old_value = $oldValue;
        $changelog->new_value = $newValue;
        $changelog->event_id = $id;
        $changelog->operation_type = EventChangeAttributes::TYPE_CHANGE;
        $changelog->operation_group = hash('md5', date('Y-m-d H:i:s'));
        $changelog->model_classname = \open20\amos\events\models\Event::className();
        $changelog->model_id = $id;
        $changelog->model_attribute = 'event_location_id';
        $changelog->user_id = \Yii::$app->user->id;
        $changelog->save(false);

        self::sendEmailChangedEvent($event, ['event_location_id' => ['oldValue' => $oldValue, 'newValue' => $newValue ] ]);
    }

    /**
     * @param $locationLabel
     * @param $entranceLabel
     * @return string
     */
    public
    static function parseLocationAndEntrance($locationLabel, $entranceLabel)
    {
        $location = [
            'event_location_id' => $locationLabel,
            'event_location_entrance_id' => $entranceLabel,
            'type' => 'label'
        ];
        return serialize($location);
    }
}
