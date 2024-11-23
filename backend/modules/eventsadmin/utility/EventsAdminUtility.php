<?php

namespace backend\modules\eventsadmin\utility;

use open20\amos\events\AmosEvents;
use open20\amos\socialauth\controllers\SocialAuthController;
use open20\amos\socialauth\models\SocialAuthUsers;
use yii\base\Exception;
use yii\validators\IpValidator;

class EventsAdminUtility
{

    /**
     *
     */
    public static function savePreferencesTags($model, $preferencesTags)
    {
        $root = \open20\amos\tag\models\Tag::find()->andWhere(['codice' => \open20\amos\events\models\Event::ROOT_TAG_PREFERENCE_CENTER])->one();
        if ($root) {
            \open20\amos\cwh\models\CwhTagOwnerInterestMm::deleteAll([
                'root_id' => $root->id,
                'record_id' => $model->id,
                'classname' => \open20\amos\admin\models\UserProfile::className()
            ]);
            foreach ($preferencesTags as $tagId) {
                $tagsMm = new \open20\amos\cwh\models\CwhTagOwnerInterestMm();
                $tagsMm->tag_id = $tagId;
                $tagsMm->record_id = $model->id;
                $tagsMm->interest_classname = 'simple-choice';
                $tagsMm->root_id = $root->id;
                $tagsMm->classname = \open20\amos\admin\models\UserProfile::className();
                $tagsMm->save(false);
            }
        }

    }

    /**
     * @return string
     */
    public static function getLabelPrivacy2()
    {
        $link = \Yii::$app->params['platform']['frontendUrl']."/it/privacy";
        return
           \Yii::t('site', "Presa visione dell'<a target='_blank' href='{link}'>informativa al trattamento di dati personali</a><br>",[
               'link' => $link
           ]).
           \Yii::t('site', "esprimo il consenso al trattamento dei miei dati personali per la finalità indicata alla lettera b) dell’informativa - comunicazioni relative agli eventi");
    }

    /**
     * @return string
     */
    public static function getLabelPrivacy1()
    {
        $link = \Yii::$app->params['platform']['frontendUrl']."/it/privacy";
        return
            \Yii::t('site', "Presa visione dell'informativa al trattamento di dati personali di cui al presente <a target='_blank' href='{link}'>link:</a><br>",[
                'link' => $link
            ]).
            \Yii::t('site', "esprimo il consenso al trattamento dei miei dati personali per la finalità indicata alla lettera a) dell’informativa - registrazione alla piattaforma Eventi *");
    }


    /**
     * @param string $provider
     * @return \Hybrid_User_Profile|\yii\web\Response
     */
    public static function getUserSocial($provider = 'facebook')
    {
        \Yii::$app->controller->redirect(['/socialauth/social-auth/endpoint','provider' => $provider, 'action' => '', 'backTo' => \Yii::$app->params['platform']['backendUrl'].'/admin/security/register?social-done='.$provider]);
        \Yii::$app->response->send();
        die;
        /**
         * @var $adapter \Hybrid_Provider_Adapter
         */
//        $module = \Yii::$app->getModule('socialauth');
//
//        $socialAuthController = new SocialAuthController('social-auth', $module);
//
//        $adapter = $socialAuthController->authProcedure($provider, \Yii::$app->params['platform']['backendUrl']);
//
////        /**
//         * If the adapter is not set go back to home
//         */
//        if (!$adapter) {
////            return $this->goHome();
//            return \Yii::$app->controller->goBack();
//        }
//
//        /**
//         * @var $userProfile \Hybrid_User_Profile
//         */
//        $userProfile = $adapter->getUserProfile();
//
//        /**
//         * Kick off social user
//         */
//        $adapter->logout();
//
//        /**
//         * @var $socialUser SocialAuthUsers
//         */
//        $socialUser = SocialAuthUsers::findOne(['identifier' => $userProfile->identifier,
//            'provider' => $provider]);
//
//        /**
//         * If the social user exists
//         */
//        if ($socialUser) {
//            $user = new \Hybrid_User_Profile();
//            $profile = $socialUser->user->userProfile;
//            $user->firstName = $profile->nome;
//            $user->lastName = $profile->cognome;
//            $user->email = $socialUser->user->email;
//            return $user;
//        } else {
//            return $userProfile;
//        }
    }

    /**
     * @param $userProfile
     * @param \Hybridauth\User\Profile $socialProfile
     * @param $provider
     * @return bool|SocialAuthUsers
     */
    public static function createSocialUser($userProfile,  $socialProfile, $provider){
            try {
                /**
                 * @var $socialUser SocialAuthUsers
                 */
                $socialUser = new SocialAuthUsers();

                /**
                 * @var $socialProfileArray array User profile from provider
                 */
                $socialProfileArray = (array)$socialProfile;
                $socialProfileArray['provider'] = $provider;
                $socialProfileArray['user_id'] = $userProfile->user_id;

                /**
                 * If all data can be loaded to new record
                 */
                if ($socialUser->load(['SocialAuthUsers' => $socialProfileArray])) {
                    /**
                     * Is valid social user
                     */
                    if ($socialUser->validate()) {
                        $socialUser->save();
                        return $socialUser;
                    } else {
                        \Yii::$app->session->addFlash('danger',
                            \Yii::t('amossocialauth',
                                'Unable to Link The Social Profile'));
                        return false;
                    }
                } else {
                    \Yii::$app->session->addFlash('danger',
                        \Yii::t('amossocialauth',
                            'Invalid Social Profile, Try again'));
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }

            return false;
    }

    /**
     * @param $array
     * @return  \Hybridauth\User\Profile
     */
    public static function getClassHybridUserProfile($array)
    {
        $socialProfile = new \Hybridauth\User\Profile();
        foreach ($array as $key => $value) {
            $socialProfile->{$key} = $value;
        }
        return $socialProfile;
    }


    /**
     * @return bool
     */
    public static function isAccessPermitted()
    {
        $ip = null;
        $allowed1  = false;
        $allowed2  = false;
        if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip1 =  $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        if(!empty($_SERVER['REMOTE_ADDR'])){
            $ip2 =  $_SERVER['REMOTE_ADDR'];
        }


        if(!empty($ip1)){
            $allowedIPs = [];
            $module = \Yii::$app->getModule('admin');
            if($module){
                $allowedIPs = \Yii::$app->params['loginAllowedIPs'];
            }
            $ipValidator = new IpValidator(['ranges' => $allowedIPs]);
            $allowed1 = $ipValidator->validate($ip1);
        }

        if(!empty($ip2)){
            $allowedIPs = [];
            $module = \Yii::$app->getModule('admin');
            if($module){
                $allowedIPs = \Yii::$app->params['loginAllowedIPs'];
            }
            $ipValidator = new IpValidator(['ranges' => $allowedIPs]);
            $allowed2 = $ipValidator->validate($ip2);
        }

        return ($allowed1 || $allowed2);
    }

    /**
     * @param $parms
     * @return \open20\amos\events\models\Event|null
     */
    public static function getModelEventFormLuya($parms){
        $queryParams = [];
        $model = null;
        if(!empty($parms['conditionSearch'])){
            $commands = explode(";", $parms["conditionSearch"]);
            foreach ($commands as $command) {
                $queryParams = \yii\helpers\ArrayHelper::merge([],
                    eval("return " . $command . ";"));
            }
            $model = \open20\amos\events\models\Event::findOne(['id' => $queryParams['event_id']]);
        }
        return $model;
    }

    /**
     * @param $alias
     * @return string
     */
    public static function getUrlUpdateEventFromAlias($alias){
        $landing = \open20\amos\events\models\EventLanding::find()->andWhere(['url' => $alias ])->one();
        if($landing){
            return \Yii::$app->params['platform']['backendUrl'].'/events/event-dashboard/view?id='.$landing->event_id;
        }
        return '';
    }


}