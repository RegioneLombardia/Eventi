<?php
/**
 */

namespace yii\web;

use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * UploadedFile represents the information for an uploaded file.
 *
 * You can call [[getInstance()]] to retrieve the instance of an uploaded file,
 * and then use [[saveAs()]] to save it on the server.
 * You may also query other information about the file, including [[name]],
 * [[tempName]], [[type]], [[size]] and [[error]].
 *
 * For more details and usage information on UploadedFile, see the [guide article on handling uploads](guide:input-file-upload).
 *
 * @property-read string $baseName Original file base name. This property is read-only.
 * @property-read string $extension File extension. This property is read-only.
 * @property-read bool $hasError Whether there is an error with the uploaded file. Check [[error]] for
 * detailed error code information. This property is read-only.
 *
 * @since 2.0
 */
class UploadedFile extends BaseObject
{
    /**
     * @var string the original name of the file being uploaded
     */
    public $name;
    /**
     * @var string the path of the uploaded file on the server.
     * Note, this is a temporary file which will be automatically deleted by PHP
     * after the current request is processed.
     */
    public $tempName;
    /**
     * @var string the MIME-type of the uploaded file (such as "image/gif").
     * Since this MIME type is not checked on the server-side, do not take this value for granted.
     * Instead, use [[\yii\helpers\FileHelper::getMimeType()]] to determine the exact MIME type.
     */
    public $type;
    /**
     * @var int the actual size of the uploaded file in bytes
     */
    public $size;
    /**
     * @var int an error code describing the status of this file uploading.
     */
    public $error;

    /**
     * @var resource a temporary uploaded stream resource used within PUT and PATCH request.
     */
    private $_tempResource;
    private static $_files;


    /**
     * UploadedFile constructor.
     *
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct($config = [])
    {
        $this->_tempResource = ArrayHelper::remove($config, 'tempResource');
        parent::__construct($config);
    }

    /**
     * String output.
     * This is PHP magic method that returns string representation of an object.
     * The implementation here returns the uploaded file's name.
     * @return string the string representation of the object
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Returns an uploaded file for the given model attribute.
     * The file should be uploaded using [[\yii\widgets\ActiveField::fileInput()]].
     * @param \yii\base\Model $model the data model
     * @param string $attribute the attribute name. The attribute name may contain array indexes.
     * For example, '[1]file' for tabular file uploading; and 'file[1]' for an element in a file array.
     * @return null|UploadedFile the instance of the uploaded file.
     * Null is returned if no file is uploaded for the specified model attribute.
     */
    public static function getInstance($model, $attribute)
    {
        $name = Html::getInputName($model, $attribute);
        return static::getInstanceByName($name);
    }

    /**
     * Returns all uploaded files for the given model attribute.
     * @param \yii\base\Model $model the data model
     * @param string $attribute the attribute name. The attribute name may contain array indexes
     * for tabular file uploading, e.g. '[1]file'.
     * @return UploadedFile[] array of UploadedFile objects.
     * Empty array is returned if no available file was found for the given attribute.
     */
    public static function getInstances($model, $attribute)
    {
        $name = Html::getInputName($model, $attribute);
        return static::getInstancesByName($name);
    }

    /**
     * Returns an uploaded file according to the given file input name.
     * The name can be a plain string or a string like an array element (e.g. 'Post[imageFile]', or 'Post[0][imageFile]').
     * @param string $name the name of the file input field.
     * @return null|UploadedFile the instance of the uploaded file.
     * Null is returned if no file is uploaded for the specified name.
     */
    public static function getInstanceByName($name)
    {
        $files = self::loadFiles();
        return isset($files[$name]) ? new static($files[$name]) : null;
    }

    /**
     * Returns an array of uploaded files corresponding to the specified file input name.
     * This is mainly used when multiple files were uploaded and saved as 'files[0]', 'files[1]',
     * 'files[n]'..., and you can retrieve them all by passing 'files' as the name.
     * @param string $name the name of the array of files
     * @return UploadedFile[] the array of UploadedFile objects. Empty array is returned
     * if no adequate upload was found. Please note that this array will contain
     * all files from all sub-arrays regardless how deeply nested they are.
     */
    public static function getInstancesByName($name)
    {
        $files = self::loadFiles();
        if (isset($files[$name])) {
            return [new static($files[$name])];
        }
        $results = [];
        foreach ($files as $key => $file) {
            if (strpos($key, "{$name}[") === 0) {
                $results[] = new static($file);
            }
        }

        return $results;
    }

    /**
     * Cleans up the loaded UploadedFile instances.
     * This method is mainly used by test scripts to set up a fixture.
     */
    public static function reset()
    {
        self::$_files = null;
    }

    /**
     * Saves the uploaded file.
     * If the target file `$file` already exists, it will be overwritten.
     * @param string $file the file path or a path alias used to save the uploaded file.
     * @param bool $deleteTempFile whether to delete the temporary file after saving.
     * If true, you will not be able to save the uploaded file again in the current request.
     * @return bool true whether the file is saved successfully
     */
    public function saveAs($file, $deleteTempFile = true)
    {
        if ($this->hasError) {
            return false;
        }

        $targetFile = Yii::getAlias($file);
        if (is_resource($this->_tempResource)) {
            $result = $this->copyTempFile($targetFile);
            return $deleteTempFile ? @fclose($this->_tempResource) : (bool) $result;
        }

        return $deleteTempFile ? move_uploaded_file($this->tempName, $targetFile) : copy($this->tempName, $targetFile);
    }

    /**
     * Copy temporary file into file specified
     *
     * @param string $targetFile path of the file to copy to
     * @return bool|int the total count of bytes copied, or false on failure
     * @since 2.0.32
     */
    protected function copyTempFile($targetFile)
    {
        $target = fopen($targetFile, 'wb');
        if ($target === false) {
            return false;
        }

        $result = stream_copy_to_stream($this->_tempResource, $target);
        @fclose($target);

        return $result;
    }

    /**
     * @return string original file base name
     */
    public function getBaseName()
    {
        // https://github.com/yiisoft/yii2/issues/11012
        $pathInfo = pathinfo('_' . $this->name, PATHINFO_FILENAME);
        return mb_substr($pathInfo, 1, mb_strlen($pathInfo, '8bit'), '8bit');
    }

    /**
     * @return string file extension
     */
    public function getExtension()
    {
        return strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
    }

    /**
     * @return bool whether there is an error with the uploaded file.
     * Check [[error]] for detailed error code information.
     */
    public function getHasError()
    {
        return $this->error != UPLOAD_ERR_OK;
    }

    /**
     * Creates UploadedFile instances from $_FILE.
     * @return array the UploadedFile instances
     */
    private static function loadFiles()
    {
        if (self::$_files === null) {
            self::$_files = [];
            if (isset($_FILES) && is_array($_FILES)) {
                foreach ($_FILES as $class => $info) {
                    $resource = isset($info['tmp_resource']) ? $info['tmp_resource'] : [];
                    self::loadFilesRecursive($class, $info['name'], $info['tmp_name'], $info['type'], $info['size'], $info['error'], $resource);
                }
            }
        }

        return self::$_files;
    }

    /**
     * Creates UploadedFile instances from $_FILE recursively.
     * @param string $key key for identifying uploaded file: class name and sub-array indexes
     * @param mixed $names file names provided by PHP
     * @param mixed $tempNames temporary file names provided by PHP
     * @param mixed $types file types provided by PHP
     * @param mixed $sizes file sizes provided by PHP
     * @param mixed $errors uploading issues provided by PHP
     */
    private static function loadFilesRecursive($key, $names, $tempNames, $types, $sizes, $errors, $tempResources)
    {
        if (is_array($names)) {
            foreach ($names as $i => $name) {
                $resource = isset($tempResources[$i]) ? $tempResources[$i] : [];
                self::loadFilesRecursive($key . '[' . $i . ']', $name, $tempNames[$i], $types[$i], $sizes[$i], $errors[$i], $resource);
            }
        } elseif ((int) $errors !== UPLOAD_ERR_NO_FILE) {
            self::$_files[$key] = [
                'name' => $names,
                'tempName' => $tempNames,
                'tempResource' => $tempResources,
                'type' => $types,
                'size' => $sizes,
                'error' => $errors,
            ];
        }
    }
}
