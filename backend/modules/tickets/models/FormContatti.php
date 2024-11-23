<?php
namespace backend\modules\tickets\models;

use open20\amos\admin\AmosAdmin;
use Yii;
use yii\base\Model;

/**
 * Model without table
 * 
 */
class FormContatti extends Model
{
    public $elencoTipiMessaggio = [
        1 => 'Richiesta informazioni',
        2 => 'Segnalazione malfunzionamento',
        3 => 'Opinione sul servizio',
        4 => 'Suggerimento / proposta',
        5 => 'Altro',
    ];
    public $nome;
    public $cognome;
    public $email;
    public $confermaEmail;
    public $telefono;
    public $tipoMessaggio;
    public $messaggio;
    public $privacy;
    public $reCaptcha;



    public function rules()
    {
        return [
            [['nome','cognome', 'email', 'confermaEmail', 'tipoMessaggio', 'messaggio'], 'required'],
            [['email', 'confermaEmail'], 'email'],
            ['privacy','required', 'requiredValue' => 1, 'message' => \Yii::t('app', "E' necessario accettare la privacy.")],
            [['messaggio'], 'string', 'max' => 1000],
            ['confermaEmail', 'compare', 'compareAttribute' => 'email', 'message' => Yii::t('app', 'Le email inserite non coincidono')],
            [['telefono', 'cognome'], 'safe'],
            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator2::className(),
                'secret' => \Yii::$app->params['google_recaptcha_secret'],
                'message' => AmosAdmin::t('amosadmin', "#register_recaptcha_alert")]
        ];
    }

    public function attributeLabels()
    {
        return [
            'privacy' => Yii::t('app', 'Ho preso visione dell\'informativa privacy'),
            'email' => Yii::t('app', 'Email'),
            'confermaEmail' => Yii::t('app', 'Conferma Email'),
            'tipoMessaggio' => Yii::t('app', 'Tipo messaggio'),
            'messaggio' => Yii::t('app', 'Messaggio'),
            'nome' => Yii::t('app', 'Nome'),
            'cognome' => Yii::t('app', 'Cognome'),
        ];
    }

}