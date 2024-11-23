<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 29/04/2020
 * Time: 11:52
 */

namespace open20\amos\events\commands;


use open20\amos\community\models\CommunityUserMm;
use open20\amos\core\applications\ConsoleApplication;
use open20\amos\core\models\ModelsClassname;
use open20\amos\core\user\User;
use open20\amos\events\AmosEvents;
use open20\amos\events\exceptions\MailupComunicationException;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventEmailTemplates;
use open20\amos\events\models\EventInternalList;
use open20\amos\events\models\EventInvitationSent;
use open20\amos\events\models\EventPushNotification;
use open20\amos\events\models\EventPushNotificationSent;
use open20\amos\events\models\search\UserEventSearch;
use open20\amos\events\utility\EventMailUtility;
use open20\amos\events\utility\EventsUtility;
use open20\amos\notificationmanager\models\NotificationConf;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Console;
use yii\helpers\Url;

class InvitationSenderController extends Controller
{
    const LIMIT_USER_TO_IMPORT = 15000;
    public $mailService;
    public $mailupListId = 2;
    public $originalMessageId = 2;
    public $dynamics_fields = [
        1 => 'nome',
        2 => 'cognome'
    ];
    public $maxError = 5;
    public $pidFile;

    /**
     *
     */
    public function init()
    {
        parent::init();

        $moduleEvents = \Yii::$app->getModule('events');
        if ($moduleEvents) {
            $this->mailupListId = $moduleEvents->mailup_configurations['mailup_list_id'];
            $this->originalMessageId = $moduleEvents->mailup_configurations['original_message_id'];
            $this->dynamics_fields = $moduleEvents->mailup_configurations['dynamics_fields'];
        } else {
            throw new InvalidConfigException('the module events must be enabled in common');
        }

        $newsletterModule = \Yii::$app->getModule('newsletter');
        if ($newsletterModule) {
            $mailServiceClassname = $newsletterModule->mail_service_driver;
            $this->mailService = new $mailServiceClassname();
        } else {
            throw new InvalidConfigException('the module newsletter must be enabled in common');
        }
        \Yii::$app->params['disableAfterFindPurify'] = true;

    }

    /**
     * @throws InvalidConfigException
     * @throws MailupComunicationException
     */
    public function actionImportAndSend()
    {
        Console::stdout('##################################################' . PHP_EOL);
        Console::stdout('##### START CRON import-user-and-send-emails #####' . PHP_EOL);
        Console::stdout('##### ' . date('d-m-Y h:i:s') . '                    #####' . PHP_EOL);
        Console::stdout('##################################################' . PHP_EOL);


//        $isRunning = $this->checkAlreadyRun();
//        if ($isRunning) {
//            Console::stdout('---- Already running' . PHP_EOL);
//            return true;
//        }
        $internalLists = EventInternalList::find()
            ->andWhere(['not in', 'status', [EventInternalList::STATUS_SENT, EventInternalList::STATUS_NO_USER_TO_IMPORT]])
            ->andWhere(['IS NOT', 'status', null])
            ->andWhere(['OR',
                ['event_internal_list.type' => null],
                ['!= ', 'event_internal_list.type', UserEventSearch::SEARCH_TYPE_COMMUNICATION]])
            ->andWhere(['<', 'mailup_errors', $this->maxError])
            ->all();

//        $q = EventInternalList::find()
//            ->andWhere(['!=', 'status', EventInternalList::STATUS_SENT])
//            ->andWhere(['IS NOT', 'status', null])->createCommand()->rawSql;
        Console::stdout('-----------' . PHP_EOL);


        /** @var  $internalList EventInternalList */
        foreach ($internalLists as $internalList) {
            try {

                if ($internalList->canSend()) {
                    Console::stdout('----------- START LIST ' . $internalList->id . ' - ' . $internalList->name . '----------- ' . PHP_EOL);
                    // --- CREATE MALUP GROUP AND  GET USER AND IMPORT TO GROUP
                    $completed = false;
                    if ($internalList->status == EventInternalList::STATUS_USERS_TO_IMPORT || $internalList->status == EventInternalList::STATUS_IMPORTING_USERS) {
                        $this->createMailGroup($internalList);
                        $completed = $this->getUserAndImport($internalList);
                    }

                    // ---- SET LIST AS IMPORT COMPLETED
                    if ($completed) {
                        console::stdout('----------- GET IMPORT ' . $internalList->mailup_import_id . '   ' . $internalList->status . PHP_EOL);
                        $importDecoded = $this->mailService->getImport($internalList->mailup_import_id);
                        console::stdout(json_encode($importDecoded) . PHP_EOL);
                        $this->checkMailupResponse($importDecoded, 'Errore nel recupero dell importazione di mailup');

                        if ($internalList->mailup_import_id) {
                            if ($importDecoded->Completed == true && $internalList->status == EventInternalList::STATUS_IMPORTING_USERS) {
                                Console::stdout('--- USERS IMPORTED' . PHP_EOL);
                                $internalList->status = EventInternalList::STATUS_IMPORTED;
                                $internalList->save(false);
                            }
                        }
                    }

                    // ---- SEND INVITATION
                    if ($internalList->status == EventInternalList::STATUS_IMPORTED) {
                        $this->sendInvitation($internalList);
                    }

                    // ---- SET SENDING TO COMPLETED
                    if ($internalList->status == EventInternalList::STATUS_SENDING) {
                        $this->sendingCompleted($internalList);
                    }
                }

            } catch (MailupComunicationException $me) {
                $this->addErrorToInvitation($internalList);
                $this->logError($me, $internalList);
            } catch (\Error $e) {
                $this->addErrorToInvitation($internalList);
                $this->logError($e, $internalList);
            } catch (\yii\db\Exception $e) {
                $this->logError($e, $internalList);
            } catch (\yii\base\ErrorException $e) {
                $this->logError($e, $internalList);
            }
        }

//        if (file_exists($this->pidFile)) {
//            unlink($this->pidFile);
//        }

        return true;
    }


    /**
     * @param $internalList EventInternalList
     * @throws MailupComunicationException
     */
    public function sendInvitation($internalList)
    {

        //----- GET TEMPLATE TO SEND
        $event = $internalList->event;
        $eventTemplates = $event->eventEmailTemplates;
        $currentTemplate = $internalList->template;

        $attrSubject = $currentTemplate . '_subject';
        $template_attr = $currentTemplate;
        $text = $eventTemplates->$template_attr;
        $footerType = $eventTemplates->getFooterText()[$template_attr];
        Console::stdout('--- template ' . $template_attr . PHP_EOL);

        if ($template_attr == 'registration_email' || $template_attr == 'webmeeting_webex') {
//            $urlYes = \Yii::$app->params['platform']['backendUrl']. Url::toRoute(['/events/event/event-signup', 'eid' => $event->id, 'pName' => "[nome]", 'pSurname' => "[cognome]", 'pEmail' => "[email]"]);
            $urlYes = EventsUtility::getUrlLanding($event) . '?pName=[nome]&pSurname=[cognome]&pEmail=[email]&listId=' . $internalList->id;
            $urlYes = 'http://[track]/' . $urlYes;
//            $urlYes = str_replace('?', ':rQS$:', $urlYes);
//            $urlYes = str_replace('&', ':rQS*:', $urlYes);
            $text .= "<br><a href=\"$urlYes\">Si, parteciperò</a>";
        }

        $logo = $event->eventLogo;
        $logoEmail = $event->eventLogoEmail;
        $urlImage = \Yii::$app->params['platform']['backendUrl'] . "/img/img_default.jpg";
        if($logoEmail) {
            $urlImage = \Yii::$app->params['platform']['backendUrl'] . $logoEmail->getWebUrl();
        }else if ($logo) {
            $urlImage = \Yii::$app->params['platform']['backendUrl'] . $logo->getWebUrl();
        }

        $subject = $eventTemplates->$attrSubject;
        $subjectWithParams = EventMailUtility::parseEmailWithParams($event, null, $subject, false);
        $textWithParms = EventMailUtility::parseEmailWithParams($event, null, $text);

        if ($event->multilanguage) {
            Console::stdout('---  test eng' . PHP_EOL);
            \Yii::$app->language = 'en-GB';
            $eventTemplatesEng = EventEmailTemplates::findOne($event->eventEmailTemplates->id);
            $eventEng = Event::findOne($event->id);
            $textEng = $eventTemplatesEng->$template_attr;
            if ($template_attr == 'registration_email' || $template_attr == 'webmeeting_webex') {
                $urlYesEng = EventsUtility::getUrlLanding($event) . '?pName=[nome]&pSurname=[cognome]&pEmail=[email]&listId=' . $internalList->id;
                $urlYesEng = 'http://[track]/' . $urlYesEng;
                $textEng .= "<br><a href=\"$urlYesEng\">Yes, I will attend</a>";
            }
            $textWithParamsEng = EventMailUtility::parseEmailWithParams($eventEng, null, $textEng);
            $textWithParms .= "<hr><br>" . $textWithParamsEng;
            \Yii::$app->language = 'it-IT';
        }


        $newsletterModule = \Yii::$app->getModule('newsletter');
        if ($newsletterModule) {
            // GET MAILUP TEMPLATE AND SUBTITUTION OF [CONTENT] WITH PLATFORM PERSONALIZED EMAIL TEXT
            $mailServiceClassname = $newsletterModule->mail_service_driver;
            $mailService = new $mailServiceClassname();
            $emailDecoded = $mailService->getEmail($this->mailupListId, $this->originalMessageId);
            $this->checkMailupResponse($emailDecoded, 'Errore nel recupero del template del messaggio su mailup');

            $htmlWithParams = EventMailUtility::parseDynamicContentMailup($urlImage, $textWithParms, $emailDecoded, $footerType);

            // COPY THE MAILUP MESSAGE
            Console::stdout('--- COPY MESSAGE ' . $this->originalMessageId . PHP_EOL);
            $decodedCopiedMessage = $mailService->copyMessage($this->mailupListId, $this->originalMessageId, [
                'Subject' => $subjectWithParams,
                'Notes' => "I_ID: " . $internalList->id . ' E_ID: ' . $event->id
            ]);
            $this->checkMailupResponse($decodedCopiedMessage, 'Errore nella copia del messaggio/template di mailup');


            // UPDATE THE MESSAGE COPIED WITH THE MODIFIED TEMPLATE
            $idList = $decodedCopiedMessage->idList;
            $idMessage = $decodedCopiedMessage->idMessage;
            $decoded = $mailService->getEmail($idList, $idMessage);
            $this->checkMailupResponse($decodedCopiedMessage, 'Errore nel recupero del messaggio copiato');


            $body = [
                'Subject' => $decoded->Subject,
                'idList' => $decoded->idList,
                'Content' => $htmlWithParams,
                'Tags' => $decoded->Tags,
                'TrackingInfo' => $decoded->TrackingInfo,
                'Embed' => $decoded->Embed,
                'IsConfirmation' => $decoded->IsConfirmation,
                'UseDynamicField' => $decoded->UseDynamicField,
                'Structure' => $decoded->Structure
            ];

            Console::stdout('--- UPDATE COPIED MESSAGE ' . $idMessage . PHP_EOL);
            $decodedReponse = $mailService->updateEmail($idList, $idMessage, $body);
            $this->checkMailupResponse($decodedCopiedMessage, 'Errore nell aggiornamento del messaggio copiato');

            Console::stdout('--- ENABLE DYNAMIC FIELDS ' . $idMessage . PHP_EOL);
            $mailService->enableDisableDynamicFieldsEmail($idList, $idMessage, 'true');

            // SEND MESSAGE TO THE GROUP CRATED FOR THE INVITATION
            Console::stdout('--- SEND EMAIL ' . $internalList->template . ' TO GROUP:  ' . $internalList->mailup_group_id . PHP_EOL);
            $sent = $mailService->sendEmailToGroup($internalList->mailup_group_id, $idMessage);
            $this->checkMailupResponse($decodedCopiedMessage, "Errore nell'invio dell'email al gruppo");

            $internalList->mailup_message_id = $idMessage;
            $internalList->mailup_list_id = $idList;
            if (!empty($sent->Id)) {
                $internalList->mailup_sending_id = $sent->Id;
                $internalList->status = EventInternalList::STATUS_SENDING;
            } else {
                // se non trova nessun recipient ( utente a cui inviare)
                if (!empty($sent->ErrorCode) && $sent->ErrorCode == 400) {
                    $internalList->status = EventInternalList::STATUS_NO_USER_TO_IMPORT;
                } else {
                    $internalList->status = EventInternalList::STATUS_ERROR;
                    $internalList->mailup_errors += 1;
                }
            }
            $internalList->save(false);

        }
    }

    /**
     * @param $internalList
     * @throws MailupComunicationException
     */
    public function createMailGroup($internalList)
    {

        //-- CREATE THE MAILUP GROUP IF NOT EXISTS
        if (empty($internalList->mailup_group_id)) {
            $event = $internalList->event;
            $parsedTitle = preg_replace("/[^A-Za-z0-9 ]/","",$event->title);
            Console::stdout('E' . $event->id . '_I' . $internalList->id . '_' . rand(1, 9999) . '_' . substr($parsedTitle, 0, 20));
            $data = [
                'Name' => 'E' . $event->id . '_I' . $internalList->id . '_' . rand(1, 9999) . '_' . substr($parsedTitle, 0, 20),
                'Notes' => 'Event: ' . $event->id . ' Id Internal list: ' . $internalList->id

            ];
            $groupDecoded = $this->mailService->createGroup($this->mailupListId, $data);
            $this->checkMailupResponse($groupDecoded, 'Errore nella creazione del gruppo di mailup');


            if ($groupDecoded) {
                if (!empty($groupDecoded->ErrorCode)) {
                    Console::stdout(json_encode($groupDecoded) . PHP_EOL);
                } else {
                    Console::stdout('--- CREATE GROUP ' . $groupDecoded->idGroup . PHP_EOL);
                    $internalList->mailup_group_id = $groupDecoded->idGroup;
                    $internalList->mailup_list_id = $this->mailupListId;
                    $internalList->save(false);
                }
            }
        }
    }

    /**
     * @param $internalList EventInternalList
     * @return bool
     * @throws InvalidConfigException
     * @throws MailupComunicationException
     * @throws \yii\db\Exception
     */
    public function getUserAndImport($internalList)
    {

        if ($internalList->mailup_group_id) {
            //--- GET USERS TO IMPORT TO THE MAILUP GROUP ---
            $query = $internalList->query_sql;
            $result = \Yii::$app->db->createCommand($query)->queryAll();
            // calculate the tot
            $internalList->n_user_found = count($result);
            $internalList->save(false);

            // ------ GET USER  TO IMPORT SEGMENTED
            // Prendo l'active query relativo alla ricerca salvata
            $queryComplete = unserialize(urldecode($internalList->active_query_complete));
            // recupero gli id degli utenti a cui è stata già inviata l'email
            $queryUserNotToImport = EventInvitationSent::find()
                ->select('user_id')
                ->andWhere(['event_internal_list_id' => $internalList->id]);

            // recupero gli utenti con un limit (x)
            /** @var $queryComplete ActiveQuery */
            $result = $queryComplete
                ->andWhere(['NOT IN', 'user.id', $queryUserNotToImport])
                ->limit(self::LIMIT_USER_TO_IMPORT)
                ->createCommand()
                ->queryAll();
            $currentFound = count($result);

            $users = [];
            $i = 0;


            if ($currentFound == 0) {
                Console::stdout('--- GET USER AND IMPORT COMPLETED - ' . $internalList->mailup_import_id . PHP_EOL);
                $internalList->status = EventInternalList::STATUS_IMPORTING_USERS;
                $internalList->save(false);
                return true;
            }
            if ($currentFound > 0) {
                if (!empty($internalList->mailup_import_id)) {
                    $importDecoded = $this->mailService->getImport($internalList->mailup_import_id);
                    $this->checkMailupResponse($importDecoded, 'Errore nel recupero dei dati dell importazione');

                    // se l'importazione preedente è completa azzero l'id di importazione econtinuo con gli altri utenti
                    if ($importDecoded->Completed == true) {
                        Console::stdout('--- PARTIAL IMPORT COMPLETED - ' . $internalList->mailup_import_id . PHP_EOL);
                        $internalList->mailup_import_id = null;
                        $internalList->save(false);
                    } else {
                        //esco dalla funzione ed attendo che l'importazione sia completata
                        Console::stdout('--- PARTIAL IMPORT NOT COMPLETED - ' . $internalList->mailup_import_id . PHP_EOL);
                        return false;
                    }
                }

                Console::stdout('--- SAVE INVITATION SENT' . PHP_EOL);
                foreach ($result as $row) {
                    $fields = [];
//                    Console::stdout('---  ' . $row['user_id'] . ' - ' . $row['email'] . PHP_EOL);
                    // SET FIELDS TO SEND TO MAILUP
                    foreach ($this->dynamics_fields as $id => $field) {
                        if (isset($row[$field])) {
                            $fields[] = ['Id' => $id, 'Value' => $row[$field]];
                        }
                    }
                    $users [] = [
                        'Email' => $row['email'],
                        'Fields' => $fields,
                        'Name' => ''
                    ];

                    //--- SAVE THE INVITATION WHICH YOU ARE SENDING --------
                    if ($internalList->template) {
                        $i++;
                        $intivationSent = new EventInvitationSent();
                        $intivationSent->event_internal_list_id = $internalList->id;
                        $intivationSent->user_id = $row['user_id'];
                        $intivationSent->email = $row['email'];
                        $intivationSent->send_at = date('Y-m-d H:i:s');
                        $intivationSent->template = $internalList->template;
                        $intivationSent->save();

                        if ($intivationSent) {
                            \open20\amos\core\models\UserActivityLog::registerLog(AmosEvents::t('amosevents', 'Invio email di invito'), $intivationSent, Event::LOG_TYPE_INVITATION_SENT, null, $intivationSent->user_id);
                        }
                    }
                }

                // --- IMPORT USERS TO THE GROUP AND UPDATE DATAS
                Console::stdout('--- IMPORT ' . $internalList->n_user_found . ' USERS to group ' . $internalList->mailup_group_id . PHP_EOL);
                $importId = $this->mailService->importRecipientsToGroups($users, $internalList->mailup_group_id);
                $this->checkMailupResponse($importId, 'Impossibile caricare i destinatari nel gruppo');

                $internalList->mailup_import_id = $importId;
                $internalList->status = EventInternalList::STATUS_IMPORTING_USERS;
                $internalList->save(false);
                return false;
            }
        } else {
            Console::stdout('--- NO USER TO IMPORT ' . PHP_EOL);
            $internalList->status = EventInternalList::STATUS_NO_USER_TO_IMPORT;
            $internalList->save(false);
            return true;
        }
        return true;
    }

    /**
     * @param $internalList
     */
    public function sendingCompleted($internalList)
    {
        $isComplete = true;
        $reportSendingWiting = $this->mailService->getWaitingSending();
        if (!empty($reportSendingWiting) && !empty($reportSendingWiting->Items)) {
            foreach ($reportSendingWiting->Items as $item) {
                if (!empty($item->Id)) {
                    $isComplete = false;
                }

            }
        }
        $reportSendingOngoing = $this->mailService->getOngoingSending();
        if (!empty($reportSendingOngoing) && !empty($reportSendingOngoing->Items)) {
            foreach ($reportSendingOngoing->Items as $item) {
                if (!empty($item->Id)) {
                    $isComplete = false;
                }

            }
        }
        if ($isComplete) {
            Console::stdout('--- SENDING COMPLETE' . PHP_EOL);
            $internalList->status = EventInternalList::STATUS_SENT;
            $history = $this->mailService->getEmailSendHistory($internalList->mailup_list_id, $internalList->mailup_message_id, []);
            $n = 0;

            if (!empty($history->Items)) {
                $elem = $history->Items[0];
                $n = $elem->Recipients;
            }
            $internalList->n_sent = $n;
            $internalList->sent_at = date('Y-m-d H:i:s');
            $internalList->save(false);
        }
    }


    /**
     * @throws InvalidConfigException
     */
    public function actionSendPushNotification()
    {
        $isConsole = false;
        if (\Yii::$app instanceof ConsoleApplication) {
            $isConsole = true;
        }
        if ($isConsole) {
            Console::stdout('##################################################' . PHP_EOL);
            Console::stdout('##### START CRON send-push-notification      #####' . PHP_EOL);
            Console::stdout('##### ' . date('d-m-Y h:i:s') . '                    #####' . PHP_EOL);
            Console::stdout('##################################################' . PHP_EOL);
        }


        $pushes = EventPushNotification::find()->andWhere(['status' => EventPushNotification::STATUS_DRAFT])->all();
        /** @var  $push EventPushNotification */
        foreach ($pushes as $push) {
            try {
                $i = 0;
                $event = Event::findOne($push->event_id);
                if ($isConsole) {
                    Console::stdout('---- START PUSH ' . $push->id . ' event_id:' . $event->id . PHP_EOL);
                    Console::stdout('---- TYPE ' . $push->type . PHP_EOL);
                }

                $class = $push->content_class;
                $id = $push->content_id;
                $title = '';
                $text = '';
                $object = $class::findOne($id);
                $modelClassname = ModelsClassname::find()->andWhere(['classname' => $class])->one();

                if ($object) {
                    // INVITATION / SAVE THE DATE
                    $typesOfNotification = [EventPushNotification::TYPE_SAVE_THE_DATE, EventPushNotification::TYPE_INVITE_REGISTER];
                    if (in_array($push->type, $typesOfNotification)) {
                        if ($push->type == EventPushNotification::TYPE_SAVE_THE_DATE) {
                            $title = AmosEvents::t('amosevents', $push->getTitlePushNotification());
                            $text = AmosEvents::t('amosevents', $push->getTextPushNotification(), [
                                'title' => $event->title
                            ]);
                        } else if ($push->type == EventPushNotification::TYPE_INVITE_REGISTER) {
                            $title = AmosEvents::t('amosevents', $push->getTitlePushNotification());
                            $text = AmosEvents::t('amosevents', $push->getTextPushNotification(), [
                                'title' => $event->title
                            ]);
                        }

                        $activeQuery = unserialize(urldecode($object->active_query));
                        $activeQuery->select('user.id');
//                pr($query->createCommand()->rawSql);

                        // PUBLICATION CONTENTS (NEWS / DISCUSSIONI / DOCUMENTI /SONDAGGI )
                    } else if ($push->type == EventPushNotification::TYPE_NEW_CONTENT) {
                        $community = $event->community;
                        if ($community) {
                            $grammar = $object->getGrammar();
                            $title = AmosEvents::t('amosevents', "Pubblicazione di")
                                . ' '
                                . $grammar->getIndefiniteArticle()
                                . ' '
                                . $grammar->getModelSingularLabel();

                            $text = AmosEvents::t('amosevents', "Dai un'occhiata a {title}", [
                                'title' => $object->getTitle()
                            ]);
                            $activeQuery = CommunityUserMm::find()
                                ->select('community_user_mm.user_id')
                                ->andWhere(['community_id' => $community->id])
                                ->andWhere(['status' => CommunityUserMm::STATUS_ACTIVE]);
                        }

                        // COMMUNICATIONS - EVENT DATA/PLACE CHANGE
                    } else if ($push->type == EventPushNotification::TYPE_EVENT_CHANGED) {
                        $title = AmosEvents::t('amosevents', $push->getTitlePushNotification());
                        $text = AmosEvents::t('amosevents', $push->getTextPushNotification(), [
                            'TITOLO' => $event->title,
                            'DATA_INIZIO' => date('d/m/Y', strtotime($event->begin_date_hour)),
                            'DATA_FINE' => date('d/m/Y', strtotime($event->end_date_hour)),
                            'ORA_INIZIO' => date('H:i', strtotime($event->begin_date_hour)),
                            'ORA_FINE' => date('H:i', strtotime($event->end_date_hour)),
                            'LOCATION' => $event->eventLocation->name,
                            'INDIRIZZO' => $event->eventLocationEntrance->name,
                        ]);
                        $activeQuery = $object->searchUsersQuery($object->event_id)
                            ->select('user_profile.user_id');
                    }

                    // QUERY GET USER TO NOTIFY
                    $query = User::find()
                        ->distinct()
                        ->innerJoin('access_tokens', 'user.id = access_tokens.user_id')
                        ->andWhere(['!=', 'fcm_token', ''])
                        ->andWhere(['!=', 'fcm_token', 'webcms'])
                        ->andWhere(['OR', ['!=', 'device_os', 'cms'], ['device_os' => null]])
                        ->andWhere(['access_tokens.user_id' => $activeQuery]);

//                $query = \open20\amos\mobile\bridge\modules\v1\models\base\AccessTokens::find()
//                    ->innerJoin('user', 'user.id = access_tokens.user_id')
//                    ->andWhere(['!=', 'fcm_token', ''])
//                    ->andWhere(['OR', ['!=', 'device_os', 'cms'], ['device_os' => null]])
//                    ->andWhere(['access_tokens.user_id' => $activeQuery]);

                    // CHECK USER NOTIFY CONFIGURATIONS
                    if ($modelClassname) {
                        $userToNotNotifyQuery = NotificationConf::find()
                            ->distinct()
                            ->select('notificationconf.user_id')
                            ->innerJoinWith('notificationConfContents')
                            ->andWhere(['OR',
                                ['push_notification' => 0],
                                ['notifications_enabled' => 0],
                            ])
                            ->andWhere(['models_classname_id' => $modelClassname->id]);
                        $query->andWhere(['NOT IN', 'user.id', $userToNotNotifyQuery]);
                    }
                    //                pr($query->createCommand()->rawSql);
                    $result = $query->asArray()->all();

                    foreach ($result as $user) {
                        $ok = \open20\amos\mobile\bridge\utility\NotificationPushUtility::sendNotification($user['id'], $title, $text, 'event', $event->id);
                        if ($ok) {
                            // Console::stdout('token '. $token['user_id']. PHP_EOL);

                            $i++;
                            $sent = new EventPushNotificationSent();
                            $sent->user_id = $user['id'];
                            $sent->event_push_notification_id = $push->id;
                            $sent->sent_at = date('Y-m-d H:i:s');
                            $sent->status = 'sent';
                            $ok = $sent->save(false);

                        }
                    }
                    $push->status = EventPushNotification::STATUS_SENT;
                    $push->save(false);
                    if ($isConsole) {
                        Console::stdout('--- n. SENT  ' . $i . PHP_EOL);
                    }
                }

            } catch (\yii\console\Exception $e) {
                Console::stdout('--- ERR ' . $e->getMessage() . PHP_EOL);
                Console::stdout('--- ERR ' . $e->getFile() . ' - ' . $e->getLine() . PHP_EOL);
                Console::stdout('--- ERR ' . $e->getTraceAsString() . PHP_EOL);
            }
        }
    }

    /**
     * @param $response
     * @param $message
     * @throws MailupComunicationException
     */
    private function checkMailupResponse($response, $message)
    {
        if (isset($response->ErrorCode)) {
            $exception = new MailupComunicationException($message . ' - ' . $response->ErrorDescription, $response->ErrorCode);
            $exception->setObjError($response);
            throw $exception;
        }
    }

    /**
     * @param EventInternalList $model
     */
    private function addErrorToInvitation($model)
    {
        // incremento il numero di errori sulla campagna
        $number = intval(is_null($model->mailup_errors) ? 0 : (int)$model->mailup_errors);
        $model->mailup_errors = ($number + 1);

        if ($model->mailup_errors >= $this->maxError) {
            $model->status = EventInternalList::STATUS_ERROR;
        }
        $model->save(false);
    }

    /**
     * @param $e Exception
     * @param $model
     */
    private function logError($e, $model)
    {
        Console::stdout('----------- ERROR !!!! ' . $e->getMessage() . '----------- ' . PHP_EOL);
        Console::stdout('in ' . $e->getFile() . ' - line: ' . $e->getLine() . PHP_EOL);
        Console::stdout($e->getTraceAsString() . PHP_EOL);
        Console::stdout('----------- END LIST ' . $model->id . ' - ' . $model->name . '----------- ' . PHP_EOL);

        $myfile = fopen(\Yii::$app->basePath . '/cron_log/' . "error_cron_invitation_sender.txt", "a+") or die("Unable to open file!");
        $txt = '----------- START LIST ' . $model->id . ' - ' . $model->name . '----------- ' . "\n";
        fwrite($myfile, $txt);
        $txt = date('d-m-Y H:i:s') . "\n";
        fwrite($myfile, $txt);
        $txt = '!!!! ERROR !!!! ' . $e->getMessage() . '----------- ' . "\n";
        fwrite($myfile, $txt);
        $txt = 'in ' . $e->getFile() . ' - line: ' . $e->getLine() . "\n";
        fwrite($myfile, $txt);
        $txt = $e->getTraceAsString() . "\n";
        fwrite($myfile, $txt);
        $txt = '----------- END LIST ----------- ' . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
//
//        if (file_exists($this->pidFile)) {
//            unlink($this->pidFile);
//        }

    }

    /**
     * @return bool
     */
    public function checkAlreadyRun()
    {
        $tmpDir = \Yii::$app->runtimePath;
        $pidFile = $tmpDir . '/event_invite_cron.pid';
        $this->pidFile = $pidFile;

        $isRunning = false;

//          Console::stdout(is_writable($pidFile) .' 1- '.PHP_EOL);
//          Console::stdout(is_writable($tmpDir) .' 2- '.PHP_EOL);
//          Console::stdout(file_exists($pidFile) .' 3- '.PHP_EOL);
        if (is_writable($pidFile) || is_writable($tmpDir)) {
            if (file_exists($pidFile)) {
                $pid = (int)trim(file_get_contents($pidFile));
                $isRunning = $this->exitFromDeadlock($pidFile);
            }
        } else {
            Console::stdout('Cannot write user_inviter_cron pid lock file. Exit script' . PHP_EOL);
        }

        if ($isRunning === false) {
            $pid = getmygid();
            file_put_contents($pidFile, $pid);
        }


        return $isRunning;
    }

    /**
     * @param $pidFile
     * @return bool
     */
    public function exitFromDeadlock($pidFile)
    {
        $isRunning = true;
        $timestampLastModify = filemtime($pidFile);
        $dateLastModify = date("d F Y H:i:s", $timestampLastModify);
        $now = date("d F Y H:i:s");
        $timestamp2 = strtotime($now);
        $hour = abs($timestamp2 - $timestampLastModify) / (60 * 60);

        if ($hour > 1) {
            Console::stdout('Deleting .pid file - last modified ' . json_encode($dateLastModify) . PHP_EOL);
            $isRunning = false;
//            unlink($pidFile);
        }
        return $isRunning;

    }

}