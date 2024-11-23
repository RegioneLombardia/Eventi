<?php

namespace open20\amos\events\models;

use open20\amos\admin\utility\UserProfileUtility;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\core\user\User;
use open20\amos\events\AmosEvents;
use open20\amos\events\utility\EventsUtility;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "event_communication".
 */
class EventCommunication extends \open20\amos\events\models\base\EventCommunication
{

    const TYPE_INVITED_SENT = 1;
    const TYPE_REGISTERED = 2;
    const TYPE_INVITED_NOT_REGISTED = 3;
    const TYPE_NOMINAL = 4;
    const TYPE_REGISTERED_AND_ATTENDED = 5;
    const TYPE_REGISTERED_AND_NOT_ATTENDED = 6;
    const TYPE_MARKET_AUTOMATION_NOTAG = 7;
    const TYPE_MARKET_AUTOMATION_INACTIVE = 8;


    const TYPE_OF_CHANGE_DATE = 'date';
    const TYPE_OF_CHANGE_HOUR = 'hour';
    const TYPE_OF_CHANGE_PLACE = 'place';
    const TYPE_OF_CHANGE_SUSPENDED = 'suspended';
    const TYPE_OF_CHANGE_OTHER = 'other';


    const SENDING_TIME_IMMEDIATE = 1;
    const SENDING_TIME_SCHEDULED = 2;


    public $email;


    public function init()
    {
        parent::init();
        \Yii::$app->params['disableAfterFindPurify'] = true;
        if ($this->isNewRecord) {
            $this->text_email = self::defaultTextCommunication();
            $this->subject_email = self::defaultSubjectCommunication();
        }
    }

    public function afterFind()
    {
        parent::afterFind();
        EventsUtility::correctPurify($this);
    }

    /**
     * @return string
     */
    public static function defaultSubjectCommunication($type = null)
    {
        if ($type == self::TYPE_REGISTERED_AND_ATTENDED) {
            $text = "{NOME}, grazie per aver partecipato a {TITOLO}!";
        } else if ($type == self::TYPE_REGISTERED_AND_NOT_ATTENDED) {
            $text = "{NOME}, {TITOLO} si è concluso";
        } else {
            $text = AmosEvents::t('amosevents', "{NOME}, il tuo evento ha cambiato data!");
        }
        return $text;
    }

    /**
     * @return string
     */
    public static function defaultTextCommunication($type = null)
    {
        $module = \Yii::$app->getModule('events');
        $linkApp = null;
        if ($module) {
            $linkApp = $module->url_download_app_mobile;
        }
//        AmosEvents::t('amosevents', "Gentile {NOME} {COGNOME}, abbiamo cambiato la data dell'evento a cui ti sei iscritto.<br>
//            L'evento {TITOLO} si terrà il 01 marzo 2021.<br>
//            Ora e luogo restano invece invariati: {TITOLO} inizierà il 01 marzo 2021 alle ore {ORA_INIZIO} presso {LOCATION}, {INDIRIZZO}.<br>
//            Segna la data in agenda, noi ti aspettiamo!<br><br>
//            Non perderti tutti gli aggiornamenti: segui <a href='{URL_LANDING}'>la pagina dell'iniziativa</a> oppure scarica <a href='$linkApp'>l’app Eventi Lombardia</a>!
//            <br><br>
//            A presto
//        ");
        if ($type == self::TYPE_REGISTERED_AND_ATTENDED) {
            $text = "Gentile {NOME} {COGNOME}, 
                <br>grazie per aver partecipato all'evento {TITOLO}.
                <br>Continua a seguirci per rimanere aggiornato sui prossimi eventi di Regione Lombardia.
                <br><br>A presto!";
        } else if ($type == self::TYPE_REGISTERED_AND_NOT_ATTENDED) {
            $text = "Gentile {NOME} {COGNOME}, 
                <br>l'evento {TITOLO} del {DATA_INIZIO} si è concluso.
                <br>Ci dispiace non averti avuto con noi e ci auguriamo di vederti ai nostri prossimi eventi.
                <br><br>A presto!";
        } else {
            $text = AmosEvents::t('amosevents', "Gentile {NOME} {COGNOME}, abbiamo cambiato la data dell'evento a cui ti sei iscritto.<br>
            L'evento {TITOLO} si terrà il 01 marzo 2021.<br>
            Ora e luogo restano invece invariati: {TITOLO} inizierà il 01 marzo 2021 alle ore {ORA_INIZIO} presso {LOCATION}, {INDIRIZZO}.<br>
            Segna la data in agenda, noi ti aspettiamo!<br><br>
            Non perderti tutti gli aggiornamenti: segui <a href='{URL_LANDING}'>la pagina dell'iniziativa</a>.
            <br><br>
            A presto
        ");
        }
        return $text;
    }


    public static function textEventChanged($type = '')
    {

        switch ($type) {
            case self::TYPE_OF_CHANGE_DATE:
                $text = "Gentile {NOME} {COGNOME}, abbiamo cambiato la data dell'evento a cui ti sei iscritto.<br>
      L'evento {TITOLO} si terrà dal {DATA_INIZIO} al {DATA_FINE}.<br>
      Ora e luogo restano invece invariati: {TITOLO} inizierà il {DATA_INIZIO} alle ore {ORA_INIZIO} presso {LOCATION}, {INDIRIZZO}.<br>
      Segna la data in agenda, noi ti aspettiamo!
      <br><br>Non perderti tutti gli aggiornamenti: segui la <a href='{URL_LANDING}'>pagina dell'iniziativa</a>.
      <br><br>A presto";
                break;
            case self::TYPE_OF_CHANGE_HOUR:
                $text = "Gentile {NOME} {COGNOME}, abbiamo cambiato l'orario a cui ti sei iscritto.<br>
      L'evento {TITOLO} si terrà alle {ORA_INIZIO}.<br>
      Data e luogo restano invece invariati: {TITOLO} inizierà il {DATA_INIZIO} alle ore {ORA_INIZIO} presso {LOCATION}, {INDIRIZZO}.<br>
      Segna la data in agenda, noi ti aspettiamo!
      <br><br>Non perderti tutti gli aggiornamenti: segui la <a href='{URL_LANDING}'>pagina dell'iniziativa</a>.
      <br><br>A presto";
                break;
            case self::TYPE_OF_CHANGE_PLACE:
                $text = "Gentile {NOME} {COGNOME}, abbiamo cambiato il luogo dell'evento a cui ti sei iscritto.<br>
      L'evento {TITOLO} si terrà presso {LOCATION}, {INDIRIZZO}.<br>
      Data e ora restano invece invariati: {TITOLO} inizierà il {DATA_INIZIO} alle ore {ORA_INIZIO}.<br>
      Segna la data in agenda, noi ti aspettiamo!
      <br><br>Non perderti tutti gli aggiornamenti: segui la <a href='{URL_LANDING}'>pagina dell'iniziativa</a>.
      <br><br>A presto";
                break;
            case self::TYPE_OF_CHANGE_SUSPENDED:
                $text = "Gentile {NOME} {COGNOME}, ci dispiace informarti che l’evento {TITOLO} previsto per il {DATA_INIZIO} presso {LOCATION}, {INDIRIZZO} a cui ti eri registrato è stato annullato.<br>
      <br>A presto";
                break;
            default:
                $text = "Gentile {NOME} {COGNOME},sono stati apportati aggiornamenti importanti all'evento a cui ti sei iscritto.<br>
       L'evento {TITOLO} si terrà presso {LOCATION}, {INDIRIZZO}<br>{TITOLO} inizierà il {DATA_INIZIO} alle ore {ORA_INIZIO} .<br>
      Segna la data in agenda, noi ti aspettiamo!
      <br><br>Non perderti tutti gli aggiornamenti: segui la <a href='{URL_LANDING}'>pagina dell'iniziativa</a>.
      <br><br>A presto";
        }

        return $text;
    }

    public static function subjectEventChanged($type = '')
    {
        switch ($type) {
            case self::TYPE_OF_CHANGE_DATE:
                $text = "{NOME}, il tuo evento ha cambiato data!";
                break;
            case self::TYPE_OF_CHANGE_HOUR:
                $text = "{NOME}, il tuo evento ha cambiato orario!";
                break;
            case self::TYPE_OF_CHANGE_PLACE:
                $text = "{NOME}, il tuo evento ha cambiato luogo!";
                break;
            case self::TYPE_OF_CHANGE_SUSPENDED:
                $text = "{NOME}, il tuo evento è stato annullato!";
                break;
            default:
                $text = "{NOME}, aggiornamenti importanti per il tuo evento!";
        }
        return $text;
    }

    public static function subjectPushNotificationEventChanged($type = '')
    {
        switch ($type) {
            case self::TYPE_OF_CHANGE_DATE:
                $text = "Cambio data";
                break;
            case self::TYPE_OF_CHANGE_HOUR:
                $text = "Cambio orario";
                break;
            case self::TYPE_OF_CHANGE_PLACE:
                $text = "Cambio luogo";
                break;
            case self::TYPE_OF_CHANGE_SUSPENDED:
                $text = "Evento annullato";
                break;
            default:
                $text = "Aggiornamenti importanti per il tuo evento!";
        }
        return $text;
    }

    /**
     * @return array
     */
    public static function getCommunicationTypeLabels()
    {
        return [
            self::TYPE_INVITED_SENT => AmosEvents::t('amosevents', 'Tutti gli utenti invitati (a cui è stato spedita una email di tipo save the date o invito'),
            self::TYPE_REGISTERED => AmosEvents::t('amosevents', 'Solo gli utenti registrati (partecipanti)'),
            self::TYPE_INVITED_NOT_REGISTED => AmosEvents::t('amosevents', 'Solo gli utenti invitati che ancora non si sono registrati'),
            self::TYPE_NOMINAL => AmosEvents::t('amosevents', 'Selezione nominale'),
            self::TYPE_REGISTERED_AND_ATTENDED => AmosEvents::t('amosevents', 'Solo gli utenti registrati che hanno partecipato'),
            self::TYPE_REGISTERED_AND_NOT_ATTENDED => AmosEvents::t('amosevents', 'Solo gli utenti registrati che non hanno partecipato'),
            self::TYPE_MARKET_AUTOMATION_NOTAG => AmosEvents::t('amosevents', 'Tag non definiti'),
            self::TYPE_MARKET_AUTOMATION_INACTIVE => AmosEvents::t('amosevents', 'Inattivi'),
        ];
    }

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
            ['email', 'email']
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
                'slug' => 'communication_type',
                'label' => $labels['communication_type'],
                'type' => 'integer'
            ],
            [
                'slug' => 'n_users',
                'label' => $labels['n_users'],
                'type' => 'integer'
            ],
            [
                'slug' => 'text_email',
                'label' => $labels['text_email'],
                'type' => 'text'
            ],
            [
                'slug' => 'subject_email',
                'label' => $labels['subject_email'],
                'type' => 'text'
            ],
            [
                'slug' => 'status',
                'label' => $labels['status'],
                'type' => 'string'
            ],
            [
                'slug' => 'sent_at',
                'label' => $labels['sent_at'],
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
     * @param $type
     * @param $event_id
     * @return ActiveQuery
     */
    public function searchUsersQuery($event_id)
    {
        /** @var  $query ActiveQuery */
        $query = User::find()
            ->distinct()
            ->innerJoinWith('userProfile')
            ->andWhere(['user_profile.attivo' => 1])
            ->andWhere(['NOT LIKE', 'user_profile.nome', UserProfileUtility::DELETED_ACCOUNT_NAME]);

        switch ($this->communication_type) {
            case self::TYPE_INVITED_SENT:
                $query->innerJoin('event_invitation_sent', 'event_invitation_sent.user_id = user.id')
                    ->innerJoin('event_internal_list', 'event_invitation_sent.event_internal_list_id = event_internal_list.id')
                    ->andWhere(['event_internal_list.event_id' => $event_id])
                    ->groupBy('user_profile.user_id');

                break;
            case self::TYPE_REGISTERED:
                $query->innerJoin('community_user_mm', 'community_user_mm.user_id = user.id')
                    ->innerJoin('event_invitation', 'event_invitation.user_id = community_user_mm.user_id')
                    ->innerJoin('event', 'event.community_id = community_user_mm.community_id')
                    ->andWhere(['event.id' => $event_id])
                    ->andWhere(['event_invitation.event_id' => $event_id])
                    ->andWhere(['community_user_mm.status' => CommunityUserMm::STATUS_ACTIVE])
                    ->andWhere(['!=', 'community_user_mm.role', Event::EVENT_MANAGER])
                    ->andWhere(['event_invitation.deleted_at' => null])
                    ->andWhere(['community_user_mm.deleted_at' => null])
                    ->groupBy('user_profile.user_id');
                break;
            case self::TYPE_INVITED_NOT_REGISTED:
                $query2 = clone $query;
                $query2->select('user.id')->innerJoin('community_user_mm', 'community_user_mm.user_id = user.id')
                    ->innerJoin('event', 'event.community_id = community_user_mm.community_id')
                    ->innerJoin('event_invitation', 'event_invitation.user_id = community_user_mm.user_id')
                    ->andWhere(['event.id' => $event_id])
                    ->andWhere(['event_invitation.event_id' => $event_id])
                    ->andWhere(['community_user_mm.status' => CommunityUserMm::STATUS_ACTIVE])
                    ->groupBy('user_profile.user_id');


                $query->innerJoin('event_invitation_sent', 'event_invitation_sent.user_id = user.id')
                    ->innerJoin('event_internal_list', 'event_invitation_sent.event_internal_list_id = event_internal_list.id')
                    ->andWhere(['event_internal_list.event_id' => $event_id])
                    ->andWhere(['not in', 'user.id', $query2])
                    ->groupBy('user_profile.user_id');

                break;
            case self::TYPE_REGISTERED_AND_ATTENDED:
                $query->innerJoin('community_user_mm', 'community_user_mm.user_id = user.id')
                    ->innerJoin('event_invitation', 'event_invitation.user_id = community_user_mm.user_id')
                    ->innerJoin('event', 'event.community_id = community_user_mm.community_id')
                    ->andWhere(['event.id' => $event_id])
                    ->andWhere(['event_invitation.event_id' => $event_id])
                    ->andWhere(['event_invitation.presenza' => 1])
                    ->andWhere(['community_user_mm.status' => CommunityUserMm::STATUS_ACTIVE])
                    ->andWhere(['!=', 'community_user_mm.role', Event::EVENT_MANAGER])
                    ->andWhere(['event_invitation.deleted_at' => null])
                    ->andWhere(['community_user_mm.deleted_at' => null])
                    ->groupBy('user_profile.user_id');
                break;
            case self::TYPE_REGISTERED_AND_NOT_ATTENDED:
                $query->innerJoin('community_user_mm', 'community_user_mm.user_id = user.id')
                    ->innerJoin('event_invitation', 'event_invitation.user_id = community_user_mm.user_id')
                    ->innerJoin('event', 'event.community_id = community_user_mm.community_id')
                    ->andWhere(['event.id' => $event_id])
                    ->andWhere(['event_invitation.event_id' => $event_id])
                    ->andWhere(['event_invitation.presenza' => 0])
                    ->andWhere(['community_user_mm.status' => CommunityUserMm::STATUS_ACTIVE])
                    ->andWhere(['!=', 'community_user_mm.role', Event::EVENT_MANAGER])
                    ->andWhere(['event_invitation.deleted_at' => null])
                    ->andWhere(['community_user_mm.deleted_at' => null])
                    ->groupBy('user_profile.user_id');
                break;

            default:
                $query->andWhere(0);
        }
        return $query;
    }

    /**
     * @param $interval
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function searchUsersMarketingAutomationQuery($interval)
    {

        if (empty($interval)) {
            $interval = '1 MONTH';
        }
        /** @var  $query ActiveQuery */
        $query = User::find()
            ->distinct()
            ->innerJoinWith('userProfile')
            ->andWhere(['user_profile.attivo' => 1])
            ->andWhere(['NOT LIKE', 'user_profile.nome', UserProfileUtility::DELETED_ACCOUNT_NAME]);

        switch ($this->communication_type) {
            case self::TYPE_MARKET_AUTOMATION_NOTAG:
                $query->andWhere(['user_profile.privacy_2' => 0])
                    ->andWhere(['>', 'user_profile.created_at', new Expression("DATE_SUB(NOW(), INTERVAL $interval)")])
                    ->groupBy('user_profile.user_id');
                break;
            case self::TYPE_MARKET_AUTOMATION_INACTIVE:
                $query->andWhere(['<', 'user_profile.ultimo_accesso', new Expression("DATE_SUB(NOW(), INTERVAL $interval)")])
                    ->groupBy('user_profile.user_id');
                break;
            default:
                $query->andWhere(0);
        }
//        pr($query->createCommand()->rawSql);die;
//
        return $query;
    }

    /**
     * @param $type
     * @return string
     */
    public static function defaultSubjectMarketingAutomation($type)
    {
        $text = '';
        switch ($type) {
            case self::TYPE_MARKET_AUTOMATION_NOTAG:
                $text = "{NOME}, aggiorna le tue preferenze.";
                break;
            case self::TYPE_MARKET_AUTOMATION_INACTIVE:
                $text = "{NOME}, prossimi appuntamenti";
                break;
        }
        return $text;
    }

    /**
     * @param $type
     * @return string
     */
    public static function defaultTextMarketingAutomation($type)
    {
        $text = '';
        switch ($type) {
            case self::TYPE_MARKET_AUTOMATION_NOTAG:
                $urlTag = \Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/update-my-profile#tab-preferencetags';
                $text = "Gentile {NOME} {COGNOME}, 
            <br>grazie per esserti iscritto al portale Eventi di Regione Lombardia.
            <br>Per permetterci di proporti eventi in linea con i tuoi interessi clicca <a href='$urlTag'>qui</a> per aggiornare le tue preferenze.
            <br><br>A presto!";
                break;
            case self::TYPE_MARKET_AUTOMATION_INACTIVE:
                $urlHome = \Yii::$app->params['platform']['frontendUrl'] . '/esplora-eventi';
                $text = "Gentile {NOME} {COGNOME}, 
            <br>qui alcuni appuntamenti da non perdere per il mese di {MESE_CORRENTE}:
            <br>{EVENTI_MESE_CORRENTE}
            <br>Clicca <a href='$urlHome'>qui</a> per scoprire il calendario completo dei prossimi eventi di Regione Lombardia.
            <br><br>Ti aspettiamo!";
                break;
        }
        return $text;
    }


    /**
     * @param $communicationsIds
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getStatusSending($communicationsIds)
    {
        $statuses = [];

        $newsletterModule = \Yii::$app->getModule('newsletter');
        if ($newsletterModule) {
            $mailServiceClassname = $newsletterModule->mail_service_driver;
            $mailService = new $mailServiceClassname();
            $communications = EventCommunication::find()->andWhere(['id' => $communicationsIds])->all();
            $sendingIdsLists = [];
            $sendingIds = [];
            foreach ($communications as $communication) {
                $sendingIdsLists[$communication->mailup_sending_id] = $communication->id;
                $sendingIds[] = $communication->mailup_sending_id;
            }

            $reportSendingOngoing = $mailService->getOngoingSending();
            if (!empty($reportSendingOngoing)) {
                foreach ($reportSendingOngoing->Items as $item) {
                    if (!empty($item->Id)) {
                        if (in_array($item->Id, $sendingIds)) {
                            $decodedHistory = $mailService->getEmailSendHistory($item->IdList, $item->IdMessage, []);
                            $n = 0;
                            if (!empty($decodedHistory->Items)) {
                                $elem = $decodedHistory->Items[0];
                                $n = $elem->Recipients;
                            }
                            $statuses[$sendingIdsLists[$item->Id]] = ['status' => 'running', 'n_sent' => $n];
                        }
                    }

                }
            }

            $reportSendingWiting = $mailService->getWaitingSending();//                        pr($reportSendingWiting, 'waiting');
            ;
            if (!empty($reportSendingWiting)) {
                foreach ($reportSendingWiting->Items as $item) {
                    if (!empty($item->Id)) {
                        if (in_array($item->Id, $sendingIds)) {
                            $statuses[$sendingIdsLists[$item->Id]] = ['status' => 'waiting'];
                        }
                    }

                }
            }
        }

        return $statuses;
    }


    /**
     * @param $statuses
     * @return string
     */
    public function getSingleStatusSending($statuses)
    {
        if ($this->status == \open20\amos\events\models\EventInternalList::STATUS_SENDING) {
            if (!empty($statuses[$this->id])) {
                if ($statuses[$this->id]['status'] == 'waiting') {
                    return AmosEvents::t('amosevents', 'In coda');
                } else {
                    $percent = round(((int)$statuses[$this->id]['n_sent'] / $this->n_users) * 100);
                    return AmosEvents::t('amosevents', "Invio in corso") . ': ' . $percent . '%';
                }
            } else {
                return AmosEvents::t('amosevents', 'Invio completato');
            }
        } else {
            if ($this->status) {
                return self::getLabelStatus()[$this->status];
            }
        }
        return AmosEvents::t('amosevents', "Bozza");
    }

    /**
     * @return bool
     */
    public function canSend()
    {
        if ($this->time_schedule_type == EventInternalList::SENDING_TIME_SCHEDULED) {
            $now = new \DateTime('now', new \DateTimeZone('Europe/Rome'));
            $scheduledDate = new \DateTime($this->time_schedule_date, new \DateTimeZone('Europe/Rome'));
            return $now >= $scheduledDate;
        }
        return true;
    }


    /**
     * @return string
     */
    public function getRappresentativeName()
    {
        return $this->title;
    }

    /**
     * @param $event Event
     */
    public function getCommunicationTypes($event)
    {

        $types = [
            EventCommunication::TYPE_INVITED_SENT => EventCommunication::getCommunicationTypeLabels()[EventCommunication::TYPE_INVITED_SENT],
            EventCommunication::TYPE_REGISTERED => EventCommunication::getCommunicationTypeLabels()[EventCommunication::TYPE_REGISTERED],
            EventCommunication::TYPE_INVITED_NOT_REGISTED => EventCommunication::getCommunicationTypeLabels()[EventCommunication::TYPE_INVITED_NOT_REGISTED],
        ];
        if ($event->has_tickets) {
            $types[self::TYPE_REGISTERED_AND_ATTENDED] = EventCommunication::getCommunicationTypeLabels()[EventCommunication::TYPE_REGISTERED_AND_ATTENDED];
            $types[self::TYPE_REGISTERED_AND_NOT_ATTENDED] = EventCommunication::getCommunicationTypeLabels()[EventCommunication::TYPE_REGISTERED_AND_NOT_ATTENDED];
        }
        $types[self::TYPE_NOMINAL] = EventCommunication::getCommunicationTypeLabels()[EventCommunication::TYPE_NOMINAL];

        return $types;
    }

    /**
     * @param $type
     * @return string
     */
    public static function getActionForMarketingTypeCommunication($type)
    {
        if ($type == self::TYPE_MARKET_AUTOMATION_INACTIVE) {
            return 'inactive';
        } else {
            return 'tag-not-defined';
        }
    }

    /**
     * @return array
     */
    public static function getOptionsFilterMarketingAutomation()
    {
        return [
            '1 MONTH' => AmosEvents::t('amosevents', "1 mese"),
            '2 MONTH' => AmosEvents::t('amosevents', "2 mesi"),
            '3 MONTH' => AmosEvents::t('amosevents', "3 mesi"),
            '6 MONTH' => AmosEvents::t('amosevents', "6 mesi"),
        ];
    }
}
