<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 20/10/2020
 * Time: 14:51
 */

namespace open20\amos\groups\utility;


use open20\amos\core\utilities\Email;
use yii\log\Logger;

class GroupsMailUtility
{


    /**
     * @param $to
     * @param $profile
     * @param $subject
     * @param $message
     * @param array $files
     * @return bool
     */
    public static function sendEmailGeneral($from, $to, $subject, $message, $files = [])
    {
        $ok = false;
        try {
            /** @var \open20\amos\core\utilities\Email $email */
            $email = new Email();
            $ok = $email->sendMail($from, $to, $subject, $message, $files);
        } catch (\Exception $ex) {
            \Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $ok;
    }
}