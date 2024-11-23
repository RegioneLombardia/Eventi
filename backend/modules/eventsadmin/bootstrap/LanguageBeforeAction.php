<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\bootstrap
 * @category   CategoryName
 */

namespace backend\modules\eventsadmin\bootstrap;

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\components\RedirectBeforeActionComponent;
use open20\amos\admin\models\UserProfile;
use open20\amos\admin\utility\UserProfileUtility;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\rest\Controller;

/**
 * Class RedirectBeforeAction
 * @package open20\amos\admin\bootstrap
 */
class LanguageBeforeAction implements BootstrapInterface
{
    /**
     * @param $app
     */
    public function bootstrap($app)
    {
        Event::on(\yii\base\Controller::className(), \yii\base\Controller::EVENT_BEFORE_ACTION, [$this, 'setLanguage']);
    }

    /**
     * @param $event
     */
    public function setLanguage($event)
    {
        if (!(Yii::$app->controller instanceof Controller)) {
            //SETTO LA LINGUA SE Cè PARAMETRO IN URL ( vale in particolare per plugin eventi )
            if (\Yii::$app->request->get('language')) {
                \Yii::$app->language = \Yii::$app->request->get('language');
                Yii::$app->params['use_only_translation_lang'] = 'it-IT';
            }

            //------------------ TRADUZIONE COMMUNITY
            // setto in sessione il prametro della lingua se sono su /events/event-dashboard/join-community?id=ID&language=en-GB o in /event-dashboard/join
            if (\Yii::$app->controller->module->id == 'events' && (\Yii::$app->controller->action->id == 'join-community' || \Yii::$app->controller->action->id == 'join')) {
                if(\Yii::$app->request->get('language') == 'en-GB') {
                    \Yii::$app->session->set('languageEventCommunity', [
                        'event_id' => \Yii::$app->request->get('id'),
                        'language' => 'en-GB'
                    ]);
                } else{
                    \Yii::$app->session->remove('languageEventCommunity');
                }
            }

            //se sono nel plugin eventi ma non nelle action di join-community e join tolgo il parametro della traduzione in sessione
            if (\Yii::$app->controller->module->id == 'events' && (\Yii::$app->controller->action->id != 'join-community' && \Yii::$app->controller->action->id != 'join')) {
                \Yii::$app->session->remove('languageEventCommunity');
            }
            // se sono in dashboard tolgo il parametro della lingua in sessione
            if(\Yii::$app->controller->module->id == 'dashboard' ){
                \Yii::$app->session->remove('languageEventCommunity');
            }

            // se c'è il parametro in sessione setto la lingua inglese
            if(!empty( \Yii::$app->session->get('languageEventCommunity'))){
                $langSession = \Yii::$app->session->get('languageEventCommunity');
                if(!empty($langSession['language']) && $langSession['language'] == 'en-GB' ){
                    \Yii::$app->language = 'en-GB';
                }
            }




        }
    }

}
