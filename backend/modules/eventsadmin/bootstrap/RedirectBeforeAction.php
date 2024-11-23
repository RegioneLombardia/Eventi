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
class RedirectBeforeAction implements BootstrapInterface
{
    /**
     * @param $app
     */
    public function bootstrap($app)
    {
        Event::on(\yii\base\Controller::className(), \yii\base\Controller::EVENT_BEFORE_ACTION, [$this, 'startUpRedirect']);
    }

    /**
     * @param $event
     */
    public function startUpRedirect($event)
    {
        if (!(Yii::$app->controller instanceof Controller)) {
            $adminModule = Yii::$app->getModule(AmosAdmin::getModuleName());
            if (!is_null($adminModule)) {
                //redirect for dl semplificazione
                $profile = UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
                if ($profile && !$profile->privacy) {
                    if (Yii::$app->controller->action->id != 'accept-privacy'
                        && Yii::$app->controller->id != 'security'
                        && Yii::$app->controller->module->id != 'socialauth'
                        && Yii::$app->controller->module->id != 'socialauthfe'
                        && Yii::$app->controller->module->id != 'attachments'
                        && (\Yii::$app->controller->action->id != 'logout')
                        && (\Yii::$app->controller->action->id != 'privacy')
                        && (\Yii::$app->controller->action->id != 'error')
                    ) {
                        $userProfileWizard = new RedirectBeforeActionComponent();
                        $userProfileWizard->redirectToUrl("/" . AmosAdmin::getModuleName() . "/security/accept-privacy");
                        Yii::$app->response->send();
                    }
                }
            }
        }
    }
}
