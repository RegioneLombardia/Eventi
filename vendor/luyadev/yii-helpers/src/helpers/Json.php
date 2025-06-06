<?php

namespace luya\yii\helpers;

use yii\helpers\BaseJson;

/**
 * Json Helper.
 *
 * Extends the {{yii\helpers\Json}} class by some usefull functions like:
 *
 * + {{luya\yii\helpers\Json::isJson()}}
 *
 * @since 1.0.0
 */
class Json extends BaseJson
{
    /**
     * Checks if a string is a json or not.
     *
     * Example values which return true:
     *
     * ```php
     * Json::isJson('{"123":"456"}');
     * Json::isJson('{"123":456}');
     * Json::isJson('[{"123":"456"}]');
     * Json::isJson('[{"123":"456"}]');
     * ```
     *
     * @param mixed $value The value to test if its a json or not.
     * @return boolean Whether the string is a json or not.
     */
    public static function isJson($value)
    {
        if (!is_scalar($value)) {
            return false;
        }

        $firstChar = substr($value, 0, 1);

        if ($firstChar !== '{' && $firstChar !== '[') {
            return false;
        }

        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Decode Json without Exception
     *
     * Since Json::decode('foo') would throw an exception, this method will return a default value
     * defined instead of an exception.
     *
     * @param string $json
     * @param boolean $asArray
     * @param mixed $defaultValue
     * @return mixed
     * @since 1.4.0
     */
    public static function decodeSilent($json, $asArray = true, $defaultValue = null)
    {
        try {
            return self::decode($json, $asArray);
        } catch (\Exception $e) {
            return $defaultValue;
        }
    }
}
