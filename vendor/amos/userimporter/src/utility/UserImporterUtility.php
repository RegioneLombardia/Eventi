<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 11/04/2022
 * Time: 12:30
 */

namespace amos\userimporter\utility;


use amos\userimporter\Module;
use open20\amos\core\user\User;
use yii\helpers\Html;

class UserImporterUtility
{
    /**
     * @param $event
     * @param $user_id
     * @param $text
     * @param bool $setStrong
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public static function parseEmailWithParams($invitation, $user_id, $text, $setStrong = true)
    {
        $url = \Yii::$app->params['platform']['backendUrl'] . "/img/img_default.jpg";
        if ($invitation) {
            if ($user_id) {
                $user = User::findOne($user_id);
            }

            if (is_null($user_id)) {
                $textWithParms = Module::t('amosuserimporter', $text, [
                        'NOME' => self::setStrong('[nome]', $setStrong),
                        'USER_ID' => '[user_id]',
                        'COGNOME' => self::setStrong('[cognome]', $setStrong),
                        'EMAIL' => self::setStrong('[email]', $setStrong),
                        'CODICE_FISCALE' => self::setStrong('[codice_fiscale]', $setStrong),
                        'GENERE' => self::setStrong('[genere]', $setStrong),
                        'ETA' => self::setStrong('[eta]', $setStrong),
                        'PROVINCIA' => self::setStrong('[provincia]', $setStrong),
                        'COMUNE' => self::setStrong('[città]', $setStrong),
                        'AZIENDA' => self::setStrong('[azienda]', $setStrong),
                        'DISACTIVATE_USER_URL' => \Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/disactivate-user',
                    ]
                );
            } else {
                $textWithParms = Module::t('amosuserimporter', $text, [
                        'NOME' => self::setStrong($user->userProfile->nome, $setStrong),
                        'USER_ID' => $user->id,
                        'COGNOME' => self::setStrong($user->userProfile->cognome, $setStrong),
                        'EMAIL' => self::setStrong($user->email, $setStrong),
                        'CODICE_FISCALE' => self::setStrong($user->userProfile->codice_fiscale, $setStrong),
                        'GENERE' => self::setStrong($user->userProfile->sesso, $setStrong),
                        'ETA' => self::setStrong($user->userProfile->userProfileAgeGroup->age_group, $setStrong),
                        'PROVINCIA' => self::setStrong($user->userProfile->nascitaProvince->nome, $setStrong),
                        'COMUNE' => self::setStrong($user->userProfile->nascitaComuni->nome, $setStrong),
                        'AZIENDA' => self::setStrong($user->userProfile->azienda, $setStrong),
                        'DISACTIVATE_USER_URL' => \Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/disactivate-user',
           ]
                );
            }
        } else {
            $textWithParms = Module::t('amosuserimporter', $text, [
                    'NOME' => self::setStrong('[nome]', $setStrong),
                    'COGNOME' => self::setStrong('[cognome]', $setStrong),
                    'EMAIL' => self::setStrong('[email]', $setStrong),
                    'TOKEN' => '[token]',
                ]
            );
        }
        return $textWithParms;
    }

    /**
     * @param $str
     * @param bool $set
     * @return string
     */
    public static function setStrong($str, $set = true)
    {
        if ($set) {
            return "<strong>" . $str . "</strong>";
        }
        return $str;
    }

    /**
     * @param $from
     * @param $to
     * @param $subjectWithParams
     * @param $textWithParms
     * @param null $event
     * @return bool
     */
    public static function sendEmailTest($from, $to, $subjectWithParams, $textWithParms, $invitation = null, $footerType = '')
    {
        $newsletterModule = \Yii::$app->getModule('newsletter');
        $eventModule = \Yii::$app->getModule('events');
        if ($newsletterModule && $eventModule) {
            // GET MAILUP TEMPLATE AND SUBTITUTION OF [CONTENT] WITH PLATFORM PERSONALIZED EMAIL TEXT
            $mailServiceClassname = $newsletterModule->mail_service_driver;
            $mailService = new $mailServiceClassname();
            $mailupListId = $eventModule->mailup_configurations['mailup_list_id'];
            $originalMessageId = $eventModule->mailup_configurations['original_message_id'];
            $emailDecoded = $mailService->getEmail($mailupListId, $originalMessageId);
            $urlImage = \Yii::$app->params['platform']['backendUrl'] . "/img/img_default.jpg";

            $htmlWithParams = self::parseDynamicContentMailup($urlImage, $textWithParms, $emailDecoded, $footerType);

            $htmlWithParams = str_replace('http://[track]/', '', $htmlWithParams);
            $mailModule = \Yii::$app->getModule("email");
            if (isset($mailModule)) {
                $mailModule->defaultLayout = 'layout_without_header_and_footer';
                return $mailModule->send($from, $to, $subjectWithParams, $htmlWithParams);
            }
        }
        return false;
    }

    /**
     * @param $urlImage
     * @param $textWithParms
     * @param $emailDecoded
     * @param string $footerType
     * @return mixed
     */
    public static function parseDynamicContentMailup($urlImage, $textWithParms, $emailDecoded, $footerType = null)
    {
        $htmlWithParams = str_replace('[content_url_image]', $urlImage, $emailDecoded->Content);
        $htmlWithParams = str_replace('[content]', $textWithParms, $htmlWithParams);
        $footerText = '';
        $htmlWithParams = str_replace('[footer]', $footerText, $htmlWithParams);

        return $htmlWithParams;
    }

    /**
     * @return array
     */
    public static function getVariablesEmail()
    {
        $variables = [
            '{NOME}' => '{NOME}',
            '{COGNOME}' => '{COGNOME}',
            '{EMAIL}' => '{EMAIL}',
        ];
        return $variables;
    }
}