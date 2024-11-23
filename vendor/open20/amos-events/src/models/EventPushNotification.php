<?php

namespace open20\amos\events\models;

use open20\amos\events\AmosEvents;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "event_push_notification".
 */
class EventPushNotification extends \open20\amos\events\models\base\EventPushNotification
{
    const TYPE_SAVE_THE_DATE = 'save_the_date';
    const TYPE_INVITE_REGISTER = 'registration_email';
    const TYPE_NEW_CONTENT = 'new_content';
    const TYPE_EVENT_CHANGED = 'event_changed';

    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_DONT_SEND = 'dont_send';

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
                'slug' => 'type',
                'label' => $labels['type'],
                'type' => 'text'
            ],
            [
                'slug' => 'content_class',
                'label' => $labels['content_class'],
                'type' => 'string'
            ],
            [
                'slug' => 'content_id',
                'label' => $labels['content_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'status',
                'label' => $labels['status'],
                'type' => 'string'
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

    public function getTitlePushNotification()
    {
        $type = $this->type;
        $subtype = $this->subtype;
        $title = '';
        switch ($type) {
            case EventPushNotification::TYPE_SAVE_THE_DATE:
                $title = AmosEvents::t('amosevents', "Save the date");
                break;

            case EventPushNotification::TYPE_INVITE_REGISTER:
                $title = AmosEvents::t('amosevents', "Invito alla registrazione");
                break;

            case EventPushNotification::TYPE_EVENT_CHANGED:
                switch ($subtype) {
                    case EventCommunication::TYPE_OF_CHANGE_PLACE:
                        $title = AmosEvents::t('amosevents', "Variazione luogo");
                        break;
                    case EventCommunication::TYPE_OF_CHANGE_HOUR:
                        $title = AmosEvents::t('amosevents', "Variazione orario");
                        break;
                    case EventCommunication::TYPE_OF_CHANGE_DATE:
                        $title = AmosEvents::t('amosevents', "Variazione data");
                        break;
                    default:
                        $title = AmosEvents::t('amosevents', "Aggiornamenti Evento");
                        break;
                }
                break;

        }
        return $title;
    }


    /**
     * @param $type
     * @param $subtype
     * @return string
     */
    public function getTextPushNotification()
    {
        $type = $this->type;
        $subtype = $this->subtype;
        $title = '';
        switch ($type) {
            case EventPushNotification::TYPE_SAVE_THE_DATE:
                $title = "{title}, è in arrivo un fantastico evento!";
                break;

            case EventPushNotification::TYPE_INVITE_REGISTER:
                $title = "Ti invitiamo a iscriverti all’evento {title}";
                break;

            case EventPushNotification::TYPE_EVENT_CHANGED:
                switch ($subtype) {
                    case EventCommunication::TYPE_OF_CHANGE_DATE:
                        $title = "Il tuo evento ha cambiato data. L'evento {TITOLO} si terrà dal {DATA_INIZIO} al {DATA_FINE}.
\nOra e luogo restano invece invariati: {TITOLO} inizierà il {DATA_INIZIO} alle ore {ORA_INIZIO} presso {LOCATION}, {INDIRIZZO}.";
                        break;
                    case EventCommunication::TYPE_OF_CHANGE_HOUR:
                        $title = "Il tuo evento ha cambiato orario. L'evento {TITOLO} si terrà alle ore {ORA_INIZIO}.
\nLa data e il luogo restano invece invariato: inizierà il {DATA_INIZIO} alle ore {ORA_INIZIO} presso {LOCATION}, {INDIRIZZO}.";
                        break;
                    case EventCommunication::TYPE_OF_CHANGE_PLACE:
                        $title = "Il tuo evento ha cambiato luogo. L'evento {TITOLO} si terrà presso {LOCATION}, {INDIRIZZO}.
\nDate e ora restano invece invariati: {TITOLO} inizierà il {DATA_INIZIO} alle ore {ORA_INIZIO}.";
                        break;
                    default:
                        $title = "Il tuo evento è stato aggiornato.
\nL'evento {TITOLO} si terrà presso {LOCATION}, {INDIRIZZO}<br>{TITOLO} inizierà il {DATA_INIZIO} alle ore {ORA_INIZIO}";
                        break;
                }
                break;

        }
        return $title;

    }


}
