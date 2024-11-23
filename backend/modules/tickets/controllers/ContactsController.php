<?php

namespace backend\modules\tickets\controllers;

use backend\modules\tickets\utility\TicketsUtility;
use open20\amos\core\controllers\BackendController;
use open20\amos\core\utilities\Email;
use backend\modules\tickets\models\FormContatti;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    preference\userprofile\controllers
 * @category   CategoryName
 */

/**
 * Description of BaseController
 *
 */
class ContactsController extends BackendController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                //'only' => ['settings'],
                'rules' => [
                    [
                        'actions' => [
                            'index',
                        ],
                        'allow' => true,
                    ],


                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'contacts' => ['post', 'get'],
                    'quick-registration' => ['post', 'get'],
                ],
            ],
        ];
    }

    public function init()
    {
        // Layout Bootstrap Italia
        $this->setUpLayout('@frontend/views/layouts/landing-cms');
        //$this->view->params['customClassMainContent'] = 'container';

        parent::init();
    }

    /**
     *
     * @return void
     */
    public function actionIndex()
    {
        $toEmail = \Yii::$app->params['email-assisitenza'];
        if(!empty( \Yii::$app->params['assistance']['emailFrontend'])) {
            $toEmail = \Yii::$app->params['assistance']['emailFrontend'];
        }
        $successMessage = '';

        $model = new FormContatti();
        $ok = null;
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            if ($model->validate()) {
                // invio email
                $tipoMessaggio = isset($model->elencoTipiMessaggio[$model->tipoMessaggio]) ? $model->elencoTipiMessaggio[$model->tipoMessaggio] : '';
                $subject = \Yii::t('app',"Gestione Eventi Regione Lombardia") .' - '.$tipoMessaggio;
                $corpoMail = '<strong>Nome:</strong> ' . $model->nome . ' <br>
                <strong>Cognome</strong>: ' . $model->cognome . ' <br>
                <strong>E-mail:</strong> ' . $model->email . ' <br>
                <strong>Tipo messaggio:</strong> ' . $tipoMessaggio . ' <br>
                
                <strong>Messaggio:</strong><br>'."\t". str_replace("\r", "<br />", $model->messaggio) .'';

                $ok = TicketsUtility::sendEmailGeneral($model->email, $toEmail, $subject, $corpoMail);
                if ($ok) {
                    $model = new FormContatti();
                }

            }

            // VarDumper::dump( $model->errors, $depth = 10, $highlight = true);
        }

        return $this->render("contacts", [
            'model' => $model,
            'ok' => $ok,
            'successMessage' => $successMessage
        ]);
    }

//    public function actionQuickRegistration($email = 'stefano.pavani+RAPIDA@open20.it')
//    {
//        // $email = 'michele.zucchini+RAPIDA@open20.it';
//
//        EmailUtility::sendUserMailQuickRegistration($email, 'Lombardia Informa: registrazione rapida', $email, 'Password2020');
//    }
}
