<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 29/05/2019
 * Time: 16:24
 */

namespace open20\amos\sondaggi\utility;

use open20\amos\core\utilities\Email;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\models\SondaggiRisposteSessioni;
use yii\log\Logger;

class SondaggiUtility
{

    /**
     * @param $to
     * @param $profile
     * @param $subject
     * @param $message
     * @param array $files
     * @return bool
     */
    public static function sendEmailGeneral($to, $profile, $subject, $message, $files = [], $from = null)
    {
        try {
            if (empty($from)) {
                $from = '';
                if (isset(\Yii::$app->params['email-assistenza'])) {
                    //use default platform email assistance
                    $from = \Yii::$app->params['email-assistenza'];
                }
            }

            /** @var \open20\amos\core\utilities\Email $email */
            $email = new Email();
            $email->sendMail($from, $to, $subject, $message, $files);
        } catch (\Exception $ex) {
            pr($ex->getMessage());
            \Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return true;
    }

    /**
     * @param $model Sondaggi
     * @param int $request_info
     * @param string $path
     */
    public static function sendEmailSondaggioCompilato($model, $idSessione, $path = null, $utente = null,
                                                       $dati_utente = null)
    {
        $risposteSessioni = SondaggiRisposteSessioni::findOne($idSessione);
        $userDefault      = null;
        $users            = [];
        if (!empty($risposteSessioni->user)) {
            $users[] = $risposteSessioni->user;
        }

        $additionalEmails = [];
        if (!empty($model->additional_emails)) {
            $additionalEmails = explode(';', $model->additional_emails);
        }

        $compilatore = !empty($users[0]) ? $users[0] : null;

        if (!empty($compilatore)) {
            $message = "<p>".AmosSondaggi::t('amossondaggi',
                    'Grazie per aver compilato il sondaggio <strong>{titolo}</strong>, in allegato trovi il sondaggio compilato.',
                    ['titolo' => $model->titolo])."</p>";
            $subject = AmosSondaggi::t("amossondaggi", "{nomeCognome} ha compilato il sondaggio '{titolo}'",
                    ['titolo' => $model->titolo, 'nomeCognome' => !empty($compilatore) ? $compilatore->userProfile->nomeCognome
                        : '']);
        } else if (!empty($dati_utente)) {
            $message = "<p>".AmosSondaggi::t('amossondaggi',
                    'Grazie per aver compilato il sondaggio <strong>{titolo}</strong>, in allegato trovi il sondaggio compilato.',
                    ['titolo' => $model->titolo])."</p>";
            $subject = AmosSondaggi::t("amossondaggi", "{nomeCognome} ha compilato il sondaggio '{titolo}'",
                    ['titolo' => $model->titolo, 'nomeCognome' => !empty($dati_utente['nome']) ? ($dati_utente['nome'].' '.$dati_utente['cognome'])
                        : AmosSondaggi::t('amossondaggi', 'Utente')]);
        }
        if (empty($path)) {
            $files = [];
        } else {
            $files = [$path];
        }

        foreach ($users as $user) {
            if (!in_array($user->email, $additionalEmails)) {
                SondaggiUtility::sendEmailGeneral([$user->email], null, $subject, $message, $files);
            }
        }

        if (!empty($dati_utente) && !empty($dati_utente['email'])) {
            if (!in_array($dati_utente['email'], $additionalEmails)) {
                SondaggiUtility::sendEmailGeneral([$dati_utente['email']], null, $subject, $message, $files);
            }
        }

        foreach ($additionalEmails as $email) {
            if (!empty($email)) {
                SondaggiUtility::sendEmailGeneral([trim($email)], null, $subject, $message, $files);
            }
        }
        $fullPath = \Yii::getAlias('@app/web/' . $path);
        unlink($fullPath);

    }
}