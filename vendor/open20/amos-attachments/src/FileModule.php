<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\attachments
 * @category   CategoryName
 */

namespace open20\amos\attachments;

use open20\amos\attachments\helpers\AttachemntsHelper;
use open20\amos\attachments\models\File;
use open20\amos\core\exceptions\AmosAbuseReplyAttemptsException;
use open20\amos\core\interfaces\BreadcrumbInterface;
use open20\amos\core\module\AmosModule;
use open20\amos\core\utilities\ZipUtility;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\log\Logger;

/**
 * Class FileModule
 * @package open20\amos\attachments
 */
class FileModule extends AmosModule implements BreadcrumbInterface {

    /**
     * seconds Cache-Control duration
     * @var integer
     */
    public $cache_age = '86400';

    /**
     * The folder into which the link is thrown for direct access via http
     * @var string
     */
    public $webDir = 'files';

    /**
     * @var type
     */
    public $controllerNamespace = 'open20\amos\attachments\controllers';

    /**
     * @var type
     */
    public $storePath = '@app/uploads/store';

    /**
     * @var type
     */
    public $tempPath = '@app/uploads/temp';

    /**
     * @var type
     */
    public $rules = [];

    /**
     * @var type
     */
    public $tableName = 'attach_file';

    /**
     * @var type
     */
    public $config = [];

    /**
     * @var type
     */
    public $disableGallery = true;

    /**
     * @var type
     */
    public $enableSingleGallery = true;

    /**
     * @var type
     */
    public $enableDatabankFile = false;

    /**
     * @var bool $enableRequestImageForGallery
     */
    public $enableRequestImageForGallery = false;

    /**
     * @var type
     */
    public $codiceTagGallery = 'root_preference_center';

    /**
     * @var type
     */
    public $disableFreeCropGallery = false;

    /**
     * @var bool
     */
    public $showLuyaGallery = false;

    /**
     * @var int
     */
    public $luyaGalleryFolderId = null;
    /**
     * @var int
     */
    public $luyaDatabankFileFolderId = null;

    /**
     * @var bool
     */
    public $autoSaveInDatabanks = false;

    /**
     * If set to true it verifies that the parent record is visible to download
     * @var bool $checkParentRecordForDownload
     */
    public $checkParentRecordForDownload = false;

    /**
     * E.g: 'original'
     * @var string $forceCrop
     */
    public $forceCrop;

    /**
     * @var null|\amos\statistics\models\AttachmentsStatsInterface
     */
    public $statistics = null;

    /**
     *
     * @var bool $enable_aws_s3
     */
    public $enable_aws_s3 = false;

    /**
     *
     * @var string $aws_s3_bucket
     */
    public $aws_s3_bucket;

    /**
     *
     * @var string $aws_s3_secret_key
     */
    public $aws_s3_secret_key;

    /**
     *
     * @var string $aws_s3_access_key
     */
    public $aws_s3_access_key;

    /**
     *
     * @var string $aws_s3_base_dir
     */
    public $aws_s3_base_dir = 'files';

    /**
     * The base url without the last slash. E.g.: 'https://www.domain.net'
     * @var string $aws_s3_base_url
     */
    public $aws_s3_base_url;

    /**
     *
     * @var string $aws_s3_default_region
     */
    public $aws_s3_default_region = 'eu-central-1';

    /**
     * @var bool If true $virusScanClass must be set and required software must beinstalled
     */
    public $enableVirusScan = false;

    /**
     * @var string What class use to scan files or directories, $enableVirusScan must be true to use
     */
    public $virusScanClass = '\open20\amos\attachments\scanners\ClamAVScanner';

    /**
     * @var int restrict to load files only on tot seconds for action FileController::actionUploadFiles
     */
    public $abuseReplyAttemptsSeconds = 5;

    /**
     * @var string[]
     */
    public $whiteListExtensions = ['csv', 'doc', 'docx', 'pdf', 'rtf', 'txt', 'xls', 'xlsx', 'odt', 'ppt', 'pptx', 'zip', 'p7m', 'svg', 'png', 'gif', 'bmp','jpg', 'jpeg','eps'];

    /**
     * @var int Kb
     */
    public $maxFileSize = 15000;

    /**
     * If set to true it does not increment the download counter for cropped images
     * @var bool $disableCounterForCroppedImage
     */
    public $disableCounterForCroppedImage = false;

    /**
     *
     * @return string
     */
    public static function getModuleName() {
        return "attachments";
    }

    /**
     *  [
     *      'enable' => true,
            'clientId' => 'xxxxxxxxxxxxxxxxxxxxxxx',
            'clientSecret' => 'xxxxxxxxxxxx',
            'enableSandbox' => true
     *      'access_token' => 'xxxxxxx'
     *  ]
     * @var array
     */
    public $shutterstockConfigs = [];

    /**
     *
     * @return type
     */
    public function getWidgetIcons() {
        return [];
    }

    /**
     *
     * @return type
     */
    public function getWidgetGraphics() {
        return [];
    }

    /**
     * @throws Exception
     */
    public function init() {
        parent::init();

        if (empty($this->storePath) || empty($this->tempPath)) {
            throw new Exception(FileModule::t('amosattachments', 'Setup {storePath} and {tempPath} in module properties'));
        }

        //Configuration
        $config = require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
        \Yii::configure($this, ArrayHelper::merge($config, $this));

        $this->rules = ArrayHelper::merge(['maxFiles' => 3], $this->rules);
        $this->defaultRoute = 'attachments';
    }

    /**
     * @param string $suffix
     * @return string
     * @throws \Exception
     */
    public function getUserDirPath($suffix = '') {
        $sessionId = md5(0);

        if (\Yii::$app->has('session')) {
            \Yii::$app->session->open();
            $sessionId = \Yii::$app->session->id;
            \Yii::$app->session->close();
        }

        $userDirPath = $this->getTempPath()
                . DIRECTORY_SEPARATOR
                . $sessionId
                . $suffix;

        //Try dir creation
        FileHelper::createDirectory($userDirPath, 0777);

        //Check if the dir has been created
        if (!is_dir($userDirPath)) {
            throw new \Exception("Unable to create Upload Direcotory '{$userDirPath}'");
        }

        return $userDirPath . DIRECTORY_SEPARATOR;
    }

    /**
     * @return bool|string
     */
    public function getTempPath() {
        return \Yii::getAlias($this->tempPath);
    }

    /**
     * @param $obj
     * @return string
     */
    public function getShortClass($obj) {
        $className = get_class($obj);
        if (preg_match('@\\\\([\w]+)$@', $className, $matches)) {
            $className = $matches[1];
        }
        return $className;
    }

    /**
     *
     * @param string $filePath
     * @param \open20\amos\core\record\RecordDynamicModel $owner
     * @param string $attribute
     * @param boolean $dropOriginFile
     * @param boolean $saveWithoutModel
     * @param boolean $encrypt
     * @param string $filePersonalized
     * @param integer $originalAttachFileId
     * @return boolean|File
     * @throws \Exception
     */
    public function attachFile(
        $filePath,
        $owner,
        $attribute = 'file',
        $dropOriginFile = true,
        $saveWithoutModel = false,
        $encrypt = false,
        $filePersonalized = null,
        $originalAttachFileId = null
    )
    {
        if (!$saveWithoutModel) {
            if (!$owner->id) {
                throw new \Exception(FileModule::t('amosattachments', 'Owner must have id when you attach file'));
            }
        }

        if (!file_exists($filePath)) {
            throw new \Exception(FileModule::t('amosattachments', 'File not exist :') . $filePath);
        }

        //File infos
        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);
        $dirName = pathinfo($filePath, PATHINFO_DIRNAME);

        //Create a clean name for file
        $cleanName = preg_replace(
                "([\.]{2,})",
                '_',
                preg_replace(
                        "([^\w\d\-_~,;\[\]\(\).])",
                        '_',
                        $fileName
                )
        );

        // New location for the file
        $cleanFilePath = $dirName . DIRECTORY_SEPARATOR . $cleanName . '.' . $fileType;
//        pr($cleanFilePath);
//        die;
        if ($encrypt) {
            $fileTmp = new File();
            $zipTempFileName = uniqid();
            $zipTempCompletePath = $this->getTempPath() . DIRECTORY_SEPARATOR . $zipTempFileName . '.zip';
            $zipUtility = new ZipUtility([
                'filesToZip' => $filePath,
                'zipFileName' => $zipTempFileName,
                'destinationFolder' => $this->getTempPath(),
                'password' => $fileTmp->getDecryptPassword(),
            ]);
            $zipUtility->createZip();
            if (file_exists($zipTempCompletePath)) {
                $filePath = $zipTempCompletePath;
            }
        }

        //Rename current file to avoid any problem
        rename($filePath, $cleanFilePath);
        // compress it!
        if (in_array($fileType, ['jpg'])) {
            $image = imagecreatefromjpeg($cleanFilePath);
            imagejpeg($image, $cleanFilePath, 80);
        }

        //SystemHashSum
        if (strtoupper(PHP_OS) == 'WINNT') {
            $fileHash = hash_file('sha256', $cleanFilePath);
        } else {
            $escapedFilePath = escapeshellarg($cleanFilePath);
            exec("sha256sum {$escapedFilePath}", $fileHash);

            $filesize = filesize($cleanFilePath);
            $fileHash = hash('sha256', reset($fileHash) . $filesize);
        }

        //New file infos
        $newFileName = $fileHash . '.' . $fileType;
        $fileDirPath = $this->getFilesDirPath($fileHash);

        $newFilePath = $fileDirPath . DIRECTORY_SEPARATOR . $newFileName;

        if (!file_exists($cleanFilePath)) {
            throw new \Exception(FileModule::t(
                                    'amosattachments',
                                    'Cannot copy file! ')
                            . $cleanFilePath
                            . FileModule::t('amosattachments', ' to ')
                            . $newFilePath
            );
        }

        $ownerId = $saveWithoutModel ? null : $owner->id;

        $ownerClass = $this->getClass($owner);

        $tableNameOwner = null;
        if (
                $owner instanceof \open20\amos\core\record\RecordDynamicModel && method_exists($owner, 'getTableName')
        ) {
            $tblName = $owner->getTableName();
            if (!empty($tblName)) {
                $tableNameOwner = $tblName;
            }
        }

        $query = File::find()->andWhere([
            'hash' => $fileHash,
            'item_id' => $ownerId,
            'attribute' => $attribute,
            'model' => $ownerClass,
        ]);

        if (!is_null($tableNameOwner)) {
            $query->andFilterWhere(['table_name_form' => $tableNameOwner]);
        }

        //rename file
        if (!empty($filePersonalized)) {
            $fileName = $filePersonalized;
        }

        $exists = $query->one();
        if (!$exists) {
            copy($cleanFilePath, $newFilePath);
            $file = new File();
            $file->name = $fileName;
            $file->model = $ownerClass;
            $file->item_id = $ownerId;
            $file->hash = $fileHash;
            $file->size = filesize($cleanFilePath);
            $file->type = $fileType;
            $file->mime = FileHelper::getMimeType($cleanFilePath);
            $file->attribute = $attribute;
            if (isset(\Yii::$app->user)){
                $file->created_by = \Yii::$app->user->id;
            }
            if (!empty($tableNameOwner)) {
                $file->table_name_form = $tableNameOwner;
            }
            if ($encrypt) {
                $file->encrypted = File::IS_ENCRYPTED;
            }

            if(!empty($originalAttachFileId)){
                $file->original_attach_file_id = $originalAttachFileId;
            }

            /** @var ActiveQuery $query */
            $query = File::find()
                    ->andWhere(['model' => $ownerClass])
                    ->andWhere(['attribute' => $attribute])
                    ->andWhere(['item_id' => $ownerId]);

            $maxSort = $query->max('sort');
            $file->sort = $maxSort + 1;

            if ($file->save()) {
                if ($dropOriginFile) {
                    unlink($cleanFilePath);
                }

                try {
                    if (!is_null($this->statistics)) {
                        $stat = \Yii::createObject($this->statistics);
                        $ok = $stat->save($file);
                        if (!$ok) {
                            \Yii::getLogger()->log(FileModule::t('amosattachments', 'Statistics: error while saving'),
                                    Logger::LEVEL_WARNING);
                        }
                    }
                } catch (\Exception $exception) {
                    \Yii::getLogger()->log($exception->getMessage(), Logger::LEVEL_ERROR);
                }

                return $file;
            } else {
                if (count($file->getErrors()) > 0) {
                    $errors = $file->getErrors();
                    $ar = array_shift($errors);

                    if ($dropOriginFile) {
                        unlink($newFilePath);
                    }

                    if ($session = \Yii::$app->has('session')) {
                        \Yii::$app->session->addFlash('error', array_shift($ar));
                    }
                }

                return false;
            }
        } else {
            if ($dropOriginFile) {
                unlink($cleanFilePath);
            }
            if ($encrypt) {
                $exists->encrypted = File::IS_ENCRYPTED;
                $exists->save(false);
            }
            return $exists;
        }
    }

    /**
     * @param $fileHash
     * @param $useStorePath
     * @return string
     */
    public function getFilesDirPath($fileHash, $isPrivateFile = true) {
        $path = $this->getStorePath() . DIRECTORY_SEPARATOR . $this->getSubDirs($fileHash);

        if (!$isPrivateFile && AttachemntsHelper::getIsStaticServed()) {
            $path = \Yii::getAlias('@webroot/static') . DIRECTORY_SEPARATOR . $this->getSubDirs($fileHash);
        }

        FileHelper::createDirectory($path, 0777);

        return $path;
    }

    /**
     * @return bool|string
     */
    public function getStorePath() {
        return \Yii::getAlias($this->storePath);
    }

    /**
     * @param $fileHash
     * @param int $depth
     * @return string
     */
    public function getSubDirs($fileHash, $depth = 3) {
        $depth = min($depth, 9);
        $path = '';

        for ($i = 0; $i < $depth; $i++) {
            $folder = substr($fileHash, $i * 3, 2);
            $path .= $folder;
            if ($i != $depth - 1) {
                $path .= DIRECTORY_SEPARATOR;
            }
        }

        return $path;
    }

    /**
     * @param \yii\base\BaseObject $obj
     * @return string
     */
    public function getClass($obj) {
        $className = $obj::className();

        return $className;
    }

    /**
     * @param $id
     */
    public function detachFile($id) {
        /** @var File $file */
        $file = File::findOne(['id' => $id]);
        if (!is_null($file)) {
            $anyMore = File::findAll(['hash' => $file->hash]);
            $filePath = $this->getFilesDirPath($file->hash)
                    . DIRECTORY_SEPARATOR
                    . $file->hash
                    . '.'
                    . $file->type;
            if (file_exists($filePath) && count($anyMore) == 1) {
                unlink($filePath);
            }

            try {
                if (!is_null($this->statistics)) {
                    $stat = \Yii::createObject($this->statistics);
                    /** @var \amos\statistics\models\AttachmentsStatsInterface $stat */
                    $ok = $stat->delete($file);
                    if (!$ok) {
                        \Yii::getLogger()->log(
                                FileModule::t('amosattachments', 'Statistics: error while saving'),
                                Logger::LEVEL_WARNING
                        );
                    }
                }
            } catch (\Exception $exception) {
                \Yii::getLogger()->log($exception->getMessage(), Logger::LEVEL_ERROR);
            }

            $ok = $file->delete();

            if ($ok && $this->enable_aws_s3) {
                $allRefs = $file->attachFileRefsMany;
                foreach ((array) $allRefs as $v) {
                    if (!empty($v->s3_url)) {
                        utilities\AwsUtility::deleteFileFromS3($v->s3_url);
                    }
                }
            }
            return $ok;
        }
    }

    /**
     * @param File $file
     * @return string
     */
    public function getWebPath(File $file) {
        $fileName = $file->hash
                . '.'
                . $file->type;

        $webPath = '/'
                . $this->webDir
                . '/'
                . $this->getSubDirs($file->hash)
                . '/'
                . $fileName;

        return $webPath;
    }

    /**
     *
     * @return type
     */
    public function getDefaultModels() {
        return File::class;
    }

    /**
     * @return array
     */
    public function getIndexActions() {
        return [
            'attach-gallery/index',
            'attach-gallery/single-gallery',
            'attach-databank-file/index',
            'shutterstock/index',
        ];
    }

    /**
     * @return array
     */
    public function defaultControllerIndexRoute() {
        return [
            'attach-gallery' => '/attachments/attach-gallery/index',
            'attach-gallery-image' => '/attachments/attach-gallery/index',
            'attach-databank-file' => '/attachments/attach-databank-file/index',
        ];
    }

    /**
     * @return array
     */
    public function defaultControllerIndexRouteSlogged() {
        return [
            'attach-gallery' => '/attachments/attach-gallery/index',
            'attach-gallery-image' => '/attachments/attach-gallery/index',
            'attach-databank-file' => '/attachments/attach-databank-file/index',
        ];
    }

    /**
     * @inheritdoc
     *
     * public static function getModuleIconName()
     * {
     * return 'feed';
     * } */
    public function getControllerNames() {
        $names = [
            'attach-gallery' => self::t('amosattachments', "Galleria"),
            'attach-gallery-image' => self::t('amosattachments', "Galleria"),
            'attach-databank-file' => self::t('amosattachments', "Asset Allegati"),
            'shutterstock' => self::t('amosattachments', "Shutterstock"),

        ];

        return $names;
    }

}
