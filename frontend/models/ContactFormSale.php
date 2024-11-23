<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

namespace frontend\models;

use open20\amos\core\utilities\Email;
use Yii;
use yii\base\Model;
use yii\log\Logger;

/**
 * ContactForm is the model behind the contact form.
 * @package frontend\models
 */
class ContactFormSale extends Model
{
    /**
     * @var string Name
     */
    public $name;
    /**
     * @var string Name
     */
    public $surname;

    /**
     * @var string Email address
     */
    public $email;

    /**
     * @var string Phone subject
     */
    public $phone;

    /**
     * @var string Email body
     */
    public $body;

    /**
     * @var
     */
    public $type_event;

    /**
     * @var
     */
    public $sala;


    public $emailToSend;




    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            // [['name','surname','phone', 'type_event', 'email','emailToSend', 'body'], 'required','message' => 'Il campo non può essere vuoto'],
            [['name'], 'required', 'message' => \Yii::t('app','Il campo nome non può essere vuoto')],
            [['surname'], 'required', 'message' => \Yii::t('app','Il campo cognome non può essere vuoto')],
            [['phone'], 'required', 'message' => \Yii::t('app','Il campo telefono non può essere vuoto')],
            [['type_event'], 'required', 'message' => \Yii::t('app','Il campo tipo di evento non può essere vuoto')],
            [['email'], 'required', 'message' => \Yii::t('app','Il campo email non può essere vuoto')],
            [['emailToSend'], 'required', 'message' => \Yii::t('app','Il campo email non può essere vuoto')],
            [['body'], 'required', 'message' => \Yii::t('app','Il campo richiesta estesa non può essere vuoto')],
            [['body'], 'string', 'max' => 1000],
            ['sala', 'string'],
            // email has to be a valid email address
            ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

    /**
     * @param $to
     * @param $profile
     * @param $subject
     * @param $message
     * @param array $files
     * @return bool
     */
    public function sendEmail()
    {
        try {

            $subject = 'Eventi regione lombardia - Richiesta prenotazione sala';
            $message
                = "<strong>Nome</strong>: ".$this->name
                . "<br><strong>Cognome</strong>: ".$this->surname
                . "<br><strong>Email</strong>: ".$this->email
                . "<br><strong>Telefono</strong>: ".$this->phone
                . "<br><strong>Tipo di evento</strong>: ".$this->type_event
                . "<br><strong>Sala</strong>: ".$this->sala
                . "<br><strong>Richiesta estesa</strong>: "
                . "<br>".$this->body."";

            /** @var \open20\amos\core\utilities\Email $email */
            $email = new Email();
            $email->sendMail(\Yii::$app->params['email-assistenza'], $this->emailToSend, $subject, $message, []);
        } catch (\Exception $ex) {
            \Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return true;
    }
}
