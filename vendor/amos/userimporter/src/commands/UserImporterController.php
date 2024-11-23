<?php

namespace amos\userimporter\commands;

use amos\userimporter\base\UserImportTaskStatus;
use amos\userimporter\models\UserImportTask;
use open20\amos\admin\models\UserProfile;
use open20\amos\admin\utility\UserProfileUtility;
use open20\amos\core\user\User;
use open20\amos\core\utilities\Email;
use open20\amos\cwh\models\CwhTagOwnerInterestMm;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventGroupReferentMm;
use open20\amos\tag\models\Tag;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;
use yii\log\Logger;
use yii\helpers\Console;

class UserImporterController extends Controller
{
    const NAME_POS    = 0;
    const SURNAME_POS = 1;
    const EMAIL_POS   = 2;
    const CF_POS = 3;

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

    public function init()
    {
        parent::init();

        $this->storage  = Yii::$app->getModule('attachments');
        $this->tempPath = Yii::getAlias($this->storage->tempPath);

        $this->Okspreadsheet  = new Spreadsheet();
        $this->Errspreadsheet = new Spreadsheet();
        $this->Okworksheet    = $this->Okspreadsheet->getActiveSheet();
        $this->Errworksheet   = $this->Errspreadsheet->getActiveSheet();

        $this->Okrow  = 1;
        $this->Errrow = 1;
    }

    /**
     * 
     */
    public function actionWorkingQueie()
    {
        try {
            Console::stdout('##################################################' . PHP_EOL);
            Console::stdout('##### START CRON user-importer/working-queue #####' . PHP_EOL);
            Console::stdout('##### '.date('d-m-Y h:i:s').'                    #####' . PHP_EOL);
            Console::stdout('##################################################' . PHP_EOL);
            $this->task = UserImportTask::find()->andWhere(['status' => UserImportTaskStatus::PENDING])->one();


            if (!is_null($this->task)) {
                Console::stdout("- WORKING on task: ".$this->task->id . PHP_EOL);
                $this->task->status = UserImportTaskStatus::WORKING;
                $this->task->save(false);
                $filePath           = $this->task->file_input->getPath();
                $reader             = $this->buildReader();
                $spreadsheet        = $reader->load($filePath);
                $worksheet          = $spreadsheet->getActiveSheet();
                $isHeader           = true;

                $i=0;
                foreach ($worksheet->getRowIterator() as $row) {
                    if (!$isHeader) {
                        $i++;
                        $cellIterator = $row->getCellIterator();
                        $values       = [];
                        foreach ($cellIterator as $cell) {
                            $values[] = $cell->getValue();
                        }
                        if(count($values) >= self::MIN_COLUMNS)
                        {
                            $user = $this->isUserPresent($values);
                            if (is_null($user)) {
                                $result = $this->createUser($values);
                                if (isset($result['user'])) {
                                    /** var User $user */
                                    $user        = $result['user'];
                                    $userProfile = $user->userProfile;
                                    $this->connectDG($user);
                                    $this->connectTags($user, $userProfile);
                                    $this->settingUpUser($user, $userProfile);
                                    $this->fillOkWorkSheet($values);
                                    $this->sendOutMail($user);
                                } else {
                                    if (isset($result['error'])) {
                                        $this->fillErrWorkSheet($values,
                                            $result['messages']);
                                    } else {
                                        $this->fillErrWorkSheet($values);
                                    }
                                }
                            }else{
                                if($user->userProfile->attivo == 0){
                                    $this->fillErrWorkSheet($values,
                                        ['Non importato: Utente disattivato']);
                                }else {
                                    $this->connectDG($user);
                                    $this->fillOkWorkSheet($values);
                                }

                            }
                        }
                    } else {
                        $isHeader = false;
                    }
                }
                $ok_file_name  = $this->tempPath.DIRECTORY_SEPARATOR.$this->generateFileName();
                $writer        = new Xlsx($this->Okspreadsheet);
                $writer->save($ok_file_name);
                $err_file_name = $this->tempPath.DIRECTORY_SEPARATOR.$this->generateFileName();
                $writer        = new Xlsx($this->Errspreadsheet);
                $writer->save($err_file_name);

                $this->storage->attachFile($ok_file_name, $this->task,
                    'file_success_input', true);
                $this->storage->attachFile($err_file_name, $this->task,
                    'file_errors_input', true);
                $this->task->status              = UserImportTaskStatus::END;
                $this->task->tot_input_processed = $this->Okrow + $this->Errrow - 2;
                $this->task->tot_input_imported  = $this->Okrow - 1;
                $this->task->save();

                Console::stdout("- processed: ".$this->task->tot_input_processed  . PHP_EOL);
                Console::stdout("- imported: ".$this->task->tot_input_imported  . PHP_EOL);
                Console::stdout('##### END CRON user-importer/working-queue #####' . PHP_EOL);

            }
        } catch (Exception $bex) {
            Yii::getLogger()->log($bex->getTraceAsString(), Logger::LEVEL_ERROR);
            $this->task->status = UserImportTaskStatus::ERROR;
            $this->task->save();
        }
    }

    /**
     *
     * @param array $values
     */
    protected function fillOkWorkSheet(array $values)
    {
        $this->Okworksheet->setCellValue('A'.$this->Okrow, $values[0]);
        $this->Okworksheet->setCellValue('B'.$this->Okrow, $values[1]);
        $this->Okworksheet->setCellValue('C'.$this->Okrow, $values[2]);
        $this->Okrow++;
    }

    /**
     *
     * @param array $values
     */
    protected function fillErrWorkSheet(array $values, $messages = null)
    {
        $this->Errworksheet->setCellValue('A'.$this->Errrow, $values[0]);
        $this->Errworksheet->setCellValue('B'.$this->Errrow, $values[1]);
        $this->Errworksheet->setCellValue('C'.$this->Errrow, $values[2]);
        if (!is_null($messages)) {
            $this->Errworksheet->setCellValue('D'.$this->Errrow,
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
     *
     * @param UserImportTask $task
     * @return type
     */
    protected function buildReader()
    {
        $filePath = $this->task->file_input->getPath();

        switch ($this->task->file_input->type) {
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
    protected function settingUpUser(User $user, UserProfile $userProfile)
    {
        $userProfile->status                    = UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED;
        $userProfile->validato_almeno_una_volta = 1;
        $userProfile->is_imported = 1;
        $userProfile->user_import_task_id = $this->task->id;
        $userProfile->save(false);
    }

    /**
     *
     * @param User $user
     */
    protected function connectDG(User $user)
    {
        $eventgroup                          = new EventGroupReferentMm();
        $eventgroup->user_id                 = $user->id;
        $eventgroup->event_group_referent_id = $this->task->event_group_referent_id;
        $eventgroup->exclude_from_query      = 0;
        $eventgroup->save();
    }

    /**
     *
     * @param User $user
     */
    protected function connectTags(User $user, UserProfile $userProfile)
    {
        $root = Tag::find()->andWhere(['codice' => Event::ROOT_TAG_PREFERENCE_CENTER])->one();
        if (!is_null($user)) {
            $Tags = Tag::find()
                ->andWhere(['root' => $root->id])
                ->andWhere(['!=', 'id', $root->id])
                ->all();
            foreach ($Tags as $tag) {
                $userTag                     = new CwhTagOwnerInterestMm();
                $userTag->interest_classname = 'simple-choice';
                $userTag->classname          = UserProfile::className();
                $userTag->record_id          = $userProfile->id;
                $userTag->tag_id             = $tag->id;
                $userTag->root_id            = $root->id;
                $userTag->save();
            }
        }
    }

    /**
     *
     * @param array $values
     */
    protected function createUser(array $values)
    {
        return UserProfileUtility::createNewAccount($values[static::NAME_POS],
                $values[static::SURNAME_POS], $values[static::EMAIL_POS], 1,
                true);
    }

    /**
     *
     * @param string $urlFile
     * @return string
     */
    protected function generateFileName()
    {
        return Yii::$app->security->generateRandomString().".xlsx";
    }

    /**
     *
     * @param type $user
     * @return boolean
     */
    protected function sendOutMail($user)
    {
        $ret = false;
        try {

            $appName     = \Yii::$app->name;
            $token       = md5($user->id.$appName.$user->username);
            $subjectView = "email/subject";
            $contentView = "email/email";
            $subject     = Email::renderMailPartial($subjectView,
                    ['profile' => $user->userProfile], $user->id);
            $mail        = Email::renderMailPartial($contentView,
                    ['profile' => $user->userProfile, 'token' => $token],
                    $user->id);
            $ret         = Email::sendMail(Yii::$app->params['supportEmail'],
                    [$user->email], $subject, $mail, []);
        } catch (\Exception $ex) {
            \Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }

        return $ret;
    }
}