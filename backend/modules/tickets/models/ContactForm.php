<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

namespace backend\modules\tickets\models;

use backend\modules\tickets\utility\TicketsUtility;
use open20\amos\core\utilities\Email;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * ContactForm is the model behind the contact form.
 * @package frontend\models
 */
class ContactForm extends Model
{
    /**
     * @var string Name
     */
    public $first_name;

    /**
     * @var string Name
     */
    public $surname;

    /**
     * @var string Telephone number
     */
    public $telephone;

    /**
     * @var string Email address
     */
    public $email;

    /**
     * @var string Email subject
     */
    public $subject;

    /**
     * @var string Email body
     */
    public $message;

    /**
     * @var string Captcha
     */
    public $verifyCode;

    /**
     * @var string Attachement
     */
    public $attachment;

    /**
     * @var integer Ticket Type
     */
    public $ticketType;
    const TICKET_TYPE_TECNICO = "TICKET_TYPE_TECNICO";
    const TICKET_TYPE_AMMINISTRATIVO = "TICKET_TYPE_AMMINISTRATIVO";


    /**
     * @return array
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['first_name', 'surname', 'email', 'subject', 'message'], 'required'],
            [['subject','telephone'], 'safe'],
            // email has to be a valid email address
            ['email', 'email'],
            ['attachment', 'file'],
            // verifyCode needs to be entered correctly
//            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'subject' => Yii::t('amosapp', 'Oggetto'),
            'first_name' => Yii::t('amosapp', 'Nome'),
            'surname' => Yii::t('amosapp', 'Cognome'),
            'verifyCode' => Yii::t('amosapp', 'Codice verifica'),
            'message' => Yii::t('amosapp', 'Messaggio'),
            'ticketType' => Yii::t('amosapp', 'Tipo di ticket'),
            'attachment' => Yii::t('amosapp', 'Allegato'),
            'telephone' => Yii::t('amosapp', 'Telefono'),
        ];
    }


    /**
     * @param null $profile
     * @return bool
     */
    public function spedisciEmailStandard($profile = null)
    {
        $emailTo = Yii::$app->params['assistance']['email'];
        $this->subject .= ' - TICKET';

        $file = UploadedFile::getInstance($this, 'attachment');

        $text = Email::renderMailPartial('@backend/modules/tickets/views/mail/info-html', [
            'model' => $this,
            'modelProfile' => $profile,
        ]);
        $files = [];
        if(!empty($file)){
            $files = [$file->name => $file->tempName];
        }

        $ok = TicketsUtility::sendEmailGeneral(Yii::$app->params['supportEmail'], $emailTo, $this->subject, $text, $files);
        return $ok;

//        return $mail->send();
    }

    public function __toString()
    {
        return \Yii::t('amosapp', "Contatta l'assistenza");
    }
}
