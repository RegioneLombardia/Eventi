<?php

namespace backend\modules\eventsadmin\bootstrap;


use backend\modules\eventsadmin\utility\EventsAdminEmailUtility;
use open20\amos\admin\models\UserProfile;
use open20\amos\news\models\News;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\rest\Controller;
use yii\web\User;

class AfterLogin extends Event implements BootstrapInterface
{

    /**
     * @param $app
     */
    public function bootstrap($app)
    {
        Event::on(User::className(), User::EVENT_AFTER_LOGIN, [$this, 'sendEmailFirstLogin']);
    }


    /**
     * @param $event
     */
    public function sendEmailFirstLogin($event)
    {
        \open20\amos\admin\models\UserAccessLog::saveLog();
        if (!(\Yii::$app->controller instanceof Controller)) {
            $user = \open20\amos\core\user\User::findOne(\Yii::$app->user->id);
            if ($user) {
                if ($user->userProfile->count_logins == 1) {
                    EventsAdminEmailUtility::sendEmailFirstAccessPassword($user);
                }
            }
        }
    }
}