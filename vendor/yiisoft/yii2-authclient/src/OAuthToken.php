<?php
/**
 */

namespace yii\authclient;

use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * Token represents OAuth token.
 *
 * @property int $expireDuration Token expiration duration. Note that the type of this property differs in
 * getter and setter. See [[getExpireDuration()]] and [[setExpireDuration()]] for details.
 * @property string $expireDurationParamKey Expire duration param key.
 * @property-read bool $isExpired Is token expired.
 * @property-read bool $isValid Is token valid.
 * @property-read array $params
 * @property string $token Token value.
 * @property string $tokenSecret Token secret value.
 *
 * @since 2.0
 */
class OAuthToken extends BaseObject
{
    /**
     * @var string key in [[params]] array, which stores token key.
     */
    public $tokenParamKey = 'oauth_token';
    /**
     * @var string key in [[params]] array, which stores token secret key.
     */
    public $tokenSecretParamKey = 'oauth_token_secret';
    /**
     * @var int object creation timestamp.
     */
    public $createTimestamp;

    /**
     * @var string key in [[params]] array, which stores token expiration duration.
     * If not set will attempt to fetch its value automatically.
     */
    private $_expireDurationParamKey;
    /**
     * @var array token parameters.
     */
    private $_params = [];


    public function __construct(array $config = [])
    {
        if (array_key_exists('tokenParamKey', $config)) {
            $this->tokenParamKey = ArrayHelper::remove($config, 'tokenParamKey');
        }
        if (array_key_exists('tokenSecretParamKey', $config)) {
            $this->tokenSecretParamKey = ArrayHelper::remove($config, 'tokenSecretParamKey');
        }
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->createTimestamp === null) {
            $this->createTimestamp = time();
        }
    }

    /**
     * @param string $expireDurationParamKey expire duration param key.
     */
    public function setExpireDurationParamKey($expireDurationParamKey)
    {
        $this->_expireDurationParamKey = $expireDurationParamKey;
    }

    /**
     * @return string expire duration param key.
     */
    public function getExpireDurationParamKey()
    {
        if ($this->_expireDurationParamKey === null) {
            $this->_expireDurationParamKey = $this->defaultExpireDurationParamKey();
        }

        return $this->_expireDurationParamKey;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
    }

    /**
     * Sets param by name.
     * @param string $name param name.
     * @param mixed $value param value,
     */
    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
    }

    /**
     * Returns param by name.
     * @param string $name param name.
     * @return mixed param value.
     */
    public function getParam($name)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : null;
    }

    /**
     * Sets token value.
     * @param string $token token value.
     * @return $this the object itself
     */
    public function setToken($token)
    {
        $this->setParam($this->tokenParamKey, $token);
    }

    /**
     * Returns token value.
     * @return string token value.
     */
    public function getToken()
    {
        return $this->getParam($this->tokenParamKey);
    }

    /**
     * Sets the token secret value.
     * @param string $tokenSecret token secret.
     */
    public function setTokenSecret($tokenSecret)
    {
        $this->setParam($this->tokenSecretParamKey, $tokenSecret);
    }

    /**
     * Returns the token secret value.
     * @return string token secret value.
     */
    public function getTokenSecret()
    {
        return $this->getParam($this->tokenSecretParamKey);
    }

    /**
     * Sets token expire duration.
     * @param string $expireDuration token expiration duration.
     */
    public function setExpireDuration($expireDuration)
    {
        $this->setParam($this->getExpireDurationParamKey(), $expireDuration);
    }

    /**
     * Returns the token expiration duration.
     * @return int token expiration duration.
     */
    public function getExpireDuration()
    {
        return $this->getParam($this->getExpireDurationParamKey());
    }

    /**
     * Fetches default expire duration param key.
     * @return string expire duration param key.
     */
    protected function defaultExpireDurationParamKey()
    {
        $expireDurationParamKey = 'expires_in';
        foreach ($this->getParams() as $name => $value) {
            if (strpos($name, 'expir') !== false) {
                $expireDurationParamKey = $name;
                break;
            }
        }

        return $expireDurationParamKey;
    }

    /**
     * Checks if token has expired.
     * @return bool is token expired.
     */
    public function getIsExpired()
    {
        $expirationDuration = $this->getExpireDuration();
        if (empty($expirationDuration)) {
            return false;
        }

        return (time() >= ($this->createTimestamp + $expirationDuration));
    }

    /**
     * Checks if token is valid.
     * @return bool is token valid.
     */
    public function getIsValid()
    {
        $token = $this->getToken();

        return (!empty($token) && !$this->getIsExpired());
    }
}
