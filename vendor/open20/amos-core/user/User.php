<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\core\user
 * @category   CategoryName
 */

namespace open20\amos\core\user;

use open20\amos\admin\models\UserProfile;
use open20\amos\core\behaviors\AttributesChangeLogBehavior;
use open20\amos\core\record\Record;
use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use open20\amos\core\models\AccessTokens;
use open20\amos\core\module\BaseAmosModule;
use open20\amos\admin\AmosAdmin;
use open20\amos\core\record\CachedActiveQuery;

/**
 * Class User
 *
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property string $access_token
 * @property string $password write-only password
 * @property \open20\amos\admin\models\UserProfile $userProfile
 *
 * @package open20\amos\core\user
 */
class User extends Record implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE  = 10;

    protected $adminInstalled = NULL;
    protected $userProfile    = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->adminInstalled = Yii::$app->getModule(AmosAdmin::getModuleName());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['username', 'email'], 'safe'],
            [['email'], 'required'],
            [['email'], 'unique'],
            [['email'], 'unique', 'targetAttribute' => 'username'],
            [['username'], 'unique', 'skipOnEmpty' => true],
            [['username'], 'unique', 'targetAttribute' => 'email'],
            [['email'], 'email'],
            ['password_reset_token', 'default', 'value' => null],
            ['username', 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors   = parent::behaviors();
        $adminModule = \Yii::$app->getModule(AmosAdmin::getModuleName());
        if ($adminModule && !empty($adminModule->enableAttributeChangeLog)) {
            $behaviors['AttributesChangeLogBehavior'] = [
                'class' => AttributesChangeLogBehavior::className(),
                'attributesToLog' => ['email'],
                'configUserActivityLog' => [
                    'enabled' => true,
                    'userAttribute' => 'id',
                    'type' => UserProfile::LOG_TYPE_UPDATE_PROFILE,
                    'name' => BaseAmosModule::t('app', 'Aggiornamento profilo'),
                    'description' => BaseAmosModule::t('app', 'Aggiornamento profilo')
                ]
            ];
        }
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        //Security Cast
        $id = (int) $id;

        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * 
     * @return string
     */
    public function getAccessToken()
    {
        $token = AccessTokens::findOne(['user_id' => $this->id,'logout_at' => null]);
        if (is_null($token)) {
            $token               = new AccessTokens();
            $token->user_id      = $this->id;
            $token->access_token = \Yii::$app->getSecurity()->generateRandomString();
            $token->fcm_token    = null;
            $token->device_os    = 'web';
            $token->save(false);
        }
        return $token->access_token;
    }

    /**
     * 
     * @return string
     */
    public function getFcmToken()
    {
        $token = AccessTokens::findOne(['user_id' => $this->id]);

        return $token->fcm_token;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $Token = AccessTokens::findOne([
            'access_token' => $token,
            'logout_at' => null]
        );

        if ($Token) {
            return $Token->user;
        }

        return false;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Find inactive user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsernameInactive($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_DELETED]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        $condition       = ['email' => $email, 'status' => self::STATUS_ACTIVE];
        $allUsersByEmail = static::findAll($condition);
        if (count($allUsersByEmail) > 1) {
            return null;
        }
        return static::findOne($condition);
    }

    /**
     * Finds inactive user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmailInactive($email)
    {
        $condition       = ['email' => $email, 'status' => self::STATUS_DELETED];
        $allUsersByEmail = static::findAll($condition);
        if (count($allUsersByEmail) > 1) {
            return null;
        }
        return static::findOne($condition);
    }

    /**
     * Finds user by username or email
     *
     * @param string $usernameOrEmail
     * @return static|null
     */
    public static function findByUsernameOrEmail($usernameOrEmail)
    {
        $user = self::findByUsername($usernameOrEmail);
        if (is_null($user)) {
            $user = self::findByEmail($usernameOrEmail);
        }
        return $user;
    }

    /**
     * Finds inactive user by username or email
     *
     * @param string $usernameOrEmail
     * @return static|null
     */
    public static function findByUsernameOrEmailInactive($usernameOrEmail)
    {
        $user = self::findByUsernameInactive($usernameOrEmail);
        if (is_null($user)) {
            $user = self::findByEmailInactive($usernameOrEmail);
        }
        return $user;
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
                'password_reset_token' => $token,
                'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire    = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString().'_'.time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        if ($this->adminInstalled) {
            $modelClass = \open20\amos\admin\AmosAdmin::instance()->createModel('UserProfile');
            return $this->hasOne($modelClass::className(), ['user_id' => 'id']);
        } else {
            return null;
        }
    }

    /**
     * @return \yii\db\ActiveRecord
     */
    public function getProfile()
    {
        if ($this->adminInstalled) {
            if (is_null($this->userProfile)) {
                $userProfileQuery  = $this->getUserProfile();
                $userProfileCache  = CachedActiveQuery::instance($userProfileQuery);
                $userProfileCache->cache(60);
                $this->userProfile = $userProfileCache->one();
            }
            return $this->userProfile;
        } else {
            return null;
        }
    }
}