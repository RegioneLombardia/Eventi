<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 29/04/2020
 * Time: 11:52
 */

namespace amos\userimporter\commands;


use amos\userimporter\exceptions\MailupComunicationException;
use amos\userimporter\models\UserImportTask;
use amos\userimporter\models\UserImportTaskUser;
use amos\userimporter\base\UserImportTaskStatus;
use open20\amos\admin\controllers\UserDropController;
use open20\amos\admin\models\UserProfile;
use open20\amos\admin\utility\UserProfileUtility;
use open20\amos\core\user\User;
use open20\amos\events\AmosEvents;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventInternalList;
use open20\amos\events\models\EventInvitationSent;
use open20\amos\events\models\EventGroupReferentMm;
use open20\amos\events\utility\EventMailUtility;
use open20\amos\events\utility\EventsUtility;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\Console;
use yii\helpers\Json;
use yii\helpers\Url;
use Yii;

class UserInviterController extends Controller
{

    const NAME_POS = 0;
    const SURNAME_POS = 1;
    const EMAIL_POS = 2;
    const CF_POS = 3;

    const LIMIT_USER_TO_IMPORT = 15000;

    const MIN_COLUMNS = 3;

    private $task;
    private $tempPath = '';
    private $storage;
    private $Okspreadsheet;
    private $Errspreadsheet;
    private $Okworksheet;
    private $Errworksheet;
    private $Okrow;
    private $Errrow;
    public $Warning;

    public $pidFile;


    public $tasks;
    public $mailService;
    public $mailupListId = 2;
    public $originalMessageId = 2;
    public $dynamics_fields = [
        1 => 'nome',
        2 => 'cognome'
    ];

    public $maxError = 5;

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


        $this->storage = Yii::$app->getModule('attachments');
        $this->tempPath = Yii::getAlias($this->storage->tempPath);

        $this->Okspreadsheet = new Spreadsheet();
        $this->Errspreadsheet = new Spreadsheet();
        $this->Okworksheet = $this->Okspreadsheet->getActiveSheet();
        $this->Errworksheet = $this->Errspreadsheet->getActiveSheet();

        $this->Okrow = 1;
        $this->Errrow = 1;

    }


    /**
     * @throws InvalidConfigException
     */
    public function actionCleanUsers()
    {
        Console::stdout('##################################################' . PHP_EOL);
        Console::stdout('##### START CRON clean-users-imported        #####' . PHP_EOL);
        Console::stdout('##### ' . date('d-m-Y h:i:s') . '                    #####' . PHP_EOL);
        Console::stdout('##################################################' . PHP_EOL);

        $imports = UserImportTaskUser::find()
            ->andWhere(['<', new Expression('DATE_ADD( created_at, INTERVAL 5 DAY)'), new Expression('NOW()')])
            ->andWhere(['user_id' => null])->all();
        $i = 0;
        foreach ($imports as $import) {
            $import->delete();
            $i++;
        }
        Console::stdout("- $i user removed" . PHP_EOL);

        Console::stdout('----------- END CRON clean-users-imported ----------- ' . PHP_EOL);

    }


    /**
     * @throws InvalidConfigException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \yii\db\Exception
     */
    public function actionImportAndInvite()
    {
        Console::stdout('##################################################' . PHP_EOL);
        Console::stdout('##### START CRON import-user-excels          #####' . PHP_EOL);
        Console::stdout('##### ' . date('d-m-Y h:i:s') . '                    #####' . PHP_EOL);
        Console::stdout('##################################################' . PHP_EOL);

//        $isRunning = $this->checkAlreadyRun();
//        if ($isRunning) {
//            Console::stdout('---- Already running' . PHP_EOL);
//            return true;
//        }

        $tasks = UserImportTask::find()
            ->andWhere(['not in', 'status', [
                UserImportTaskStatus::STATUS_SENT,
                UserImportTaskStatus::END,
                UserImportTaskStatus::STATUS_NO_USER_TO_IMPORT
            ]])
            ->andWhere(['<', 'mailup_errors', $this->maxError])
            ->all();

//        $q = UserImportTask::find()->andWhere(['not in', 'status', [
//            UserImportTaskStatus::STATUS_SENT,
//            UserImportTaskStatus::END,
//        ]]);
//        Console::stdout($q->createCommand()->rawSql . PHP_EOL);


        /** @var  $task UserImportTask */
        foreach ($tasks as $task) {
            try {
                Console::stdout('----------- START TASK ' . $task->id . ' - ' . $task->name . '----------- ' . PHP_EOL);

                // --- IMPORT FROM EXCEL
                if ($task->status === (string)UserImportTaskStatus::PENDING) {
                    $this->importFromExcel($task);
                }

                // --- CREATE MALUP GROUP AND  GET USER AND IMPORT TO GROUP
                $completed = false;
                if ($task->status == UserImportTaskStatus::STATUS_USERS_TO_IMPORT || $task->status == UserImportTaskStatus::STATUS_IMPORTING_USERS) {
                    $this->createMailGroup($task);
                    $completed = $this->getUserAndImport($task);
                }

                // ---- SET LIST AS IMPORT COMPLETED
                if ($completed) {
                    $importDecoded = $this->mailService->getImport($task->mailup_import_id);
                    $this->checkMailupResponse($importDecoded, 'Errore nel recupero dell importazione di mailup');

                    if (!empty($importDecoded->Completed) && $importDecoded->Completed == true && $task->status == UserImportTaskStatus::STATUS_IMPORTING_USERS) {
                        Console::stdout('--- USERS IMPORTED' . PHP_EOL);
                        $task->status = UserImportTaskStatus::STATUS_IMPORTED;
                        $task->save(false);
                    }
                }

                // ---- SEND INVITATION
                if ($task->status == UserImportTaskStatus::STATUS_IMPORTED) {
                    $this->sendInvitation($task);
                }

                // ---- SET SENDING TO COMPLETED
                if ($task->status == UserImportTaskStatus::STATUS_SENDING) {
                    $this->sendingCompleted($task);
                }

                Console::stdout('----------- END TAKS ' . $task->id . ' - ' . $task->name . '----------- ' . PHP_EOL);
            } catch (MailupComunicationException $me) {
                $this->addErrorToInvitation($task);
                $this->logError($me, $task);
            } catch (\Error $e) {
                $this->addErrorToInvitation($task);
                $this->logError($e, $task);
            } catch (\yii\db\Exception $e) {
                $this->logError($e, $task);
            }
        }
//        unlink($this->pidFile);
        return 0;
    }


    /**
     * @param $task UserImportTask
     * @param $task
     * @throws InvalidConfigException
     * @throws MailupComunicationException
     */
    public function sendInvitation($task)
    {

        $appName = \Yii::$app->name;
        $urlRegister = \Yii::$app->params['platform']['backendUrl'] . '/admin/security/register?token_invitation={TOKEN}';

        $moduleEvents = \Yii::$app->getModule('events');
        if ($moduleEvents) {
            if ($moduleEvents->disableSocial) {
                $urlRegister = \Yii::$app->params['platform']['backendUrl'] . '/admin/security/login?token_invitation={TOKEN}';
            }
        }
        //----- GET TEMPLATE TO SEND
        $text = $task->email_text;
        $text .= "<br>" . "Clicca <a href='$urlRegister'>qui</a> per procedere con la registrazione.";

        $subject = $task->email_subject;
        $urlImage = \Yii::$app->params['platform']['backendUrl'] . "/img/img_default.jpg";

        $subjectWithParams = EventMailUtility::parseEmailWithParams(null, null, $subject, false);
        $textWithParms = EventMailUtility::parseEmailWithParams(null, null, $text);

        $newsletterModule = \Yii::$app->getModule('newsletter');
        if ($newsletterModule) {
            // GET MAILUP TEMPLATE AND SUBTITUTION OF [CONTENT] WITH PLATFORM PERSONALIZED EMAIL TEXT
            $mailServiceClassname = $newsletterModule->mail_service_driver;
            $mailService = new $mailServiceClassname();
            $emailDecoded = $mailService->getEmail($this->mailupListId, $this->originalMessageId);
            $this->checkMailupResponse($emailDecoded, "Impossibile recuperare l'email/template da inviare");

            $htmlWithParams = EventMailUtility::parseDynamicContentMailup($urlImage, $textWithParms, $emailDecoded, \open20\amos\events\models\EventEmailTemplates::FOOTER_TYPE_NO_FOOTER);

            // COPY THE MAILUP MESSAGE
            Console::stdout('--- COPY MESSAGE ' . $this->originalMessageId . PHP_EOL);
            $decodedCopiedMessage = $mailService->copyMessage($this->mailupListId, $this->originalMessageId, [
                'Subject' => $subjectWithParams,
                'Notes' => "I_ID: " . $task->id . ' E_ID: ' . $task->id
            ]);
            $this->checkMailupResponse($decodedCopiedMessage, "Impossibile copiare il email/template");


            // UPDATE THE MESSAGE COPIED WITH THE MODIFIED TEMPLATE
            $idList = $decodedCopiedMessage->idList;
            $idMessage = $decodedCopiedMessage->idMessage;
            $decoded = $mailService->getEmail($idList, $idMessage);
            $this->checkMailupResponse($decoded, "Impossibile recuperare 'email/template copiato");

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
            $this->checkMailupResponse($decodedReponse, "Impossibile aggiornare l'email copiatao");

            $mailService->enableDisableDynamicFieldsEmail($idList, $idMessage, 'true');

            // SEND MESSAGE TO THE GROUP CRATED FOR THE INVITATION
            Console::stdout('--- SEND EMAIL IMPORT' . ' TO GROUP:  ' . $task->mailup_group_id . PHP_EOL);
            $sent = $mailService->sendEmailToGroup($task->mailup_group_id, $idMessage);
            $this->checkMailupResponse($sent, "Impossibile inviare l'email al gruppo");

            $task->mailup_message_id = $idMessage;
            $task->mailup_list_id = $idList;
            $task->mailup_sending_id = $sent->Id;
            $task->status = UserImportTaskStatus::STATUS_SENDING;
            $task->save(false);

        }
    }

    /**
     * @param $task UserImportTask
     * @throws MailupComunicationException
     */
    public function createMailGroup($task)
    {

        //-- CREATE THE MAILUP GROUP IF NOT EXISTS
        if (empty($task->mailup_group_id)) {
            $data = [
                'Name' => $task->id . '_' . rand(1, 9999) . '_' . substr(str_replace('\'', '', $task->name), 0, 20),
                'Notes' => 'Id import task: ' . $task->id

            ];
            $groupDecoded = $this->mailService->createGroup($this->mailupListId, $data);
            $this->checkMailupResponse($groupDecoded, 'Errore nela creazione del gruppo su mailup');


            if ($groupDecoded) {
                if (!empty($groupDecoded->ErrorCode)) {
                    Console::stdout(json_encode($groupDecoded) . PHP_EOL);
                } else {
                    Console::stdout('--- CREATE GROUP ' . $groupDecoded->idGroup . PHP_EOL);
                    $task->mailup_group_id = $groupDecoded->idGroup;
                    $task->mailup_list_id = $this->mailupListId;
                    $task->save(false);
                }
            }
        }
    }

    /**
     * @param $task
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function importFromExcel($task)
    {
        $filePath = $task->file_input->getPath();
        $reader = $this->buildReader($task);
        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $isHeader = true;

        $this->Warning = 0;
        $nFound = 0;
        $i = 0;
        Console::stdout("- IMPORT FROM EXCEL: " . $filePath . PHP_EOL);
        Console::stdout("- dg: " . $task->event_group_referent_id . PHP_EOL);
//        $task->status = UserImportTaskStatus::WORKING;
        $task->save(false);

        try {
            foreach ($worksheet->getRowIterator() as $row) {
                if (!$isHeader) {
                    $i++;
                    $cellIterator = $row->getCellIterator();
                    $values = [];
                    foreach ($cellIterator as $cell) {
                        $values[] = $cell->getValue();
                    }
                    // skip rows without values
                    $isRowEmpty = true;
                    foreach ($values as $val) {
                        $isRowEmpty = $isRowEmpty && empty($val);
                    }

                    if (!$isRowEmpty && count($values) >= self::MIN_COLUMNS) {
                        $user = $this->isUserPresent($values);
                        $isImported = $this->isImportedInDg($values, $task);
                        if (is_null($user)) {
                            if (!empty($values[self::EMAIL_POS])) {
                                $userImported = $this->isEmailImportedInOtherList($values, $task);
                                $userImport = new UserImportTaskUser();
                                $userImport->user_import_task_id = $task->id;
                                $userImport->email = $values[self::EMAIL_POS];
                                $userImport->name = $values[self::NAME_POS];
                                $userImport->surname = $values[self::SURNAME_POS];
                                $userImport->to_send = true;
                                if ($userImported) {
                                    $userImport->token = $userImported->token;
                                    $userImport->expire_date = $userImported->expire_date;
                                    $userImport->to_send = false;
                                    $this->fillErrWorkSheet($values,
                                        ["Utente già importato"]);
                                    $this->Warning = $this->Warning + 1;

                                } else {
                                    $expireDate = date('Y-m-d H:i:s', strtotime("+5 days"));
                                    $userImport->expire_date = $expireDate;
                                    $userImport->token = hash('md5', $task->id) . hash('md5', $i);
                                }

                                if ($userImport->validate('email') && $userImport->save(false)) {
                                    $this->fillOkWorkSheet($values);
                                } else {
                                    $err = '';
                                    foreach ($userImport->getErrors() as $error => $message) {
                                        $err .= " - " . $error . ': ' . implode(' ', $message);
                                    }
                                    $this->fillErrWorkSheet($values,
                                        ['Non importato: ', $err]);
                                }
                            } else {
                                $this->fillErrWorkSheet($values,
                                    ['Non importato: email mancante']);
                            }


                        } else {
                            if ($user && $user->userProfile->attivo == 0) {
                                $this->fillErrWorkSheet($values,
                                    ['Non importato: Utente disattivato']);
                            } else if ($isImported) {
                                $this->fillErrWorkSheet($values,
                                    ['Utente già importato']);
                            } else {
                                $this->connectDG($user, $task);
                                \open20\amos\core\models\UserActivityLog::registerLog(\Yii::t('amosuserimporter', 'Importazione utente'), $task, Event::LOG_TYPE_INVITATION_SENT, null, $user->id);
                                $this->fillOkWorkSheet($values);
                            }

                        }
                    }
                } else {
                    $isHeader = false;
                }
            }
            $ok_file_name = $this->tempPath . DIRECTORY_SEPARATOR . $this->generateFileName();
            $writer = new Xlsx($this->Okspreadsheet);
            $writer->save($ok_file_name);
            $err_file_name = $this->tempPath . DIRECTORY_SEPARATOR . $this->generateFileName();
            $writer = new Xlsx($this->Errspreadsheet);
            $writer->save($err_file_name);

            $this->storage->attachFile($ok_file_name, $task,
                'file_success_input', true);
            $this->storage->attachFile($err_file_name, $task,
                'file_errors_input', true);
            $task->status = UserImportTaskStatus::STATUS_USERS_TO_IMPORT;
            $task->tot_input_processed = $this->Okrow + $this->Errrow - 2 - $this->Warning;
            $task->tot_input_imported = $this->Okrow - 1;
            $task->save();

            Console::stdout("- processed: " . $task->tot_input_processed . PHP_EOL);
            Console::stdout("- imported: " . $task->tot_input_imported . PHP_EOL);
        } catch (Exception $e) {

        }

    }

    /**
     * @param $task UserImportTask
     * @return bool
     * @throws InvalidConfigException
     * @throws MailupComunicationException
     * @throws \yii\db\Exception
     */
    public function getUserAndImport($task)
    {
        if ($task->mailup_group_id) {
            //--- GET USERS TO IMPORT TO THE MAILUP GROUP ---
            $query = $task->getUserImportTaskUsers()->andWhere(['to_send' => true]);
            $task->n_user_found = $query->count();
            $task->save(false);

            // SEGMENTED QUERY
            $queryComplete = clone $query;
            // recupero gli utenti con un limit (x)
            /** @var $queryComplete ActiveQuery */
            $queryComplete
                ->andWhere(['mailup_import_id' => null])
                ->limit(self::LIMIT_USER_TO_IMPORT);
            $result = $queryComplete
                ->all();

            $currentFound = count($result);
            $users = [];

            $i = 0;
            Console::stdout('found - ' . $currentFound . PHP_EOL);

            if ($currentFound == 0 && $task->n_user_found > 0) {
                Console::stdout('--- GET USER AND IMPORT COMPLETED - ' . $task->mailup_import_id . PHP_EOL);
                return true;
            } else if ($currentFound == 0 && $task->n_user_found == 0) {
                $task->status = UserImportTaskStatus::STATUS_NO_USER_TO_IMPORT;
                $task->save(false);
                Console::stdout('- no user to import' . PHP_EOL);
                return true;
            }
            if ($currentFound > 0) {
                if (!empty($task->mailup_import_id)) {
                    $importDecoded = $this->mailService->getImport($task->mailup_import_id);
                    $this->checkMailupResponse($importDecoded, "Impossibile recuperare l'importazione");

                    // se l'importazione preCedente è completa azzero l'id di importazione e continuo con gli altri utenti
                    if ($importDecoded->Completed == true) {
                        Console::stdout('--- PARTIAL IMPORT COMPLETED - ' . $task->mailup_import_id . PHP_EOL);
                        $task->mailup_import_id = null;
                        $task->save(false);
                    } else {
                        //esco dalla funzione ed attendo che l'importazione sia completata
                        Console::stdout('--- PARTIAL IMPORT NOT COMPLETED - ' . $task->mailup_import_id . PHP_EOL);
                        return false;
                    }
                }
                Console::stdout('--- Prepare data for mailup ' . PHP_EOL);
                foreach ($result as $row) {
                    $fields = [];
                    Console::stdout($row->name . PHP_EOL);
                    foreach ($this->dynamics_fields as $id => $field) {
                        if ($field == 'nome') {
                            $fields[] = ['Id' => $id, 'Value' => $row->name];
                        }
                        if ($field == 'cognome') {
                            $fields[] = ['Id' => $id, 'Value' => $row->surname];
                        }
                        if ($field == 'token') {
                            $fields[] = ['Id' => $id, 'Value' => $row->token];
                        }
                    }
                    $users [] = [
                        'Email' => $row->email,
                        'Fields' => $fields,
                        'Name' => ''
                    ];
                }

                // --- IMPORT USERS TO THE GROUP AND UPDATE DATAS
                Console::stdout('--- IMPORT ' . $task->n_user_found . ' USERS to group ' . $task->mailup_group_id . PHP_EOL);
                $importId = $this->mailService->importRecipientsToGroups($users, $task->mailup_group_id);
                $this->checkMailupResponse($importId, "Impossibile importare gli utenti dentro gruppo");

                if (is_integer($importId)) {
                    $task->mailup_import_id = $importId;
                    $task->status = UserImportTaskStatus::STATUS_IMPORTING_USERS;
                    $task->save(false);

                    /** @var  $model UserImportTaskUser */
                    foreach ($result as $model) {
                        $model->mailup_import_id = $importId;
                        $model->save(false);
                    }

                    return false;
                }
            } else {
                $task->status = UserImportTaskStatus::STATUS_NO_USER_TO_IMPORT;
                $task->save(false);
                Console::stdout('- no user to import' . PHP_EOL);
                return true;
            }
        }
        return true;
    }

    /**
     * @param $internalList
     */
    public function sendingCompleted($internalList)
    {
        $isComplete = true;
        Console::stdout('--- check sending waiting sending' . PHP_EOL);
        $reportSendingWiting = $this->mailService->getWaitingSending();
//        $this->checkMailupResponse($reportSendingWiting, "Impossibile recupereare info su waiting sending");

        if (!empty($reportSendingWiting) && !empty($reportSendingWiting->Items)) {
            foreach ($reportSendingWiting->Items as $item) {
                if (!empty($item->Id)) {
                    $isComplete = false;
                }

            }
        }
        Console::stdout('--- check sending ongoing' . PHP_EOL);
        $reportSendingOngoing = $this->mailService->getOngoingSending();
//        $this->checkMailupResponse($reportSendingOngoing, "Impossibile recupereare info su ongoing seding");

        if (!empty($reportSendingOngoing) && !empty($reportSendingOngoing->Items)) {
            foreach ($reportSendingOngoing->Items as $item) {
                if (!empty($item->Id)) {
                    $isComplete = false;
                }

            }
        }
        if ($isComplete) {
            Console::stdout('--- SENDING COMPLETE' . PHP_EOL);
            $internalList->status = UserImportTaskStatus::STATUS_SENT;
            $history = $this->mailService->getEmailSendHistory($internalList->mailup_list_id, $internalList->mailup_message_id, []);
//            $this->checkMailupResponse($history, "Impossibile recupereare history");

            $n = 0;
            if (!empty($history->Items)) {
                $elem = $history->Items[0];
                $n = $elem->Recipients;
            }
            $internalList->n_sent = $n;
            $internalList->save(false);
        }
    }

    /**
     * @throws \Exception
     */
    public function actionDeleteDeactivatedUsers()
    {
        Console::stdout('##################################################' . PHP_EOL);
        Console::stdout('##### START CRON delete users                #####' . PHP_EOL);
        Console::stdout('##### ' . date('d-m-Y h:i:s') . '                    #####' . PHP_EOL);
        Console::stdout('##################################################' . PHP_EOL);

        $users = UserProfile::find()->andWhere(['attivo' => false])
            ->andWhere(['<', new Expression('DATE_ADD( disactivate_at, INTERVAL 13 MONTH)'), new Expression('NOW()')])->all();
//        $query = UserProfile::find()->andWhere(['attivo' => false])
//            ->andWhere(['<', new Expression('DATE_ADD( disactivate_at, INTERVAL 13 MONTH)'), new Expression('NOW()')])->createCommand()->rawSql;
//        Console::stdout($query . PHP_EOL);

        foreach ($users as $profile) {
            Console::stdout('--- WORKING ON USER ' . $profile->user_id . PHP_EOL);

            $email = $profile->user->email;
            $moduleAdmin = \Yii::$app->getModule('admin');
            $dropController = new UserDropController('user_drop', $moduleAdmin);
//
//            //Irreversible action of user drop
            if (!empty($moduleAdmin) && $moduleAdmin->hardDelete) {
                $dropController->dropEverything($profile->user_id);
            } else {
                $dropController->softDropEverything($profile->user_id);
            }
            Console::stdout('--- USER ' . $profile->id . ' DELETED - ' . $email . PHP_EOL);

        }

    }


    /**
     *
     * @param array $values
     */
    protected function fillOkWorkSheet(array $values)
    {
        $this->Okworksheet->setCellValue('A' . $this->Okrow, $values[0]);
        $this->Okworksheet->setCellValue('B' . $this->Okrow, $values[1]);
        $this->Okworksheet->setCellValue('C' . $this->Okrow, $values[2]);
        $this->Okrow++;
    }

    /**
     *
     * @param array $values
     */
    protected function fillErrWorkSheet(array $values, $messages = null)
    {
        $this->Errworksheet->setCellValue('A' . $this->Errrow, $values[0]);
        $this->Errworksheet->setCellValue('B' . $this->Errrow, $values[1]);
        $this->Errworksheet->setCellValue('C' . $this->Errrow, $values[2]);
        if (!is_null($messages)) {
            $this->Errworksheet->setCellValue('D' . $this->Errrow,
                Json::encode($messages));
        }
        $this->Errrow++;
    }

    /**
     *
     * @param array $values
     * @return User
     */
    protected function isUserPresent(array $values)
    {
        $usr = User::findOne(['email' => $values[static::EMAIL_POS]]);
        return $usr;
    }

    /**
     * @param array $values
     * @return mixed
     * @throws InvalidConfigException
     */
    protected function isEmailImportedInOtherList(array $values, $task)
    {
        $taskuser = UserImportTaskUser::find()
            ->andWhere(['email' => $values[static::EMAIL_POS]])
            ->andWhere(['!=', 'user_import_task_id', $task->id])->one();
        return $taskuser;
    }

    /**
     * @param array $values
     * @param $task
     * @param $dg
     * @return bool
     * @throws InvalidConfigException
     */
    protected function isImportedInDg(array $values, $task)
    {
        $taskuser = UserImportTaskUser::find()
            ->innerJoinWith('userImportTask')
            ->andWhere(['user_import_task.deleted_at' => null])
            ->andWhere(['email' => $values[static::EMAIL_POS]])
            ->andWhere(['event_group_referent_id' => $task->event_group_referent_id])->count();
        return $taskuser > 0;
    }


    /**
     * @return \PhpOffice\PhpSpreadsheet\Reader\IReader
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    protected function buildReader($task)
    {
        $filePath = $task->file_input->getPath();

        switch ($task->file_input->type) {
            case 'zip':

                break;
        }
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        return $reader;
    }


    /**
     *
     * @param User $user
     */
    protected function connectDG(User $user, $task)
    {
        $eventgroup = new EventGroupReferentMm();
        $eventgroup->user_id = $user->id;
        $eventgroup->event_group_referent_id = $task->event_group_referent_id;
        $eventgroup->exclude_from_query = 0;
        $eventgroup->save();
    }


    /**
     *
     * @param string $urlFile
     * @return string
     */
    protected function generateFileName()
    {
        return \Yii::$app->security->generateRandomString() . ".xlsx";
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
     * @param UserImportTask $model
     */
    private function addErrorToInvitation($model)
    {
        // incremento il numero di errori sulla campagna
        $number = intval(is_null($model->mailup_errors) ? 0 : (int)$model->mailup_errors);
        $model->mailup_errors = ($number + 1);

        if ($model->mailup_errors >= $this->maxError) {
            $model->status = UserImportTaskStatus::STATUS_ERROR;
        }
        $model->save(false);
    }

    /**
     * @param $e Exception
     * @param $model UserImportTask
     */
    private function logError($e, $model)
    {
        Console::stdout('----------- ERROR !!!! ' . $e->getMessage() . '----------- ' . PHP_EOL);
        Console::stdout('in ' . $e->getFile() . ' - line: ' . $e->getLine() . PHP_EOL);
        Console::stdout($e->getTraceAsString() . PHP_EOL);
        Console::stdout('----------- END LIST ' . $model->id . ' - ' . $model->name . '----------- ' . PHP_EOL);

        $myfile = fopen(\Yii::$app->basePath . '/cron_log/' . "error_cron_import_user.txt", "a+") or die("Unable to open file!");
        $txt = '----------- START IMPORT LIST ' . $model->id . ' - ' . $model->name . '----------- ' . "\n";
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

//        unlink($this->pidFile);

    }


    /**
     * @return bool
     */
    public function checkAlreadyRun()
    {
        $tmpDir = \Yii::$app->runtimePath;
        $pidFile = $tmpDir . '/user_inviter_cron.pid';
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
        Console::stdout($dateLastModify . PHP_EOL);
        $now = date("d F Y H:i:s");
        $timestamp2 = strtotime($now);
        $hour = abs($timestamp2 - $timestampLastModify) / (60 * 60);
        Console::stdout($hour . PHP_EOL);
        if ($hour > 1) {
            Console::stdout('Deleting .pid file - last modified ' . json_encode($dateLastModify) . PHP_EOL);
            $isRunning = false;
            unlink($pidFile);
        }
        return $isRunning;

    }

}