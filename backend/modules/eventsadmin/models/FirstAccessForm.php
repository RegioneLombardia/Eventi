<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\core\forms
 * @category   CategoryName
 */

namespace backend\modules\eventsadmin\models;

use open20\amos\core\module\Module;
use Yii;
use yii\base\Model;
use open20\amos\core\user\User;
use kartik\password\StrengthValidator;
use yii\helpers\ArrayHelper;



/**
 * First-Login form
 */
class FirstAccessForm extends Model
{

    const SCENARIO_CHECK_PRIVACY = 'check-privacy';
    /**
     * @var string Username
     */
    public $username;

    /**
     * @var string Password
     */
    public $password;

    /**
     * @var string Repeated Password
     */
    public $ripetiPassword;

    /**
     * @var string Password-reset token
     */
    public $token;

    /**
     * @var
     */
    public $privacy_2;

    /**
     * @var integer Privacy
     */
    public $privacy;

    private $_user = false;

    public function scenarios()
    {
        $parentScenarios = parent::scenarios();
        $scenarios = ArrayHelper::merge(
            $parentScenarios,
            [
                self::SCENARIO_CHECK_PRIVACY => ['username','password', 'ripetiPassword', 'privacy','privacy_2']
            ]
        );
        return $scenarios;
    }

    /**
     * Define Properties rules
     * @inheritdoc
     */
    public function rules()
    {
        $strength = 10;
        if(!empty($this->token)){
            $user = User::find()
                ->andWhere(['password_reset_token' => $this->token])->one();
            if($user && \Yii::$app->authManager->checkAccess($user->id, 'ADMIN')){
                $strength = 14;
            }
        }
        return [
            // username and password are both required
            [['username', 'password', 'ripetiPassword', 'privacy_2'], 'safe'],
            [['password'], StrengthValidator::className(), 'min' => $strength,  'digit' => 1, 'special' => 1, 'upper'=> 1, 'lower' => 1, 'userAttribute' => 'username'],
            ['ripetiPassword', 'compare', 'compareAttribute' => 'password', 'message' => \Yii::t('amoscore',"#first_access_pwd_compare_alert")],
            [['username'], 'required'],
            [['password'], 'required', 'message' => \Yii::t('amoscore',"#first_access_pwd_alert")],
            [['privacy'], 'required', 'requiredValue' => 1, 'message' => Module::t('amoscore', "#first_access_privacy_alert_not_accepted") ,'on' => self::SCENARIO_CHECK_PRIVACY],
            [['ripetiPassword'], 'required', 'message' => \Yii::t('amoscore',"#first_access_pwd_2_alert")],
            [['token'], 'string']
    ];
    }


    /**
     * Find User by Username
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * Check Username existence
     * @param string $username Username
     * @return User|null
     */
    public function verifyUsername($username)
    {
        $user = new User();
        $verifyUsername = $user->findOne(['username' => $username]);
        return $verifyUsername;
    }
}
