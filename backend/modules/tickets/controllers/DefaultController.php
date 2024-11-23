<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

namespace backend\modules\tickets\controllers;


use backend\modules\tickets\models\ContactForm;
use open20\amos\core\controllers\BaseController;
use open20\amos\dashboard\controllers\TabDashboardControllerTrait;
use open20\amos\admin\models\UserProfile;
use Yii;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;



/**
 * Site controller
 */
class DefaultController extends BaseController
{

    use TabDashboardControllerTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                            'captcha'

                        ],
                        //'roles' => ['@']
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get']
                ]
            ]
        ];
    }

    public function init()
    {
        $this->initDashboardTrait();
        $this->setModelObj(new ContactForm());
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $this->layout = '@vendor/open20/amos-layout/src/views/layouts/main';
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => 'error_formatemp'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ],
        ];
    }

    /**
     * Displays contacts.
     *
     * @return string
     */
    public function actionIndex($layout = NULL)
    {
        $modelProfile = UserProfile::findOne(['user_id' => Yii::$app->user->id]);
        $modelForm = new ContactForm();
        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->validate()) {
            if ($modelForm->spedisciEmailStandard($modelProfile)) {
                Yii::$app->session->addFlash('success', 'Grazie per averci contattato. Vi risponderemo appena possibile.');
            } else {
                Yii::$app->session->addFlash('error', 'C\'è stato un\'errore nell\'invio dell\'email.');
            }
            return $this->refresh();
        } else {
            $this->layout = '@vendor/open20/amos-layout/src/views/layouts/form';

            return $this->render('contacts', ['modelForm' => $modelForm, 'modelProfile' => $modelProfile]);
        }
    }

}
