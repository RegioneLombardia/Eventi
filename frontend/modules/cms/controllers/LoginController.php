<?php

namespace app\modules\cms\controllers;

use backend\modules\eventsadmin\utility\EventsAdminUtility;
use open20\amos\admin\models\UserProfile;
use open20\amos\core\validators\PIVAValidator;
use open20\amos\mobile\bridge\modules\v1\models\AccessTokens;
use open20\amos\mobile\bridge\modules\v1\models\User as AmosUser;
use luya\admin\controllers\LoginController as BaseLoginController;
use luya\admin\models\User;
use luya\admin\models\UserLogin;
use luya\admin\models\UserOnline;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\validators\IpValidator;

class LoginController extends BaseLoginController
{

    public function actionIndex()
    {
//        if (YII_ENV == YII_ENV_PROD) {
//            $isPermitted = EventsAdminUtility::isAccessPermitted();
//            if (!$isPermitted) {
//                return $this->redirect('/');
//            }
//        }

        return parent::actionIndex();
    }

    /**
     *
     * @return array
     */
    public function getRules()
    {
        return
            ArrayHelper::merge(parent::getRules(),
                [
                    [
                        'allow' => true,
                        'actions' => ['login-amos'],
                        'roles' => ['?', '@'],
                    ],
                ]);
    }

    /**
     *
     * @param type $secure_token
     * @return type
     */
    public function actionLoginAmos($secure_token = null)
    {

        if ($secure_token) {
            $amosuser = $this->findIdentityByAccessToken($secure_token);
            if (!is_null($amosuser)) {
                $user = $this->getCmsUser($amosuser);
                if (!is_null($user)) {
                    Yii::$app->adminuser->idParam = '__luyaAdmin_id';
                    if ($user && Yii::$app->adminuser->login($user)) {

                        $user->updateAttributes([
                            'force_reload' => false,
                            'login_attempt' => 0,
                            'login_attempt_lock_expiration' => null,
                            'auth_token' => Yii::$app->security->hashData(Yii::$app->security->generateRandomString(),
                                $user->password_salt),
                        ]);
                        // kill prev user logins
                        UserLogin::updateAll(['is_destroyed' => true],
                            ['user_id' => $user->id]);

                        // create new user login
                        $login = new UserLogin([
                            'auth_token' => $user->auth_token,
                            'user_id' => $user->id,
                            'is_destroyed' => false,
                        ]);
                        $login->save();

                        // refresh user online list
                        UserOnline::refreshUser($user, 'login');
                    }
                }
            }
        }
        echo 'jCallback' . '(' . "{'fullname' : '" . Yii::$app->adminuser->idParam . "','user_id' : '" . $user->id . "' }" . ')';
        return;
    }


    /**
     *
     * @param type $token
     * @return type
     */
    protected function findIdentityByAccessToken($token)
    {
        $Token = AccessTokens::findOne(['access_token' => $token]);

        if ($Token) {
            return $Token->user;
        }

        return null;
    }

    /**
     *
     * @param  $amosuser
     */
    protected function getCmsUser(AmosUser $amosuser)
    {


        /* var $command yii\db\Command */
        /*	        $myfile = fopen("newfile_2.txt", "w") or die("Unable to open file!");

            try{

           // $res = \Yii::$app->db->createCommand('Select * from admin_group where id = ' . $luyaAmdinModule->defaultCmsGroup)->queryAll();
             $txt =  \Yii::$app->params['defaultCmsGroup']." ciao \n";
             fwrite($myfile, $txt);

             $txt =  " -- ciao \n";
            fwrite($myfile, $txt);

            }catch( \yii\base\Exception $e){
                 $txt =  " -- ciao \n";
            fwrite($myfile, $e->getMessage());
            }
            fclose($myfile);

            die;*/


        $user = User::findOne(['email' => $amosuser->email]);

        if (is_null($user)) {
            /* @var $userProfile UserProfile */
            $userProfile = $amosuser->userProfile;
            $user = new User();
            $user->firstname = $userProfile->nome;
            $user->lastname = $userProfile->cognome;
            $user->email = $amosuser->email;
            $salt = \Yii::$app->security->generateRandomString();
            $pw = \Yii::$app->security->generatePasswordHash('' . $salt);
            $user->password = $pw;
            $user->password_salt = $salt;
            $user->title = 1;
            $user->is_deleted = false;
            $user->save();


            /* var $command yii\db\Command */
            $command = \Yii::$app->db->createCommand();
            $defaultCmsGroup = \Yii::$app->params['defaultCmsGroup'];
            $group_id = 1;

            if (\Yii::$app->user->can('ASSISTENZA_EVENTI')) {
                $group_id = 3;
            } else if ($defaultCmsGroup) {
                $res = \Yii::$app->db->createCommand('Select * from admin_group where id = ' . $defaultCmsGroup)->queryAll();
                if (!empty($res)) {
                    $group_id = $defaultCmsGroup;
                }
            }


            $command->insert('{{%admin_user_group}}',
                [
                    'user_id' => $user->id,
                    'group_id' => $group_id,
                ])->execute();
        }
        return $user;
    }
}