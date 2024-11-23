<?php

namespace amos\userimporter\models;

use amos\userimporter\base\UserImportTaskStatus;
use amos\userimporter\models\base\UserImportTask as BaseUserImportTask;
use open20\amos\attachments\behaviors\FileBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_import_task".
 */
class UserImportTask extends BaseUserImportTask
{

    const SCENARIO_CREATE_LIST = 'scenario_create_list';

    private $file_input;
    private $file_success_input;
    private $file_errors_input;
    public $email_test;

    public function getFile_input()
    {
        if (empty($this->file_input)) {
            $this->file_input = $this->hasOneFile('file_input')->one();
        }

        return $this->file_input;
    }

    public function getFile_success_input()
    {
        if (empty($this->file_success_input)) {
            $this->file_success_input = $this->hasOneFile('file_success_input')->one();
        }

        return $this->file_success_input;
    }

    public function getFile_errors_input()
    {
        if (empty($this->file_errors_input)) {
            $this->file_errors_input = $this->hasOneFile('file_errors_input')->one();
        }

        return $this->file_errors_input;
    }

    public function setFile_input($file_input)
    {
        $this->file_input = $file_input;
    }

    public function setFile_success_input($file_success_input)
    {
        $this->file_success_input = $file_success_input;
    }

    public function setFile_errors_input($file_errors_input)
    {
        $this->file_errors_input = $file_errors_input;
    }

    /**
     *
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),
                [
                    'fileBehavior' => [
                        'class' => FileBehavior::className()
                    ],
        ]);
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
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE_LIST] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
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
        return ArrayHelper::merge(parent::rules(),
                [
                    [['email_test'],'safe'],
                    [['file_input'],'required', 'on' => self::SCENARIO_DEFAULT],
                    [['file_input'],
                        'file',
                        'skipOnEmpty' => true,
                        'extensions' => (!empty($this->module)) ? $this->module->whiteListFilesExtensions
                            : '',
                        'checkExtensionByMimeType' => true,
                        'maxFiles' => 1,
                    ],
                    [['file_success_input'],
                        'file',
                        'skipOnEmpty' => true,
                        'extensions' => (!empty($this->module)) ? $this->module->whiteListFilesExtensions
                            : '',
                        'checkExtensionByMimeType' => false,
                        'maxFiles' => 1,
                    ],
                    [['file_errors_input'],
                        'file',
                        'skipOnEmpty' => true,
                        'extensions' => (!empty($this->module)) ? $this->module->whiteListFilesExtensions
                            : '',
                        'checkExtensionByMimeType' => false,
                        'maxFiles' => 1,
                    ],
        ]);
    }

    public function attributeLabels()
    {
        return
            ArrayHelper::merge(
                parent::attributeLabels(), [
        ]);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function importFromDbList(){
        $users = UserImportEditList::find()->all();

        $i = 0;
        foreach ($users as $user){
            $i++;
            $userImport = new \amos\userimporter\models\UserImportTaskUser();
            $userImport->user_import_task_id = $this->id;
            $userImport->email = $user->email;
            $userImport->name = $user->name;
            $userImport->surname = $user->surname;
            $userImport->to_send = true;
            $expireDate = date('Y-m-d H:i:s', strtotime("+5 days"));
            $userImport->expire_date = $expireDate;
            $userImport->token = hash('md5', $this->id) . hash('md5', $i);
            $userImport->save(false);
        }
        $this->status = UserImportTaskStatus::STATUS_USERS_TO_IMPORT;
        $this->tot_input_processed = $i;
        $this->tot_input_imported = $i;
        $this->save(false);

        UserImportEditList::deleteAll();
        \Yii::$app->session->addFlash('success',\Yii::t('amosuserimporter','Sono stati invitati <strong>{i}</strong> utenti',['i' => $i]));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function generateExcelEditList(){
        $users = UserImportTaskUser::find()
            ->andWhere(['user_import_task_id' => $this->id])->asArray()->all();

        $xlsData[0] = ["Nome", "Cognome", "Email"];
        //recupera i dati
        foreach ($users as $user) {
            $xlsData [] = [
                $user['name'],
                $user['surname'],
                $user['email'],
            ];
        }

        //inizializza l'oggetto excel
        $objPHPExcel = new \PHPExcel();
        //li pone nella tab attuale del file xls
        $objPHPExcel->getActiveSheet()->fromArray($xlsData, NULL, 'A1');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('/tmp/User_list.xls');
        return \Yii::$app->response->sendFile('/tmp/User_list.xls');
        die;
    }
}