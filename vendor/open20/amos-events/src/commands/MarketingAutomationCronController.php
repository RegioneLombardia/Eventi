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

class MarketingAutomationCronController extends Controller
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
    }


    /**
     * @throws InvalidConfigException
     * @throws MailupComunicationException
     */
    public function actionSendMarketingCommunications()
    {
        Console::stdout('##########################################################' . PHP_EOL);
        Console::stdout('##### START CRON marketing-communications #####' . PHP_EOL);
        Console::stdout('##### ' . date('d-m-Y h:i:s') . '                    #####' . PHP_EOL);
        Console::stdout('##########################################################' . PHP_EOL);


        $communications = EventCommunication::find()
            ->andWhere(['NOT IN', 'status', [EventCommunication::STATUS_SENT, EventCommunication::STATUS_NO_USER_TO_IMPORT]])
            ->andWhere(['IS NOT', 'status', null])
            ->andWhere(['event_id' =>  null])
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

        return true;
    }


    /**
     * @param $communication EventCommunication
     * @throws MailupComunicationException
     */
    public function sendCommunication($communication)
    {

        //----- GET TEMPLATE TO SEND
        $text = $communication->text_email;
        $subject = $communication->subject_email;

        $subjectWithParams = EventMailUtility::parseEmailWithParams(null, null, $subject, false);
        $textWithParms = EventMailUtility::parseEmailWithParams(null, null, $text);

        $urlImage = \Yii::$app->params['platform']['backendUrl'] . "/img/img_default.jpg";
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
                'Notes' => "C_ID: " . $communication->id . ' E_ID: '
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
            $parsedTitle = preg_replace("/[^A-Za-z0-9 ]/","",$communication->title);

            $data = [
                'Name' =>'MK_C' . $communication->id . '_' . rand(1, 9999) . '_' . substr($parsedTitle, 0, 20),
                'Notes' => 'Marketing automation: '. ' Id Communication: ' . $communication->id
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
                $query = $communication->searchUsersMarketingAutomationQuery($communication->search_inverval);
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
    }

    /**
     * @return void
     */
    public function actionStoreCookieLog(){
        Console::stdout('##################################################' . PHP_EOL);
        Console::stdout('##### START CRON store-cookie-log #####' . PHP_EOL);
        Console::stdout('##### ' . date('d-m-Y h:i:s') . '              #####' . PHP_EOL);
        Console::stdout('##################################################' . PHP_EOL);

        $sql = "CALL EventsLogCookieManage;";
        $command = \Yii::$app->db->createCommand($sql);
        $command->execute();

        Console::stdout('--------------------- END -------------------------' . PHP_EOL);

        return 0;
    }


}