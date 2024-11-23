<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 03/12/2020
 * Time: 09:29
 */

namespace backend\modules\eventsadmin\utility;


use open20\amos\core\utilities\Email;
use yii\log\Logger;

class EventsAdminEmailUtility
{

    /**
     * @param $user
     * @return bool
     */
    public static function sendEmailFirstAccessPassword($user){
        $from = \Yii::$app->params['email-assistenza'];
        $module = \Yii::$app->getModule('events');
        $linkUpdateProfile = \Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/update-my-profile';
        $linkApp = null;
        if($module){
            $linkApp = $module->url_download_app_mobile;
        }
        $email = $user->email;
        $subject = \Yii::t('app',"Ciao {name}, ti diamo il benvenuto nella piattaforma!",[
            'name' => $user->userProfile->nome
        ]);
        \Yii::$app->controller->view->params['footerType'] = \open20\amos\events\models\EventEmailTemplates::FOOTER_TYPE_TAG_AND_UNSUBSCRIBE;
        $text = \Yii::t('app',"Gentile <strong>{nome}</strong> <strong>{cognome}</strong>,
        <br>ti diamo il benvenuto nella piattaforma Eventi Lombardia: la tua registrazione è avvenuta con successo. 
        <br><br>Potrai <a href='{link_profilo}'>modificare le preferenze del tuo profilo utente</a> in ogni momento, oppure <a href='{link_profilo_notify}'>non ricevere più mail come questa</a>.
        ",[
            'linkdownload' => $linkApp,
            'link_profilo' => $linkUpdateProfile,
            'link_profilo_notify' => $linkUpdateProfile.'?page=notify',
            'nome' => $user->userProfile->nome,
            'cognome' => $user->userProfile->cognome,
        ]);
        $textApp = "<br><br>Ti ricordiamo che puoi ricevere tutti gli aggiornamenti sugli eventi organizzati da Regione Lombardia anche tramite app: <a href='{linkdownload}'>scarica Eventi Lombardia</a> e non perderti la prossima iniziativa!";
        return self::sendEmailGeneral($from, $email, $subject, $text );
    }


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
        try {

            /** @var \open20\amos\core\utilities\Email $email */
            $email = new Email();
            $email->sendMail($from, $to, $subject, $message, $files);
        } catch (\Exception $ex) {
            \Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return true;
    }
}