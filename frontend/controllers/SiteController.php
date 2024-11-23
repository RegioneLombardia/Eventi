<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

namespace app\controllers;

use luya\admin\models\Config;
use luya\cms\helpers\Url;
use luya\cms\menu\QueryOperatorFieldInterface;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use amos\userauth\models\UserLoginForm;

/**
 * Class SiteController
 * @package frontend\controllers
 */
class SiteController extends Controller
{

    const USERAUTH_CONFIG_REDIRECT_NAV_ID = 'userauth_redirect_nav_id';
    const USERAUTH_CONFIG_AFTER_LOGIN_NAV_ID = 'userauth_afterlogin_nav_id';
    const NOPERMISSION_CONFIG_REDIRECT_NAV_ID ='nopermission_redirect_nav_id';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'login'],
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout','save-tags'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * Logs in a user.
     * @return string|Response
     */
    public function actionLogin($redir = null)
    {
        if (!Yii::$app->user->isGuest) {
            return Yii::$app->response->redirect(Url::home(true));
        }

        $model = new UserLoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && Yii::$app->user->login($model->user)) {
            return $this->goBack();
        }

        $navItem = Yii::$app->menu->find()->where([QueryOperatorFieldInterface::FIELD_NAVID => Config::get(self::USERAUTH_CONFIG_REDIRECT_NAV_ID)])->with([
                'hidden'])->one();


        // redirect to the given nav item
        return Yii::$app->response->redirect($navItem->absoluteLink);
    }

    /**
     * Logs out the current user.
     * @return Response
     */
    public function actionLogout($redir = null)
    {
        Yii::$app->user->logout();
        $referrer = \Yii::$app->request->referrer;
        if($redir){
            return $this->redirect($redir);
        }
        return $this->redirect($referrer ? $referrer : \Yii::$app->params['platform']['frontendUrl']);

    }


}