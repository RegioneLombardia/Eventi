<?php

namespace amos\userauth\frontend\controllers;

use amos\userauth\frontend\Module;
use amos\userauth\models\UserLoginForm;
use luya\admin\models\Config;
use luya\cms\menu\QueryOperatorFieldInterface;
use luya\helpers\Url;
use luya\web\Controller;
use Yii;
use yii\filters\HttpCache;
use yii\web\Response;

class DefaultController extends Controller
{

    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return [
            'httpCache' => [
                'class' => HttpCache::class,
                'cacheControlHeader' => 'no-store, no-cache',
                'lastModified' => function ($action, $params) {
                    return time();
                },
            ],
        ];
    }

    /**
     * Render the login form model.
     *
     * @return Response|string
     */
    public function actionIndex($redir = null)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect($this->getRedirectUrl($redir));
        }

        $model = new UserLoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && Yii::$app->user->login($model->user)) {
            $to_go = null;
            if (!empty($redir)) {
                $to_go = $this->redirect($this->getRedirectUrl($redir));
            } elseif (!empty(Yii::$app->user->getReturnUrl())) {
                $to_go = $this->redirect($this->getRedirectUrl(Yii::$app->user->getReturnUrl()));
            }
            return $to_go;
        }

        return $this->render('index',
                [
                    'model' => $model,
        ]);
    }

    /**
     * Get the redirect url from config, redir parmater or default base (home) url.
     *
     * @param string $redir Optional urlencoded redirect from url
     * @return string
     */
    protected function getRedirectUrl($redir)
    {
        if (!empty($redir)) {
            return urldecode($redir);
        }

        $navId = Config::get(Module::USERAUTH_CONFIG_AFTER_LOGIN_NAV_ID, false);

        if ($navId) {
            $navItem = Yii::$app->menu->find()->where([QueryOperatorFieldInterface::FIELD_NAVID => $navId])->with([
                    'hidden'])->one();

            if ($navItem) {
                return $navItem->absoluteLink;
            }
        }

        return Url::base(true);
    }
}