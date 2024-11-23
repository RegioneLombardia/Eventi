<?php

namespace open20\amos\events\models;

use open20\amos\een\AmosEen;
use open20\amos\events\AmosEvents;
use open20\amos\events\utility\EventsUtility;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "event_email_templates".
 */
class EventEmailTemplates extends \open20\amos\events\models\base\EventEmailTemplates
{
    const TEMPLATE_SAVE_THE_DATE = 'save_the_date';
    const TEMPLATE_REGISTRATION_EMAIL = 'registration_email';
    const TEMPLATE_CONFIRM_REGISTRATION = 'confirm_registration';
    const TEMPLATE_SEND_TICKETS = 'send_tickets';
    const TEMPLATE_WAITING_LIST = 'info_waiting_list';
    const TEMPLATE_WEBMEETING_WEBEX = 'webmeeting_webex';
    const TEMPLATE_DATE_WEBMEETING_WEBEX_SAVE_DATE = 'webmeeting_webex_save_date';
    const TEMPLATE_WEBMEETING_WEBEX_CONFIRM_REG = 'webmeeting_webex_confirm_reg';


    const FOOTER_TYPE_NO_FOOTER = 'no_footer';
    const FOOTER_TYPE_TAG_AND_UNSUBSCRIBE = 'tag_unsubscribe_platform_footer';
    const FOOTER_TYPE_ALL_FOOTER = 'all_footer';

    public $email_test_1;
    public $email_test_2;
    public $email_test_3;
    public $email_test_4;
    public $email_test_5;
    public $email_test_6;
    public $email_test_7;
    public $email_test_8;

    public function getTypeEmailTest($string)
    {
        switch ($string) {
            case 'email_test_1' :
                return 'save_the_date';
                break;
            case 'email_test_2' :
                return 'registration_email';
                break;
            case 'email_test_3' :
                return 'confirm_registration';
                break;
            case 'email_test_4' :
                return 'send_tickets';
                break;
            case 'email_test_5' :
                return 'info_waiting_list';
                break;
            case 'email_test_6' :
                return 'webmeeting_webex';
                break;
            case 'email_test_7' :
                return 'webmeeting_webex_save_date';
                break;
            case 'email_test_8' :
                return 'webmeeting_webex_confirm_reg';
                break;
            default:
                return 'save_the_date';
        }

    }

    public function afterFind()
    {
        parent::afterFind();
        EventsUtility::correctPurify($this);
    }


    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            $this->save_the_date = self::getDefaultText()[self::TEMPLATE_SAVE_THE_DATE];
            $this->registration_email = self::getDefaultText()[self::TEMPLATE_REGISTRATION_EMAIL];
            $this->confirm_registration = self::getDefaultText()[self::TEMPLATE_CONFIRM_REGISTRATION];
            $this->send_tickets = self::getDefaultText()[self::TEMPLATE_SEND_TICKETS];
            $this->info_waiting_list = self::getDefaultText()[self::TEMPLATE_WAITING_LIST];
            $this->webmeeting_webex = self::getDefaultText()[self::TEMPLATE_WEBMEETING_WEBEX];
            $this->webmeeting_webex_save_date = self::getDefaultText()[self::TEMPLATE_DATE_WEBMEETING_WEBEX_SAVE_DATE];
            $this->webmeeting_webex_confirm_reg = self::getDefaultText()[self::TEMPLATE_WEBMEETING_WEBEX_CONFIRM_REG];


            $this->save_the_date_subject = self::getDefaultSubject()[self::TEMPLATE_SAVE_THE_DATE . '_subject'];
            $this->registration_email_subject = self::getDefaultSubject()[self::TEMPLATE_REGISTRATION_EMAIL . '_subject'];
            $this->confirm_registration_subject = self::getDefaultSubject()[self::TEMPLATE_CONFIRM_REGISTRATION . '_subject'];
            $this->send_tickets_subject = self::getDefaultSubject()[self::TEMPLATE_SEND_TICKETS . '_subject'];
            $this->info_waiting_list_subject = self::getDefaultSubject()[self::TEMPLATE_WAITING_LIST . '_subject'];
            $this->webmeeting_webex_subject = self::getDefaultSubject()[self::TEMPLATE_WEBMEETING_WEBEX . '_subject'];
            $this->webmeeting_webex_save_date_subject = self::getDefaultSubject()[self::TEMPLATE_DATE_WEBMEETING_WEBEX_SAVE_DATE . '_subject'];
            $this->webmeeting_webex_confirm_reg_subject = self::getDefaultSubject()[self::TEMPLATE_WEBMEETING_WEBEX_CONFIRM_REG . '_subject'];


        }
    }

    /**
     * @return array
     */
    public static function getLabelTemplate()
    {
        return [
            'save_the_date' => AmosEvents::t('amosevents', "Save the date"),
            'registration_email' => AmosEvents::t('amosevents', "Invito alla registrazione"),
            'info_waiting_list' => AmosEvents::t('amosevents', "Info lista di attesa"),
            'confirm_registration' => AmosEvents::t('amosevents', "Conferma registrazione"),
            'send_tickets' => AmosEvents::t('amosevents', "Invio biglietti"),
            'webmeeting_webex' => AmosEvents::t('amosevents', "Invito alla registrazione Webex"),
            'webmeeting_webex_save_date' => AmosEvents::t('amosevents', "Save the date Webex"),
            'webmeeting_webex_confirm_reg' => AmosEvents::t('amosevents', "Conferma registrazione Webex"),
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
            [['email_test_1', 'email_test_2', 'email_test_3', 'email_test_4', 'email_test_5', 'email_test_6', 'email_test_7', 'email_test_8'], 'safe']
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
                'slug' => 'save_the_date',
                'label' => $labels['save_the_date'],
                'type' => 'text'
            ],
            [
                'slug' => 'save_the_date_subject',
                'label' => $labels['save_the_date_subject'],
                'type' => 'text'
            ],
            [
                'slug' => 'registration_email',
                'label' => $labels['registration_email'],
                'type' => 'text'
            ],
            [
                'slug' => 'registration_email_subject',
                'label' => $labels['registration_email_subject'],
                'type' => 'text'
            ],
            [
                'slug' => 'confirm_registration',
                'label' => $labels['confirm_registration'],
                'type' => 'text'
            ],
            [
                'slug' => 'confirm_registration_subject',
                'label' => $labels['confirm_registration_subject'],
                'type' => 'text'
            ],
            [
                'slug' => 'info_waiting_list',
                'label' => $labels['info_waiting_list'],
                'type' => 'text'
            ],
            [
                'slug' => 'info_waiting_list_subject',
                'label' => $labels['info_waiting_list_subject'],
                'type' => 'text'
            ],
            [
                'slug' => 'send_tickets',
                'label' => $labels['send_tickets'],
                'type' => 'text'
            ],
            [
                'slug' => 'send_tickets_subject',
                'label' => $labels['send_tickets_subject'],
                'type' => 'text'
            ],
            [
                'slug' => 'webmeeting_webex',
                'label' => $labels['webmeeting_webex'],
                'type' => 'text'
            ],
            [
                'slug' => 'webmeeting_webex_subject',
                'label' => $labels['webmeeting_webex_subject'],
                'type' => 'text'
            ],
            [
                'slug' => 'webmeeting_webex_save_date',
                'label' => $labels['webmeeting_webex_save_date'],
                'type' => 'text'
            ],
            [
                'slug' => 'webmeeting_webex_save_date_subject',
                'label' => $labels['webmeeting_webex_save_date_subject'],
                'type' => 'text'
            ],
            [
                'slug' => 'webmeeting_webex_confirm_reg',
                'label' => $labels['webmeeting_webex_confirm_reg'],
                'type' => 'text'
            ],
            [
                'slug' => 'webmeeting_webex_confirm_reg_subject',
                'label' => $labels['webmeeting_webex_confirm_reg_subject'],
                'type' => 'text'
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
     * @return array
     */
    public function getDefaultText()
    {
        $module = \Yii::$app->getModule('events');
        $linkApp = null;
        if ($module) {
            $linkApp = $module->url_download_app_mobile;
        }
//        return [
//            'save_the_date' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br>siamo felici di presentarti in anteprima l’evento '{TITOLO}' che si terrà il {DATA_INIZIO} presso {LOCATION} {INDIRIZZO}.<br><br>Di cosa si tratta?<br>'{TITOLO}' è {DESCRIZIONE}.<br><br>Siamo sicuri che l’iniziativa sia di tuo interesse: segna la data del {DATA_INIZIO} sull’agenda, noi ti aspettiamo!<br><br> Segui la pagina dell'iniziativa per tutti gli aggiornamenti: puoi farlo <a href=\"{URL_LANDING}\">qui</a> oppure sulla nostra <a href='$linkApp'>app Eventi Lombardia</a><br><br> A presto!</p>"),
//            'registration_email' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br>siamo felici di invitarti all’evento '{TITOLO}' che si terrà il {DATA_INIZIO} presso {LOCATION} {INDIRIZZO}.<br><br> Di cosa si tratta?<br>'{TITOLO}' è {DESCRIZIONE}. Siamo sicuri che l’iniziativa sia di tuo interesse: ricordati di completare la registrazione entro le {ORA_CHIUSURA_ISCRIZIONI} del {DATA_CHIUSURA_ISCRIZIONI} per non perdertelo! <br><br> Segui la pagina dell'iniziativa per tutti gli aggiornamenti: puoi farlo <a href='{URL_LANDING}'>qui</a> oppure sulla nostra <a href='$linkApp'>app Eventi Lombardia</a>.<br><br> A presto!</p>"),
//            'info_waiting_list' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br>ci dispiace doverti informare che abbiamo esaurito i posti per'{TITOLO}' in programma {DATA_INIZIO} presso {LOCATION} {INDIRIZZO}.<br><br> Abbiamo però inserito il tuo nominativo in lista di attesa e ti contatteremo appena tornerà disponibile un posto. <br><br> Per ora ti ringraziamo, continua a seguire la <a href='{URL_LANDING}'>pagina dell'evento</a> per ogni aggiornamento!<br><br>C’è anche un modo più veloce per farlo: scarica <a href='$linkApp'>l’app Eventi Lombardia!</a><br><br> A presto.</p>"),
//            'confirm_registration' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br>ti ricordiamo che l’inizio dell’evento {TITOLO} è in programma per le ore {ORA_INIZIO} del {DATA_INIZIO} presso {LOCATION}.<br>Continua a seguirci per ogni aggiornamento!<br>Puoi farlo <a href='{URL_LANDING}'>qui</a> oppure sulla nostra <a href='$linkApp'>app Eventi Lombardia</a>. <br><br>A presto, ti aspettiamo!</p>"),
//            'send_tickets' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br>siamo felici di inviarti il biglietto per l’evento '{TITOLO}' che si terrà il {DATA_INIZIO} presso {LOCATION} {INDIRIZZO}.<br><br>Il biglietto è nominativo e ti garantirà l’accesso a {LOCATION} che ospiterà l’iniziativa. L’accesso sarà consentito solo previo riconoscimento personale. Ti aspettiamo!<br><br>Segui <a href='{URL_LANDING}'>la pagina dell'iniziativa</a> per tutti gli aggiornamenti oppure scarica <a href='$linkApp'>l’app Eventi Lombardia</a>!<br><br> A presto!</p>"),
//        ];
        return [
            'save_the_date' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br>siamo felici di presentarti in anteprima l’evento '{TITOLO}' che si terrà il {DATA_INIZIO} presso {LOCATION} {INDIRIZZO}.<br><br>Di cosa si tratta?<br>'{TITOLO}' - {DESCRIZIONE}.<br><br>Siamo sicuri che l’iniziativa sia di tuo interesse: segna la data del {DATA_INIZIO} sull’agenda, noi ti aspettiamo!<br><br> Segui la pagina dell'iniziativa per tutti gli aggiornamenti: puoi farlo <a href=\"{URL_LANDING}\">qui</a>.<br><br> A presto!</p>"),
            'registration_email' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br>siamo felici di invitarti all’evento '{TITOLO}' che si terrà il {DATA_INIZIO} presso {LOCATION} {INDIRIZZO}.<br><br> Di cosa si tratta?<br>'{TITOLO}' - {DESCRIZIONE}. Siamo sicuri che l’iniziativa sia di tuo interesse: ricordati di completare la registrazione entro le {ORA_CHIUSURA_ISCRIZIONI} del {DATA_CHIUSURA_ISCRIZIONI} per non perdertelo! <br><br> Segui la pagina dell'iniziativa per tutti gli aggiornamenti: puoi farlo <a href='{URL_LANDING}'>qui</a>.<br><br> A presto!</p>"),
            'info_waiting_list' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br>ci dispiace doverti informare che abbiamo esaurito i posti per'{TITOLO}' in programma {DATA_INIZIO} presso {LOCATION} {INDIRIZZO}.<br><br> Abbiamo però inserito il tuo nominativo in lista di attesa e ti contatteremo appena tornerà disponibile un posto. <br><br> Per ora ti ringraziamo, continua a seguire la <a href='{URL_LANDING}'>pagina dell'evento</a> per ogni aggiornamento!<br><br> A presto.</p>"),
            'confirm_registration' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br>ti ricordiamo che l’inizio dell’evento {TITOLO} è in programma per le ore {ORA_INIZIO} del {DATA_INIZIO} presso {LOCATION}.<br>Continua a seguirci per ogni aggiornamento!<br>Puoi farlo <a href='{URL_LANDING}'>qui</a></p>"),
            'send_tickets' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br>siamo felici di inviarti il biglietto per l’evento '{TITOLO}' che si terrà il {DATA_INIZIO} presso {LOCATION} {INDIRIZZO}.<br><br>Il biglietto è nominativo e ti garantirà l’accesso a {LOCATION} che ospiterà l’iniziativa. L’accesso sarà consentito solo previo riconoscimento personale. Ti aspettiamo!<br><br>Segui <a href='{URL_LANDING}'>la pagina dell'iniziativa</a><br><br> A presto!</p>"),
            'webmeeting_webex' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br>ti informiamo che il {DATA_INIZIO} si terrà una sessione di videoconferenza con tecnologia Cisco WebEx.<br><br> Argomento della videoconferenza?<br>'{TITOLO}' - {DESCRIZIONE}. Se sei interessato a partecipare ricordati di completare la registrazione entro le {ORA_CHIUSURA_ISCRIZIONI} del {DATA_CHIUSURA_ISCRIZIONI} per poterti inserire tra i partecipanti autorizzati.<br><br>Tutte le informazioni sulle modalità di partecipazione verranno pubblicate nella pagina dedicata all'evento <a href='{URL_LANDING}'>qui</a>.<br><br> A presto!</p>"),
            'webmeeting_webex_save_date' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br>ti informiamo che il {DATA_INIZIO} si terrà una sessione di videoconferenza con tecnologia Cisco WebEx.<br><br> Argomento della videoconferenza?<br>'{TITOLO}' - {DESCRIZIONE}.<br><br>Tutte le informazioni sulle modalità di partecipazione verranno pubblicate nella pagina dedicata all'evento <a href='{URL_LANDING}'>qui</a>.<br><br> A presto!</p>"),
            'webmeeting_webex_confirm_reg' => AmosEvents::t('amosevents', "<p>Gentile {NOME} {COGNOME}, <br> ti ricordiamo che la sessione di videoconferenza {TITOLO} è in programma per le ore {ORA_INIZIO} del {DATA_INIZIO}.<br><br>Tutte le informazioni sulle modalità di partecipazione verranno pubblicate nella pagina dedicata all'evento <a href='{URL_LANDING}'>qui</a>.<br><br> A presto!</p>"),

        ];
    }

    /**
     * @return array
     */
    public function getDefaultSubject()
    {
        return [
            'save_the_date_subject' => AmosEvents::t('amosevents', "{NOME}, il {DATA_INIZIO} è in arrivo un fantastico evento!"),
            'registration_email_subject' => AmosEvents::t('amosevents', "{NOME}, ti invitiamo a iscriverti all’evento '{TITOLO}'!"),
            'info_waiting_list_subject' => AmosEvents::t('amosevents', "{NOME}, abbiamo esaurito i posti (per ora)!"),
            'confirm_registration_subject' => AmosEvents::t('amosevents', "Registrazione {TITOLO}"),
            'send_tickets_subject' => AmosEvents::t('amosevents', "{NOME}, ecco il tuo biglietto per '{TITOLO}'!"),
            'webmeeting_webex_subject' => AmosEvents::t('amosevents', "{NOME}, ti invitiamo a iscriverti all’evento con sessione di videoconferenza '{TITOLO}'!"),
            'webmeeting_webex_save_date_subject' => AmosEvents::t('amosevents', "{NOME}, è in programma un evento con sessione di videoconferenza '{TITOLO}'!"),
            'webmeeting_webex_confirm_reg_subject' => AmosEvents::t('amosevents', "Registrazione {TITOLO}"),

        ];
    }

    /**
     * @return array
     */
    public function getFooterText()
    {
        return [
            'save_the_date' => self::FOOTER_TYPE_TAG_AND_UNSUBSCRIBE,
            'registration_email' => self::FOOTER_TYPE_TAG_AND_UNSUBSCRIBE,
            'info_waiting_list' => self::FOOTER_TYPE_TAG_AND_UNSUBSCRIBE,
            'confirm_registration' => self::FOOTER_TYPE_ALL_FOOTER,
            'send_tickets' => self::FOOTER_TYPE_TAG_AND_UNSUBSCRIBE,
            'webmeeting_webex' => self::FOOTER_TYPE_TAG_AND_UNSUBSCRIBE,
            'webmeeting_webex_save_date' => self::FOOTER_TYPE_TAG_AND_UNSUBSCRIBE,
            'webmeeting_webex_confirm_reg' => self::FOOTER_TYPE_ALL_FOOTER,
        ];
    }

    /**
     * @return array
     */
    public function getDefaultTextEng()
    {
        return [
            'save_the_date' => "<p>Dear {NOME} {COGNOME}, <br>we are happy to show you the event '{TITOLO}' that will start at {DATA_INIZIO} in {LOCATION} {INDIRIZZO}.<br><br> What is about?<br>'{TITOLO}' - {DESCRIZIONE}.<br><br>We are sure this initiative is of your interest: save the date {DATA_INIZIO} in your calendar, we will wait for you!<br><br> Follow the page of the event if yuo want to stay up to date: you can do it <a href=\"{URL_LANDING}\">here</a>.<br><br> See you soon!</p>",
            'registration_email' =>"<p>Dear {NOME} {COGNOME}, <br>we are happy to invite you to the event '{TITOLO}' that will start at {DATA_INIZIO} in {LOCATION} {INDIRIZZO}.<br><br> What is about?<br>'{TITOLO}' - {DESCRIZIONE}. We are sure the event is of your interest: remember to subscribe before {ORA_CHIUSURA_ISCRIZIONI} of {DATA_CHIUSURA_ISCRIZIONI} if you don't want to miss it! <br><br> Follow the page of the event if yuo want stay up to date: you can do it <a href='{URL_LANDING}'>here</a>.<br><br> See you soon!</p>",
            'info_waiting_list' => "<p>Dear {NOME} {COGNOME}, <br>we are sorry to inform you that the seats for the event '{TITOLO}' that will start at {DATA_INIZIO} in {LOCATION} {INDIRIZZO} are all sold out.<br><br> We have saved your name on the waiting list and we will cantact you when a seat will become again available. <br><br> Thank you, continuo to follow the <a href='{URL_LANDING}'>event's page</a> if you want stay up to date!<br><br> See you soon.</p>",
            'confirm_registration' =>"<p>Dear {NOME} {COGNOME}, <br>we remember you that the event {TITOLO} will start at {ORA_INIZIO} {DATA_INIZIO} in {LOCATION}.<br>Continue to follow us if you want to be updated!<br>You can do it <a href='{URL_LANDING}'>here</a></p>",
            'send_tickets' =>"<p>Dear {NOME} {COGNOME}, <br>we are happy to send you the ticket for the event '{TITOLO}' that will start at {DATA_INIZIO} in {LOCATION} {INDIRIZZO}.<br><br>Your ticket is nominal and with it you can access to {LOCATION} where will take place the event. The access will be available only after prsonal recognition. We will wait you!<br><br>Follow us in the <a href='{URL_LANDING}'>page of the initiative</a><br><br> See you soon!</p>",
            'webmeeting_webex' =>"<p>Dear {NOME} {COGNOME}, <br> we inform you that the videoconference session is made with the technology Cisco WebEx.<br><br> What is the subject of the videoconference?<br>'{TITOLO}' - {DESCRIZIONE}. If you are interested to participate to the event, remember to complete the registration before {ORA_CHIUSURA_ISCRIZIONI} of {DATA_CHIUSURA_ISCRIZIONI} if you want to be among the authorized participants.<br><br>All informations about how you can participate will be published in the dedicated area of the event <a href='{URL_LANDING}'>here</a>.<br><br> See you soon!</p>",
            'webmeeting_webex_save_date' => "<p>Dear {NOME} {COGNOME}, <br> we inform you that the videoconference session is made with the technology Cisco WebEx.<br><br> What is the subject of the videoconference?<br>'{TITOLO}' - {DESCRIZIONE}.<br><br>All informations about how you can participate will be published in the dedicated area of the event <a href='{URL_LANDING}'>here</a>.<br><br> See you soon!</p>",
            'webmeeting_webex_confirm_reg' =>"<p>Dear {NOME} {COGNOME}, <br> we remind you that the videoconference session {TITOLO} will start at {ORA_INIZIO} in date {DATA_INIZIO}.<br><br>All informations about how you can participate will be published in the dedicated area of the event <a href='{URL_LANDING}'>here</a>.<br><br> See you soon!</p>",

        ];
    }


    /**
     *
     */
    public function saveDefaultTextEng(){
        \Yii::$app->language = 'en-GB';
        $defaults = self::getDefaultTextEng();

        if(!empty($defaults['save_the_date'])){
            $this->save_the_date = $defaults['save_the_date'];
        }
        if(!empty($defaults['registration_email'])){
            $this->registration_email = $defaults['registration_email'];
        }

        if(!empty($defaults['info_waiting_list'])){
            $this->info_waiting_list = $defaults['info_waiting_list'];
        }

        if(!empty($defaults['confirm_registration'])){
            $this->confirm_registration = $defaults['confirm_registration'];
        }

        if(!empty($defaults['send_tickets'])){
            $this->send_tickets = $defaults['send_tickets'];
        }

        if(!empty($defaults['webmeeting_webex'])){
            $this->webmeeting_webex = $defaults['webmeeting_webex'];
        }
        if(!empty($defaults['webmeeting_webex_save_date'])){
            $this->webmeeting_webex_save_date = $defaults['webmeeting_webex_save_date'];
        }

        if(!empty($defaults['webmeeting_webex_confirm_reg'])){
            $this->webmeeting_webex_confirm_reg = $defaults['webmeeting_webex_confirm_reg'];
        }
        $this->save(false);
        \Yii::$app->language = 'it-IT';
    }

    /**
     * @param $insert
     * @param $changedAttributes
     */
    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);
        EventChangeLog::saveChangeLog($this->event_id, $this, 'Aggiornamento email evento');
    }

    /**
     * @return bool
     */
    public function canShowTranslationInLine()
    {
        if ($this->event->multilanguage) {
            return true;
        }
        return false;
    }
}
