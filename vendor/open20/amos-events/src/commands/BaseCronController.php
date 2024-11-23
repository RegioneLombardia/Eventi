<?php

namespace open20\amos\events\commands;

use open20\amos\core\user\User;
use open20\amos\events\exceptions\MailupComunicationException;
use open20\amos\events\models\EventInternalList;
use yii\console\Controller;
use yii\helpers\Console;
use Yii;

class BaseCronController extends Controller
{
    private $usernameEnabled = 'CRON';

    private $timeStart;
    public $pidFile;
    public $maxError = 5;

    /**
     * @return string
     */
    public function getUsernameEnabled()
    {
        return $this->usernameEnabled;
    }


    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_ACTION, [$this, 'beforeAction']);
        $this->on(self::EVENT_AFTER_ACTION, [$this, 'afterAction']);
    }


    /**
     * @return false|string
     */
    public function getNow()
    {
        return date('Y-m-d H:i:s');
    }

    public function actionTest()
    {
        Console::stdout("User loggato: " . Yii::$app->user->getIdentity()->username . "\n");
        Console::stdout("User email loggato: " . Yii::$app->user->getIdentity()->email . "\n");
        Console::stdout("ID di sessione: " . Yii::$app->getSession()->id . "\n");
        $logReturn['code'] = 0;
        $logReturn['message'] = "User loggato: " . Yii::$app->user->getIdentity()->username . "\n" .
            "User email loggato: " . Yii::$app->user->getIdentity()->email . "\n" .
            "ID di sessione: " . Yii::$app->getSession()->id . "\n";
        $corpo [] = "Contenuto della mail - Email di test";
        $this->inviaMail(Yii::$app->user->getIdentity()->email, [], 'Email di test da CRON', $corpo);
        return $logReturn;
    }

    /**
     * @param string $name
     */
    public function trace($scope, $message)
    {
        $message .= "\n";
        $message = '[' . date_format(new \DateTime('now'), 'Y-m-d H:i:s') . ']: ' . $message;
        Console::stdout($message);
        Yii::debug($message, 'cron');
    }

    /**
     * @param yii\base\InlineAction $event
     * @return bool
     * @internal param \yii\base\Action $action
     */
    public function beforeAction($event)
    {
        Console::stdout('##################################################' . PHP_EOL);
        Console::stdout('##### START CRON ' . \Yii::$app->controller->action->id . '      #####' . PHP_EOL);
        Console::stdout('##### ' . date('d-m-Y h:i:s') . '                    #####' . PHP_EOL);
        Console::stdout('##################################################' . PHP_EOL);


        return true;
    }

    public function afterAction($action, $result)
    {
        Console::stdout("##### END CRON: " . $action->id . "\n");
        $messaggi = 'Codice restituito: [' . $result['code'] . "]\n" . $result['message'];

        Yii::debug('messaggio', 'END ACTION: ' . $messaggi);

        return true;
    }


    /**
     * @param $response
     * @param $message
     * @throws MailupComunicationException
     */
    protected function checkMailupResponse($response, $message)
    {
        if (isset($response->ErrorCode)) {
            $exception = new MailupComunicationException($message . ' - ' . $response->ErrorDescription, $response->ErrorCode);
            $exception->setObjError($response);
            throw $exception;
        }
    }

    /**
     * @param $model
     */
    protected function addErrorToInvitation($model)
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
     * @param $e
     * @param $model
     */
    protected function logError($e, $model)
    {
        $action_id = \Yii::$app->controller->action->id;

        Console::stdout('----------- ERROR !!!! ' . $e->getMessage() . '----------- ' . PHP_EOL);
        Console::stdout('in ' . $e->getFile() . ' - line: ' . $e->getLine() . PHP_EOL);
        Console::stdout($e->getTraceAsString() . PHP_EOL);
        Console::stdout('----------- END LIST ' . $model->id . ' - ' . $model->name . '----------- ' . PHP_EOL);

        $myfile = fopen(\Yii::$app->basePath . '/cron_log/' . "error_cron_{$action_id}.txt", "a+") or die("Unable to open file!");
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
        $action_id = \Yii::$app->controller->action->id;
        $pidFile = $tmpDir . "/{$action_id}_cron.pid";
        $this->pidFile = $pidFile;

        $isRunning = false;

        if (is_writable($pidFile) || is_writable($tmpDir)) {
            if (file_exists($pidFile)) {
                $pid = (int)trim(file_get_contents($pidFile));
                $isRunning = $this->exitFromDeadlock($pidFile);
            }
        } else {
            Console::stdout("Cannot write {$action_id}_cron pid lock file. Exit script" . PHP_EOL);
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


}