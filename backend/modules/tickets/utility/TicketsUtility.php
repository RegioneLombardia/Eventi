<?php
namespace backend\modules\tickets\utility;

use open20\amos\core\utilities\Email;
use yii\log\Logger;

class TicketsUtility
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
//            pr($from, $to);
//            pr($subject, 'sub');
//            pr($message, 'mess');
            $ok = $email->sendMail($from, $to, $subject, $message, $files);
        } catch (\Exception $ex) {
            \Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $ok;
    }


}