<?php

namespace open20\amos\events\commands;

use open20\amos\core\models\ModelsClassname;
use open20\amos\events\commands\BaseCronController;
use open20\amos\events\exceptions\MailupComunicationException;
use open20\amos\events\models\EventCommunication;
use open20\amos\events\models\EventInternalList;
use open20\amos\events\models\EventMailupErrors;
use Exception;
use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;
use amos\newsletter\utility\MailupUtility;
use open20\amos\events\utility\TableMessageBouncesUtility;
use open20\amos\events\utility\TableMessageClicksUtility;
use open20\amos\events\utility\TableMessageViewsUtility;
use DateInterval;
use DateTime;
use Yii;
use yii\helpers\Console;
use yii\helpers\Inflector;
use yii\console\Controller;


/**
 * Undocumented class
 */
class StatisticsController extends BaseCronController
{

    public $mailService;
//    private $dbstats = 'dbstats';
    private $dbstats = 'db';
    private $dayToStats = 7;

    /**
     *
     */
    public function init()
    {
        parent::init();

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
     */
    public function actionScanErrorsMailup()
    {
        $tmpDir = \Yii::getAlias('@backend').'/web';
        $dateFile = $tmpDir . '/last_date_error_mailup_cron.txt';
        $currentDate = date('Y-m-d H:i:s');
        $myfile = fopen($dateFile, "w") or die("Unable to open file!");
        $txt = $currentDate;
        fwrite($myfile, $txt);
        fclose($myfile);

        $internalLists = EventInternalList::find()
            ->andWhere(['status' => EventInternalList::STATUS_SENT])
            ->andWhere(['errors_processed' => 0])
            ->all();

        $eventCommunication = EventCommunication::find()
            ->andWhere(['status' => EventInternalList::STATUS_SENT])
            ->andWhere(['errors_processed' => 0])
            ->all();


        /** @var EventInternalList $list */
        foreach ($internalLists as $list) {
            try {
                Console::stdout('----------- START LIST ' . $list->id . '-' . $list->getRappresentativeName() . '----------- ' . PHP_EOL);

                $this->saveMailupBounceErrors($list);
            } catch (MailupComunicationException $me) {
                $this->addErrorToInvitation($list);
                $this->logError($me, $list);
            } catch (\Error $e) {
                $this->addErrorToInvitation($list);
                $this->logError($e, $list);
            } catch (\yii\db\Exception $e) {
                $this->logError($e, $list);
            }
            Console::stdout('----------- END LIST ' . $list->id . '----------- ' . PHP_EOL);

        }
        /** @var  $communication EventCommunication */
        foreach ($eventCommunication as $communication) {
            try {
                Console::stdout('----------- START COMMUNICATION LIST ' . $communication->id . '-' . $communication->getRappresentativeName() . '----------- ' . PHP_EOL);
                $this->saveMailupBounceErrors($communication);
            } catch (MailupComunicationException $me) {
                $this->addErrorToInvitation($communication);
                $this->logError($me, $communication);
            } catch (\Error $e) {
                $this->addErrorToInvitation($communication);
                $this->logError($e, $communication);
            } catch (\yii\db\Exception $e) {
                $this->logError($e, $communication);
            }
            Console::stdout('----------- END COMMUNICATION LIST ' . $communication->id . '----------- ' . PHP_EOL);

        }
    }


    /**
     * @param $list
     */
    public function saveMailupBounceErrors($list)
    {

        Console::stdout("LIST ID: - "  . "\n");
        $allData = MailupUtility::getAllDataFromFunction('getStatisticMessageListBounces', [$list->mailup_message_id]);
        $class = get_class($list);
        if (!empty($allData)) {
            $this->trace('scan-mailup-errors', "- $class LIST ID " . $list->id . " ");
            foreach ($allData as $item) {
                $model = new EventMailupErrors;
                foreach ($item as $field => $value) {
                    $fieldForDb = Inflector::camel2id($field, '_');
                    if (in_array($fieldForDb, ['email', 'id_message', 'id_recipient', 'type'])) {
                        $model->$fieldForDb = $value;
                    }
                }
                $model->created_at = date('Y-m-d H:i:s');
                $model->record_id = $list->id;
                $class = get_class($list);
                $modelClassname = ModelsClassname::find()->andWhere(['classname' => $class])->one();
                if ($modelClassname) {
                    $model->models_classname_id = $list->id;
                }
                $model->mailup_group_id = $list->mailup_group_id;
                $model->save(false);
//                VarDumper::dumpAsString($allData, 3, false)
            }

            // salvo il numero di errori sulla lista
            $list->mailup_stats_number_comunication_bounces = count($allData);
            $list->errors_processed = true;
            $list->save(false);

            $this->trace('scan-mailup-errors', "- ERRORI BOUNCE TROVATI: " . count($allData) . " elementi");

        }
        else{
            $list->mailup_stats_number_comunication_bounces = 0;
            $list->errors_processed = true;
            $list->save(false);
        }
    }

    /**
     * @throws InvalidConfigException
     */
    public function actionScanLists()
    {
        $currentDb = $this->dbstats;
        $internalLists = EventInternalList::find()
            ->andWhere(['status' => EventInternalList::STATUS_SENT])
            ->all();

        /** @var EventInternalList $list */
        foreach ($internalLists as $list) {

            try {
                $dateTimeToStats = $this->getTimeToStats($list);
                $dateTimeNow = new DateTime();
//                     Console::stdout("LIST ID: ".$list->id . ' - ' .VarDumper::dumpAsString($dateTimeToStats, 3, false) . "\n");

                // se sono passati n gg da quando è stata inviata la lista
                if ($dateTimeNow >= $dateTimeToStats) {
                    Console::stdout("WORKING ON LIST ID: " . $list->id . "\n");

                    // Se non essite la tabella allora raccolgo i dati e li inserisco
                    if (Yii::$app->$currentDb->schema->getTableSchema(TableMessageViewsUtility::getTableName($list->id), true) === null) {
                        $this->createTableMessageViews($list);
                    }

                    // Se non essite la tabella allora raccolgo i dati e li inserisco
                    if (Yii::$app->$currentDb->schema->getTableSchema(TableMessageClicksUtility::getTableName($list->id), true) === null) {
                        $this->createTableMessageClicks($list);
                    }

                    // Se non essite la tabella allora raccolgo i dati e li inserisco
                    if (Yii::$app->$currentDb->schema->getTableSchema(TableMessageBouncesUtility::getTableName($list->id), true) === null) {
                        $this->createTableMessageBounces($list);
                    }


                }

// Console::stdout(VarDumper::dumpAsString($this->mailService->getStatisticMessageCountClicks($channel->mailup_message_id) , 3, false) . "\n");
// Console::stdout(VarDumper::dumpAsString($this->mailService->getEmailSendHistory($channel->mailup_list_id, $channel->mailup_message_id, []) , 3, false) . "\n\n\n");


// Console::stdout(VarDumper::dumpAsString(MailupUtility::getAllDataFromFunction('getEmailRecipients', [$channel->mailup_message_id])) . "\n");
// Console::stdout(VarDumper::dumpAsString(MailupUtility::getAllDataFromFunction('getStatisticBounces', [$channel->mailup_message_id])) . "\n\n\n");
                // getSubscribtionsToGroup
// mailup_stats_number_comunication_sent
// mailup_stats_number_comunication_bounces


            } catch (MailupComunicationException $me) {
                if (!is_null($me->getObjError()) && isset($me->getObjError()->ErrorCode)) {
                    $list->mailup_error_message = 'Error code: ' . $me->getObjError()->ErrorCode . ': ' . $me->getObjError()->ErrorName;
                    $this->trace('scan-channel', 'ERROR MAILUP RESPONSE [response]: ' . VarDumper::dumpAsString($list->toArray()));
                } else {
                    $list->mailup_error_message = 'Errore generico di comunicazione con mailup...';
                }
                // salvo il messaggio di errore!
                $list->save(false);

                $this->trace('scan-channel', 'ERROR ' . $me->getCode() . ' [file]: ' . $me->getLine() . ' - ' . $me->getFile());
                $this->trace('scan-channel', 'ERROR ' . $me->getCode() . ' [message]: ' . $me->getMessage());
                $this->trace('scan-channel', 'ERROR ' . $me->getCode() . ' [trace]: ' . VarDumper::dumpAsString($me->getTraceAsString(), 3, false));
                $this->trace('scan-channel', 'ERROR ' . $me->getCode() . ' [channel]: ' . VarDumper::dumpAsString($list->toArray()));


            } catch (Exception $e) {
                $this->trace('scan-channel', 'ERROR ' . $e->getCode() . ' [file]: ' . $e->getLine() . ' - ' . $e->getFile());
                $this->trace('scan-channel', 'ERROR ' . $e->getCode() . ' [message]: ' . $e->getMessage());
                $this->trace('scan-channel', 'ERROR ' . $e->getCode() . ' [trace]: ' . VarDumper::dumpAsString($e->getTraceAsString(), 3, false));
                $this->trace('scan-channel', 'ERROR ' . $e->getCode() . ' [channel]: ' . VarDumper::dumpAsString($list->toArray()));

                // LOG ERRORE, proseguo sul resto
            }

        }
    }

    /**
     * @param $list
     * @return DateTime
     * @throws Exception
     */
    private function getTimeToStats($list)
    {
        $dateTimeSentString = $list->sent_at;
        $dateTimeSent = new DateTime($dateTimeSentString);
        $dateTimeToStats = clone $dateTimeSent;
        $dateTimeToStats = $dateTimeToStats->add(new \DateInterval('P' . $this->dayToStats . 'D'));

        return $dateTimeToStats;
    }

    /**
     * Undocumented function
     *
     * @param EventInternalList $channel
     * @return void
     */
    private function createTableMessageViews($list)
    {
        $allData = MailupUtility::getAllDataFromFunction('getStatisticMessageListViews', [$list->mailup_message_id]);;
//        Console::stdout("-: " . VarDumper::dumpAsString($allData, 3, false) . "\n");
        if (!empty($allData)) {
            $this->trace('scan-channel', "Internal list di ID " . $list->id . " riempie tabella " . TableMessageViewsUtility::getTableName($list->id));
            foreach ($allData as $item) {
                $model = TableMessageViewsUtility::buildModel($list->id, $this->dbstats);
                foreach ($item as $field => $value) {
                    // Console::stdout(Inflector::camel2id($field, '_') . "\n");
                    $fieldForDb = Inflector::camel2id($field, '_');
                    $model->$fieldForDb = $value;
                }
                $model->created_at = date('Y-m-d H:i:s');
                $model->communication_id = $list->id;
                $model->mailup_group_id = $list->mailup_group_id;

                $model->save();
            }

            // salvo il numero di views sul canale mm
            $list->mailup_stats_number_comunication_views = count($allData);
            $list->save(false);

            $this->trace('scan-channel', "Fine riempimento tabella " . TableMessageViewsUtility::getTableName($list->id) . " con " . count($allData) . " elementi");
        }

    }

    /**
     * Undocumented function
     *
     * @param EventInternalList $list
     * @return void
     */
    private function createTableMessageClicks($list)
    {
        $allData = MailupUtility::getAllDataFromFunction('getStatisticMessageListClicks', [$list->mailup_message_id]);

        if (!empty($allData)) {
            $this->trace('scan-channel', "InternalList di ID " . $list->id . " riempie tabella " . TableMessageClicksUtility::getTableName($list->id));
            foreach ($allData as $item) {
                $model = TableMessageClicksUtility::buildModel($list->id, $this->dbstats);
                foreach ($item as $field => $value) {
                    $fieldForDb = Inflector::camel2id($field, '_');
                    $model->$fieldForDb = $value;
                }
                $model->created_at = date('Y-m-d H:i:s');
                $model->communication_id = $list->id;
                $model->mailup_group_id = $list->mailup_group_id;

                $model->save();
            }

            // salvo il numero di clicks sul canale mm
            $list->mailup_stats_number_comunication_clicks = count($allData);
            $list->save(false);

            $this->trace('scan-channel', "Fine riempimento tabella " . TableMessageClicksUtility::getTableName($list->id) . " con " . count($allData) . " elementi");
        }

    }

    /**
     * Undocumented function
     *
     * @param EventInternalList $list
     * @return void
     */
    private function createTableMessageBounces($list)
    {
        $allData = MailupUtility::getAllDataFromFunction('getStatisticMessageListBounces', [$list->mailup_message_id]);

        if (!empty($allData)) {
            $this->trace('scan-list', "InternalList di ID " . $list->id . " riempie tabella " . TableMessageBouncesUtility::getTableName($list->id));
            foreach ($allData as $item) {
                $model = TableMessageBouncesUtility::buildModel($list->id, $this->dbstats);
                foreach ($item as $field => $value) {
                    $fieldForDb = Inflector::camel2id($field, '_');
                    $model->$fieldForDb = $value;
                }
                $model->created_at = date('Y-m-d H:i:s');
                $model->communication_id = $list->id;
                $model->mailup_group_id = $list->mailup_group_id;

                $model->save();
            }

            // salvo il numero di clicks sul canale mm
            $list->mailup_stats_number_comunication_bounces = count($allData);
            $list->save(false);

            $this->trace('scan-channel', "Fine riempimento tabella " . TableMessageBouncesUtility::getTableName($list->id) . " con " . count($allData) . " elementi");
        }

    }


}
