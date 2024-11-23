<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 29/04/2020
 * Time: 11:52
 */

namespace open20\amos\events\commands;


use open20\amos\events\AmosEvents;
use open20\amos\events\exceptions\MailupComunicationException;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventCommunication;
use open20\amos\events\models\EventCommunicationSent;
use open20\amos\events\models\EventEmailTemplates;
use open20\amos\events\models\EventInvitationSent;
use open20\amos\events\models\EventLanding;
use open20\amos\events\models\EventPushNotification;
use open20\amos\events\utility\EventMailUtility;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\Console;
use yii\helpers\Url;

class CommunicationSenderController extends Controller
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

    public function actionSendMemoStreaming()
    {
        Console::stdout('##########################################################' . PHP_EOL);
        Console::stdout('##### START CRON send-memo-streaming #####' . PHP_EOL);
        Console::stdout('##### ' . date('d-m-Y h:i:s') . '                    #####' . PHP_EOL);
        Console::stdout('##########################################################' . PHP_EOL);


        $eventLandings = EventLanding::find()
            ->innerJoinWith('event')
            ->andWhere(['is not', 'streaming_url', null])
            ->andWhere(['is', 'event_communication_memo_id', null])
            ->andWhere(['<=', 'event_landing.date_sending_memo', new Expression('NOW()')])
            ->all();

        /** @var  $landing EventLanding */
        foreach ($eventLandings as $landing) {
            Console::stdout('-- START EVENT id:' . $landing->event_id . PHP_EOL);
            try {
                $communication = new EventCommunication();
                $communication->event_id = $landing->event_id;
                $communication->text_email = $landing->text_sending_memo;
                $communication->subject_email = $landing->subject_sending_memo;
                $communication->title = AmosEvents::t('amosevents', "Promemoria streaming");
                $communication->communication_type = EventCommunication::TYPE_REGISTERED;
                $query = $communication->searchUsersQuery($landing->event_id);
                $communication->n_users = $query->count();
                $communication->is_automatic = true;
                $communication->created_by = 1;
                $communication->status = EventCommunication::STATUS_USERS_TO_IMPORT;
                if ($communication->save(false)) {
                    $landing->event_communication_memo_id = $communication->id;
                    $landing->save(false);
                    Console::stdout('- PROCESSING MEMO' . PHP_EOL);
                }

                $event = $landing->event;
                if ($event && $event->multilanguage) {
                    \Yii::$app->language = 'en-GB';
                    $landingEng = EventLanding::findOne($landing->id);
                    $communication->text_email = $landingEng->text_sending_memo;
                    $communication->save(false);
                    \Yii::$app->language = 'it-IT';
                }
                Console::stdout('--- END id:' . $landing->event_id . PHP_EOL);


            } catch (MailupComunicationException $me) {
                $this->addErrorToInvitation($communication);
                $this->logError($me, $communication);
            } catch (\Error $e) {
                $this->addErrorToInvitation($communication);
                $this->logError($e, $communication);
            } catch (\yii\db\Exception $e) {
                $this->logError($e, $communication);
            }

        }
    }
    //----------------------

    /**
     * @throws InvalidConfigException
     * @throws MailupComunicationException
     */
    public function actionImportAndSendCommunications()
    {
        Console::stdout('##########################################################' . PHP_EOL);
        Console::stdout('##### START CRON import-user-and-send-communications #####' . PHP_EOL);
        Console::stdout('##### ' . date('d-m-Y h:i:s') . '                    #####' . PHP_EOL);
        Console::stdout('##########################################################' . PHP_EOL);

        $isRunning = $this->checkAlreadyRun();
        if ($isRunning) {
            Console::stdout('---- Process already running' . PHP_EOL);
            return true;
        }
        $communications = EventCommunication::find()
            ->andWhere(['NOT IN', 'status', [EventCommunication::STATUS_SENT, EventCommunication::STATUS_NO_USER_TO_IMPORT]])
            ->andWhere(['IS NOT', 'status', null])
            ->andWhere(['IS NOT', 'event_id', null])
            ->andWhere(['<', 'mailup_errors', $this->maxError])
            ->all();

        /** @var  $communication EventCommunication */
        foreach ($communications as $communication) {
            try {
                if ($communication->canSend()) {
                    Console::stdout('----------- START LIST ' . $communication->id . ' - ' . $communication->title . '----------- ' . PHP_EOL);
                    $completed = false;
                    // --- CREATE MALUP GROUP AND  GET USER AND IMPORT TO GROUP
                    if ($communication->status == EventCommunication::STATUS_USERS_TO_IMPORT || $communication->status == EventCommunication::STATUS_IMPORTING_USERS) {
                        $this->createMailGroup($communication);
                        $completed = $this->getUserAndImport($communication);
                    }

                    // ---- SET LIST AS IMPORT COMPLETED
                    if ($completed) {
                        Console::stdout('----------- GET IMPORT ' . $communication->mailup_import_id . '   ' . $communication->status . PHP_EOL);
                        $importDecoded = $this->mailService->getImport($communication->mailup_import_id);
                        $this->checkMailupResponse($importDecoded, "Impossibile recuperare l'importazione");

                        if ($communication->mailup_import_id) {
                            if ($importDecoded->Completed == true && $communication->status == EventCommunication::STATUS_IMPORTING_USERS) {
                                Console::stdout('--- USERS IMPORTED' . PHP_EOL);
                                $communication->status = EventCommunication::STATUS_IMPORTED;
                                $communication->save(false);
                            }
                        } else {
                            $communication->status = EventCommunication::STATUS_NO_USER_TO_IMPORT;
                            $communication->save(false);
                        }
                    }

                    // ---- SEND INVITATION
                    if ($communication->status == EventCommunication::STATUS_IMPORTED) {
                        $this->sendCommunication($communication);
                    }

                    // ---- SET SENDING TO COMPLETED
                    if ($communication->status == EventCommunication::STATUS_SENDING) {
                        $this->sendingCompleted($communication);
                    }

                    Console::stdout('----------- END LIST ' . $communication->id . ' - ' . $communication->title . '----------- ' . PHP_EOL);
                }
            } catch (MailupComunicationException $me) {
                $this->addErrorToInvitation($communication);
                $this->logError($me, $communication);
            } catch (\Error $e) {
                $this->addErrorToInvitation($communication);
                $this->logError($e, $communication);
            } catch (\yii\db\Exception $e) {
                $this->logError($e, $communication);
            } catch (\yii\base\Exception $e) {
                $this->logError($e, $communication);
            }
        }

        if (file_exists($this->pidFile)) {
            unlink($this->pidFile);
        }

        return true;
    }


    public function startPushNotificationEventChange($communication)
    {
        Console::stdout('--- START PUSH - id comunication ' . $communication->id . PHP_EOL);

        $pushNotifiation = EventPushNotification::find()
            ->andWhere(['status' => EventPushNotification::STATUS_DONT_SEND])
            ->andWhere(['content_id' => $communication->id])
            ->andWhere(['content_class' => get_class($communication)])->one();
        if ($pushNotifiation) {
            $pushNotifiation->status = EventPushNotification::STATUS_DRAFT;
            $pushNotifiation->save(false);
        }
    }

    /**
     * @param $communication EventCommunication
     * @throws MailupComunicationException
     */
    public function sendCommunication($communication)
    {
        //----- Avvia push notification
        $this->startPushNotificationEventChange($communication);

        //----- GET TEMPLATE TO SEND
        $event = $communication->event;

        $text = $communication->text_email;
        $subject = $communication->subject_email;

        $subjectWithParams = EventMailUtility::parseEmailWithParams($event, null, $subject, false);
        $textWithParms = EventMailUtility::parseEmailWithParams($event, null, $text);

        // get email text in english
        if ($event->multilanguage) {
            \Yii::$app->language = 'en-GB';
            $communicatonEng = EventCommunication::findOne($communication->id);
            $eventEng = Event::findOne($event->id);
            $textEng = $communicatonEng->text_email;
            $textWithParamsEng = EventMailUtility::parseEmailWithParams($eventEng, null, $textEng);
            $textWithParms .= "<hr><br>" . $textWithParamsEng;
            \Yii::$app->language = 'it-IT';
        }

        $logo = $event->eventLogo;
        $logoEmail = $event->eventLogoEmail;
        $urlImage = \Yii::$app->params['platform']['backendUrl'] . "/img/img_default.jpg";
        if($logoEmail) {
            $urlImage = \Yii::$app->params['platform']['backendUrl'] . $logoEmail->getWebUrl();
        }else if ($logo) {
            $urlImage = \Yii::$app->params['platform']['backendUrl'] . $logo->getWebUrl();
        }

        $newsletterModule = \Yii::$app->getModule('newsletter');
        if ($newsletterModule) {
            // GET MAILUP TEMPLATE AND SUBTITUTION OF [CONTENT] WITH PLATFORM PERSONALIZED EMAIL TEXT
            $mailServiceClassname = $newsletterModule->mail_service_driver;
            $mailService = new $mailServiceClassname();
            $emailDecoded = $mailService->getEmail($this->mailupListId, $this->originalMessageId);
            $this->checkMailupResponse($emailDecoded, "Impossibile recuperare il template/email");

            $htmlWithParams = EventMailUtility::parseDynamicContentMailup($urlImage, $textWithParms, $emailDecoded, EventEmailTemplates::FOOTER_TYPE_TAG_AND_UNSUBSCRIBE);


            // COPY THE MAILUP MESSAGE
            Console::stdout('--- COPY MESSAGE ' . $this->originalMessageId . PHP_EOL);
            $decodedCopiedMessage = $mailService->copyMessage($this->mailupListId, $this->originalMessageId, [
                'Subject' => $subjectWithParams,
                'Notes' => "C_ID: " . $communication->id . ' E_ID: ' . $event->id
            ]);
            $this->checkMailupResponse($decodedCopiedMessage, "Impossibile copiare il template/email");


            // UPDATE THE MESSAGE COPIED WITH THE MODIFIED TEMPLATE
            $idList = $decodedCopiedMessage->idList;
            $idMessage = $decodedCopiedMessage->idMessage;
            $decoded = $mailService->getEmail($idList, $idMessage);
            $this->checkMailupResponse($decoded, "Impossibile recuperare il template/email");


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
            $this->checkMailupResponse($decodedReponse, "Impossibile aggiornare l'email copiata");

            $mailService->enableDisableDynamicFieldsEmail($idList, $idMessage, 'true');

            // SEND MESSAGE TO THE GROUP CRATED FOR THE COMMUNCATION
            Console::stdout('--- SEND EMAIL ' . $communication->title . ' TO GROUP:  ' . $communication->mailup_group_id . PHP_EOL);
            $sent = $mailService->sendEmailToGroup($communication->mailup_group_id, $idMessage);
            $this->checkMailupResponse($sent, "Impossibile inviare l'emai al gruppo su mailup");

            $communication->mailup_message_id = $idMessage;
            $communication->mailup_list_id = $idList;
            if (!empty($sent->Id)) {
                $communication->mailup_sending_id = $sent->Id;
                $communication->status = EventCommunication::STATUS_SENDING;
            } else {
                // se non trova nessun recipient ( utente a cui inviare)
                if (!empty($sent->ErrorCode) && $sent->ErrorCode == 400) {
                    $communication->status = EventCommunication::STATUS_NO_USER_TO_IMPORT;
                } else {
                    $communication->status = EventCommunication::STATUS_ERROR;
                    $communication->mailup_errors += 1;
                }
            }

            $communication->save(false);

        }
    }

    /**
     * @param $communication EventCommunication
     * @throws MailupComunicationException
     */
    public function createMailGroup($communication)
    {
        //-- CREATE THE MAILUP GROUP IF NOT EXISTS
        if (empty($communication->mailup_group_id)) {
            $event = $communication->event;

            $parsedTitle = preg_replace("/[^A-Za-z0-9 ]/","",$communication->title);
            $data = [
                'Name' => 'E' . $event->id . '_C' . $communication->id . '_' . rand(1, 9999) . '_' . substr($parsedTitle, 0, 20),
                'Notes' => 'Communication Event: ' . $event->id . ' Id Communication: ' . $communication->id
            ];
            $groupDecoded = $this->mailService->createGroup($this->mailupListId, $data);
            $this->checkMailupResponse($groupDecoded, "Impossibile creare il gruppo su mailup.");

            if (!empty($groupDecoded->ErrorCode)) {
                Console::stdout(json_encode($groupDecoded) . PHP_EOL);
            } else if (!empty($groupDecoded->idGroup) && $groupDecoded) {
                Console::stdout('--- CREATE GROUP ' . $groupDecoded->idGroup . PHP_EOL);
                $communication->mailup_group_id = $groupDecoded->idGroup;
                $communication->mailup_list_id = $this->mailupListId;
                $communication->save(false);
            }
        }
    }

    /**
     * @param $communication EventCommunication
     * @return bool
     * @throws InvalidConfigException
     * @throws MailupComunicationException
     * @throws \yii\db\Exception
     */
    public function getUserAndImport($communication)
    {
        if ($communication->mailup_group_id) {
            //--- GET USERS TO IMPORT TO THE MAILUP GROUP ---
            /** @var  $query ActiveQuery */

            /** NOMINAL COMMUNICATION */
//            Console::stdout(json_encode($communication->eventInternalList->name). PHP_EOL);
            if ($communication->communication_type == EventCommunication::TYPE_NOMINAL && $communication->eventInternalList) {
                $queryComplete = unserialize(urldecode($communication->eventInternalList->active_query_complete));
            } else {
                //   OTHER COMMUNICATIONS
                $query = $communication->searchUsersQuery($communication->event_id);
                $query2 = $query
                    ->select(new Expression("user.*, user_profile.*, istat_comuni.nome as 'comune', istat_province.nome as 'provincia', user_profile_age_group.age_group as eta"))
                    ->leftJoin('user_profile_age_group', 'user_profile_age_group.id = user_profile.user_profile_age_group_id')
                    ->leftJoin('istat_comuni', 'istat_comuni.id = user_profile.nascita_comuni_id')
                    ->leftJoin('istat_province', 'istat_province.id = user_profile.nascita_province_id');
                $result = $query2->asArray()->all();
                $communication->n_users = count($result);
                $communication->save(false);

                $queryComplete = clone $query2;
            }

            //se è una comunicazione di cambio data/luogo evento agli utenti registrati controllo il flag nel profilo
            if ($communication->is_changed_info == 1) {
                $queryComplete->andWhere(['user_profile.enable_email_change_event' => 1]);
            }

            // SEGMENTED QUERY
            $queryUserNotToImport = EventCommunicationSent::find()
                ->select('user_id')
                ->andWhere(['event_communication_id' => $communication->id]);

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
                Console::stdout('--- GET USER AND IMPORT COMPLETED - ' . $communication->mailup_import_id . PHP_EOL);
                $communication->status = EventCommunication::STATUS_IMPORTING_USERS;
                $communication->save(false);
                return true;
            }
            if ($currentFound) {
                if (!empty($communication->mailup_import_id)) {
                    $importDecoded = $this->mailService->getImport($communication->mailup_import_id);
                    $this->checkMailupResponse($importDecoded, "Impossibile recuperare l'importazione");

                    // se l'importazione preCedente è completa azzero l'id di importazione e continuo con gli altri utenti
                    if ($importDecoded->Completed == true) {
                        Console::stdout('--- PARTIAL IMPORT COMPLETED - ' . $communication->mailup_import_id . PHP_EOL);
                        $communication->mailup_import_id = null;
                        $communication->save(false);
                    } else {
                        //esco dalla funzione ed attendo che l'importazione sia completata
                        Console::stdout('--- PARTIAL IMPORT NOT COMPLETED - ' . $communication->mailup_import_id . PHP_EOL);
                        return false;
                    }
                }
                Console::stdout('--- SAVE COMMUNCATIONS  SENT' . PHP_EOL);
                foreach ($result as $row) {
                    $fields = [];
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
                    $i++;
                    $communicationSent = new EventCommunicationSent();
                    $communicationSent->event_communication_id = $communication->id;
                    $communicationSent->user_id = $row['user_id'];
                    $communicationSent->email = $row['email'];
                    $communicationSent->sent_at = date('Y-m-d H:i:s');
                    $communicationSent->save();
                    if ($communicationSent) {
                        \open20\amos\core\models\UserActivityLog::registerLog(AmosEvents::t('amosevents', 'Invio comunicazione'), $communicationSent, Event::LOG_TYPE_COMMUNICATION_SENT, null, $communicationSent->user_id);
                    }
                }

                // --- IMPORT USERS TO THE GROUP AND UPDATE DATAS
                Console::stdout('--- IMPORT ' . $communication->n_users . ' USERS to group ' . $communication->mailup_group_id . PHP_EOL);
                $importId = $this->mailService->importRecipientsToGroups($users, $communication->mailup_group_id);
                $this->checkMailupResponse($importId, "Impossibile importare gli utenti sul gruppo.");

                $communication->mailup_import_id = $importId;
                $communication->status = EventCommunication::STATUS_IMPORTING_USERS;
                $communication->save(false);
                return false;
            } else {
                Console::stdout('--- NO USER TO IMPORT ' . PHP_EOL);
                $communication->status = EventCommunication::STATUS_NO_USER_TO_IMPORT;
                $communication->save(false);
                return true;
            }
        }
        return true;
    }

    /**
     * @param $communication
     */
    public function sendingCompleted($communication)
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
            $communication->status = EventCommunication::STATUS_SENT;
            $history = $this->mailService->getEmailSendHistory($communication->mailup_list_id, $communication->mailup_message_id, []);
            $n = 0;
            if (!empty($history->Items)) {
                $elem = $history->Items[0];
                $n = $elem->Recipients;
            }
            $communication->n_sent = $n;
            $communication->sent_at = date('Y-m-d H:i:s');
            $communication->save(false);
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
     * @param EventCommunication $model
     */
    private function addErrorToInvitation($model)
    {
        // incremento il numero di errori sulla campagna
        $number = intval(is_null($model->mailup_errors) ? 0 : (int)$model->mailup_errors);
        $model->mailup_errors = ($number + 1);

        if ($model->mailup_errors >= $this->maxError) {
            $model->status = EventCommunication::STATUS_ERROR;
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
        Console::stdout('----------- END LIST ' . $model->id . ' - ' . $model->title . '----------- ' . PHP_EOL);

        $myfile = fopen(\Yii::$app->basePath . '/cron_log/' . "error_cron_communication_sender.txt", "a+") or die("Unable to open file!");
        $txt = '----------- START COMMUNICATIO LIST ' . $model->id . ' - ' . $model->title . '----------- ' . "\n";
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

        if (file_exists($this->pidFile)) {
            unlink($this->pidFile);
        }

    }

    /**
     * @return bool
     */
    public function checkAlreadyRun()
    {
        $tmpDir = \Yii::$app->runtimePath;
        $pidFile = $tmpDir . '/event_communication_cron.pid';
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
            unlink($pidFile);
        }
        return $isRunning;

    }

    public function actionAutomatedCommunications()
    {
//        $now = new \DateTime('now', new \DateTimeZone(''));
        $events = Event::find()
            ->andWhere(['time_cron_executed' => 0])
            ->andWhere(['<','end_date_hour',new Expression('NOW()')])
//            ->andWhere(['>','id',443])
            ->andWhere(['>','end_date_hour','2022-05-24 00:00:00'])
            ->andWhere(['has_tickets' => 1])
            ->limit(10)
            ->all();

        /** @var  $event Event*/
        foreach ($events as $event){
            try {
                Console::stdout('- CREATE COMUNICATION EVENT: ' . $event->id . PHP_EOL);
                Console::stdout('end-date: ' . $event->end_date_hour . PHP_EOL);
                $comunication = new EventCommunication();
                $comunication->event_id = $event->id;
                $comunication->communication_type = EventCommunication::TYPE_REGISTERED_AND_ATTENDED;
                $comunication->time_schedule_type = EventCommunication::SENDING_TIME_SCHEDULED;
                $comunication->time_schedule_date = date("Y-m-d H:i:s", strtotime('+3 hours'));;
                $comunication->title = \Yii::t('amosevents', 'Ringraziamento partecipazione');
                $comunication->subject_email = EventCommunication::defaultSubjectCommunication(EventCommunication::TYPE_REGISTERED_AND_ATTENDED);
                $comunication->text_email = EventCommunication::defaultTextCommunication(EventCommunication::TYPE_REGISTERED_AND_ATTENDED);
                $comunication->n_users = $comunication->searchUsersQuery($event->id)->count();
                $comunication->status = EventCommunication::STATUS_USERS_TO_IMPORT;

                $comunication2 = clone $comunication;
                $comunication2->title = \Yii::t('amosevents', 'Informativa per mancata partecipazione');
                $comunication2->subject_email = EventCommunication::defaultSubjectCommunication(EventCommunication::TYPE_REGISTERED_AND_NOT_ATTENDED);
                $comunication2->text_email = EventCommunication::defaultTextCommunication(EventCommunication::TYPE_REGISTERED_AND_NOT_ATTENDED);

                $comunication2->communication_type = EventCommunication::TYPE_REGISTERED_AND_NOT_ATTENDED;
                $comunication2->n_users = $comunication2->searchUsersQuery($event->id)->count();

                if ($comunication2->n_users > 0) {
                    $comunication2->save(false);
                }
                if ($comunication->n_users > 0) {
                    $comunication->save(false);
                }

                $event->time_cron_executed = 1;
                $event->detachBehaviorByClassName('open20\amos\notificationmanager\behaviors\NotifyBehavior');
                $event->detachBehavior('notifyBehavior');
                $event->callLyua = false;
                $event->save(false);

            } catch( ErrorException $e){
                Console::stdout('Error '.$e->getName() .' with message '. $e->getMessage() . PHP_EOL);
                Console::stdout( 'in '.$e->getFile().':'.$e->getLine() . PHP_EOL);
                Console::stdout( $e->getTraceAsString().PHP_EOL);

            }
        }

    }
}