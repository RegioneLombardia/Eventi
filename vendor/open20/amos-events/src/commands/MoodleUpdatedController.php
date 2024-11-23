<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 29/04/2020
 * Time: 11:52
 */

namespace open20\amos\events\commands;


use open20\amos\events\AmosEvents;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventGroupReferentMm;
use open20\amos\events\models\EventInternalList;
use open20\amos\events\models\EventInvitationSent;
use open20\amos\events\utility\EventMailUtility;
use open20\amos\events\utility\EventsUtility;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Url;

class MoodleUpdatedController extends Controller
{

    public $moduleMoodle = null;
    public $enabled = false;
    public $courseid;
    public $role;
    public $pidFile;


    /**
     *
     */
    public function init()
    {
        parent::init();

        $moduleMoodle = \Yii::$app->getModule('moodle');
        $moduleEvents = \Yii::$app->getModule('events');
        if ($moduleMoodle && $moduleEvents && !empty($moduleEvents->operatorCandidate['enabled'])) {
            $this->enabled = $moduleEvents->operatorCandidate['enabled'];
            $this->courseid = $moduleEvents->operatorCandidate['courseid'];
            $this->role = $moduleEvents->operatorCandidate['role'];
        } else {
            return false;
        }
        return true;
    }

    /**
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionSendEmailCoursesCompleted()
    {
        Console::stdout('###################################################' . PHP_EOL);
        Console::stdout('##### START CRON send-email-courses-completed #####' . PHP_EOL);
        Console::stdout('##### ' . date('d-m-Y h:i:s') . '                    #####' . PHP_EOL);
        Console::stdout('###################################################' . PHP_EOL);

        $isRunning = $this->checkAlreadyRun();
        if ($isRunning) {
            return true;
        } else {
            Console::stdout('---- LOCK CRON' . PHP_EOL);
        }

        $members = EventGroupReferentMm::find()
            ->andWhere(['IS', 'status', null])
            ->andWhere(['exclude_from_query' => true])
            ->andWhere([
                'OR',
                ['status' => null],
                ['NOT IN', 'status', [
                    EventGroupReferentMm::STATUS_REQUEST_ACTIVATION,
                    EventGroupReferentMm::STATUS_ACTIVE
                ]
                ]
            ])
            ->all();

        /** @var  $member EventGroupReferentMm */
        foreach ($members as $member) {
            Console::stdout('- PROCESSING user ' . $member->user_id . PHP_EOL);

            $isCompleted = EventsUtility::checkIfCourseIsCompleted($this->courseid, $member->user_id);
            if ($isCompleted) {
                Console::stdout('- COURSE COMPLETED ' . $member->user_id . PHP_EOL);

                $member->status = EventGroupReferentMm::STATUS_REQUEST_ACTIVATION;
                if ($member->save(false)) {
                    EventMailUtility::sendEmailEnableOperator($member, $member->user_id);
                }
            }
        }
        unlink($this->pidFile);
        return true;
    }

    /**
     * @return bool
     */
    public function checkAlreadyRun()
    {
        $tmpDir = \Yii::$app->runtimePath;
        $pidFile = $tmpDir . '/moodle_update_cron.pid';
        $this->pidFile = $pidFile;

        $isRunning = false;

//          Console::stdout(is_writable($pidFile) .' 1- '.PHP_EOL);
//          Console::stdout(is_writable($tmpDir) .' 2- '.PHP_EOL);
//          Console::stdout(file_exists($pidFile) .' 3- '.PHP_EOL);
        if (is_writable($pidFile) || is_writable($tmpDir)) {
            if (file_exists($pidFile)) {
                $pid = (int)trim(file_get_contents($pidFile));
                $isRunning = true;
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

}