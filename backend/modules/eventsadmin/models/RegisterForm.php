<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\models
 * @category   CategoryName
 */

namespace backend\modules\eventsadmin\models;

use backend\modules\eventsadmin\utility\EventsAdminUtility;
use open20\amos\admin\AmosAdmin;
use open20\amos\core\validators\CFValidator;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;

/**
 * Class LoginForm
 * @package open20\amos\admin\models
 */
class RegisterForm extends Model
{
    public $nome;
    public $cognome;
    public $email;
    public $privacy;
    public $privacy_2;

    public $sesso;
    public $eta;
    public $provincia;
    public $comune;
    public $telefono;
    public $codice_fiscale;
    public $azienda;

    public $token;

    public $userSocial;
    public $datiRecuperatiDaSocial = false;
    public $socialScelto;

    /**
     * @var string $captcha
     */
    public $reCaptcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['nome'], 'required', 'message' => AmosAdmin::t('amosadmin', "#register_name_alert")],
            [['cognome'], 'required', 'message' => AmosAdmin::t('amosadmin', "#register_surname_alert")],
            [['email'], 'required', 'message' => AmosAdmin::t('amosadmin', "#register_email_alert")],
            [['privacy'], 'required', 'message' => AmosAdmin::t('amosadmin', "#register_privacy_alert")],
            [['privacy'], 'required', 'requiredValue' => 1, 'message' => AmosAdmin::t('amosadmin', "#register_privacy_alert_not_accepted")],
            [['nome', 'cognome','azienda','telefono','codice_fiscale'], 'string'],
            ['email', 'email'],
            ['codice_fiscale', CFValidator::className()],
            ['telefono', \open20\amos\core\validators\PhoneValidator::className()],
            [['eta','provincia', 'comune', 'sesso','token','privacy_2'],'safe'],
            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'message' => AmosAdmin::t('amosadmin', "#register_recaptcha_alert")],
            [['userSocial','datiRecuperatiDaSocial', 'socialScelto'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'nome' => AmosAdmin::t('amosadmin', 'Nome'),
            'cognome' => AmosAdmin::t('amosadmin', 'Cognome'),
        ]);
    }

    /**
     * @param $model
     */
    public function loadDataFromSocial()
    {
        if (!\Yii::$app->request->isPost) {
            $user = null;
            $social = isset(Yii::$app->request->get()['social']) ? Yii::$app->request->get()['social'] : null;
            $socialdone = isset(Yii::$app->request->get()['social-done']) ? Yii::$app->request->get()['social-done'] : null;

            //redirect to endpoint
            if (!empty($social)) {
                return EventsAdminUtility::getUserSocial($social);
            }
            if (!empty($socialdone)) {
                $user = \Yii::$app->session->get('socialAuthUserProfile');
            }

            if (!is_null($user) && $user instanceof  \Hybridauth\User\Profile) {
                $this->nome = $user->firstName;
                $this->cognome = $user->lastName;
                $this->email = $user->email;

                $this->userSocial = Json::encode($user);
                $this->datiRecuperatiDaSocial = true;
                $this->socialScelto = $socialdone;
            }
        }
    }
}
