<?php
/**
 */

namespace yii\authclient;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\HttpException;

/**
 * OAuth2 serves as a client for the OAuth 2 flow.
 *
 * In oder to acquire access token perform following sequence:
 *
 * ```php
 * use yii\authclient\OAuth2;
 *
 * // assuming class MyAuthClient extends OAuth2
 * $oauthClient = new MyAuthClient();
 * $url = $oauthClient->buildAuthUrl(); // Build authorization URL
 * Yii::$app->getResponse()->redirect($url); // Redirect to authorization URL.
 * // After user returns at our site:
 * $code = Yii::$app->getRequest()->get('code');
 * $accessToken = $oauthClient->fetchAccessToken($code); // Get access token
 * ```
 *
 *
 * @since 2.0
 */
abstract class OAuth2 extends BaseOAuth
{
    /**
     * @var string protocol version.
     */
    public $version = '2.0';
    /**
     * @var string OAuth client ID.
     */
    public $clientId;
    /**
     * @var string OAuth client secret.
     */
    public $clientSecret;
    /**
     * @var string token request URL endpoint.
     */
    public $tokenUrl;
    /**
     * @var bool whether to use and validate auth 'state' parameter in authentication flow.
     * If enabled - the opaque value will be generated and applied to auth URL to maintain
     * state between the request and callback. The authorization server includes this value,
     * when redirecting the user-agent back to the client.
     * The option is used for preventing cross-site request forgery.
     * @since 2.1
     */
    public $validateAuthState = true;
    /**
     * @var bool Whether to enable proof key for code exchange (PKCE) support and add
     * a `code_challenge` and `code_verifier` to the auth request.
     * @since 2.2.10
     *
     */
    public $enablePkce = false;


    /**
     * Composes user authorization URL.
     * @param array $params additional auth GET params.
     * @return string authorization URL.
     */
    public function buildAuthUrl(array $params = [])
    {
        $defaultParams = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->getReturnUrl(),
            'xoauth_displayname' => Yii::$app->name,
        ];
        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        if ($this->validateAuthState) {
            $authState = $this->generateAuthState();
            $this->setState('authState', $authState);
            $defaultParams['state'] = $authState;
        }

        if ($this->enablePkce) {
            $codeVerifier = bin2hex(Yii::$app->security->generateRandomKey(64));
            $this->setState('authCodeVerifier', $codeVerifier);
            $defaultParams['code_challenge'] = trim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
            $defaultParams['code_challenge_method'] = 'S256';
        }

        return $this->composeUrl($this->authUrl, array_merge($defaultParams, $params));
    }

    /**
     * Fetches access token from authorization code.
     * @param string $authCode authorization code, usually comes at GET parameter 'code'.
     * @param array $params additional request params.
     * @return OAuthToken access token.
     * @throws HttpException on invalid auth state in case [[enableStateValidation]] is enabled.
     */
    public function fetchAccessToken($authCode, array $params = [])
    {
        if ($this->validateAuthState) {
            $authState = $this->getState('authState');
            $incomingRequest = Yii::$app->getRequest();
            $incomingState = $incomingRequest->get('state', $incomingRequest->post('state'));
            if (!isset($incomingState) || empty($authState) || strcmp($incomingState, $authState) !== 0) {
                throw new HttpException(400, 'Invalid auth state parameter.');
            }
            $this->removeState('authState');
        }

        $defaultParams = [
            'code' => $authCode,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->getReturnUrl(),
        ];

        if ($this->enablePkce) {
            $defaultParams['code_verifier'] = $this->getState('authCodeVerifier');
        }

        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->tokenUrl)
            ->setData(array_merge($defaultParams, $params));

         // Azure AD will complain if there is no `Origin` header.
        if ($this->enablePkce) {
            $request->addHeaders(['Origin' => Url::to('/')]);
        }

        $this->applyClientCredentialsToRequest($request);

        $response = $this->sendRequest($request);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $data = $request->getData();
        $data['access_token'] = $accessToken->getToken();
        $request->setData($data);
    }

    /**
     * Applies client credentials (e.g. [[clientId]] and [[clientSecret]]) to the HTTP request instance.
     * This method should be invoked before sending any HTTP request, which requires client credentials.
     * @param \yii\httpclient\Request $request HTTP request instance.
     * @since 2.1.3
     */
    protected function applyClientCredentialsToRequest($request)
    {
        $request->addData([
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);
    }

    /**
     * Gets new auth token to replace expired one.
     * @param OAuthToken $token expired auth token.
     * @return OAuthToken new auth token.
     */
    public function refreshAccessToken(OAuthToken $token)
    {
        $params = [
            'grant_type' => 'refresh_token'
        ];
        $params = array_merge($token->getParams(), $params);

        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->tokenUrl)
            ->setData($params);

        $this->applyClientCredentialsToRequest($request);

        $response = $this->sendRequest($request);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }

    /**
     * Generates the auth state value.
     * @return string auth state value.
     * @since 2.1
     */
    protected function generateAuthState()
    {
        $baseString = get_class($this) . '-' . time();
        if (Yii::$app->has('session')) {
            $baseString .= '-' . Yii::$app->session->getId();
        }
        return hash('sha256', uniqid($baseString, true));
    }

    /**
     * Creates token from its configuration.
     * @param array $tokenConfig token configuration.
     * @return OAuthToken token instance.
     */
    protected function createToken(array $tokenConfig = [])
    {
        $defaultTokenConfig = ['tokenParamKey' => 'access_token'];
        $tokenConfig = array_merge($defaultTokenConfig, $tokenConfig);

        return parent::createToken($tokenConfig);
    }

    /**
     * Authenticate OAuth client directly at the provider without third party (user) involved,
     * using 'client_credentials' grant type.
     * @param array $params additional request params.
     * @return OAuthToken access token.
     * @since 2.1.0
     */
    public function authenticateClient($params = [])
    {
        $defaultParams = [
            'grant_type' => 'client_credentials',
        ];

        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->tokenUrl)
            ->setData(array_merge($defaultParams, $params));

        $this->applyClientCredentialsToRequest($request);

        $response = $this->sendRequest($request);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }

    /**
     * Authenticates user directly by 'username/password' pair, using 'password' grant type.
     * @param string $username user name.
     * @param string $password user password.
     * @param array $params additional request params.
     * @return OAuthToken access token.
     * @since 2.1.0
     */
    public function authenticateUser($username, $password, $params = [])
    {
        $defaultParams = [
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
        ];

        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->tokenUrl)
            ->setData(array_merge($defaultParams, $params));

        $this->applyClientCredentialsToRequest($request);

        $response = $this->sendRequest($request);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }

    /**
     * Authenticates user directly using JSON Web Token (JWT).
     * @param string $username
     * @param \yii\authclient\signature\BaseMethod|array $signature signature method or its array configuration.
     * If empty - [[signatureMethod]] will be used.
     * @param array $options additional options. Valid options are:
     *
     * - header: array, additional JWS header parameters.
     * - payload: array, additional JWS payload (message or claim-set) parameters.
     * - signatureKey: string, signature key to be used, if not set - [[clientSecret]] will be used.
     *
     * @param array $params additional request params.
     * @return OAuthToken access token.
     * @since 2.1.3
     */
    public function authenticateUserJwt($username, $signature = null, $options = [], $params = [])
    {
        if (empty($signature)) {
            $signatureMethod = $this->getSignatureMethod();
        } elseif (is_object($signature)) {
            $signatureMethod = $signature;
        } else {
            $signatureMethod = $this->createSignatureMethod($signature);
        }

        $header = isset($options['header']) ? $options['header'] : [];
        $payload = isset($options['payload']) ? $options['payload'] : [];

        $header = array_merge([
            'typ' => 'JWT'
        ], $header);
        if (!isset($header['alg'])) {
            $signatureName = $signatureMethod->getName();
            if (preg_match('/^([a-z])[a-z]*\-([a-z])[a-z]*([0-9]+)$/is', $signatureName, $matches)) {
                // convert 'RSA-SHA256' to 'RS256' :
                $signatureName = $matches[1] . $matches[2] . $matches[3];
            }
            $header['alg'] = $signatureName;
        }

        $payload = array_merge([
            'iss' => $username,
            'scope' => $this->scope,
            'aud' => $this->tokenUrl,
            'iat' => time(),
        ], $payload);
        if (!isset($payload['exp'])) {
            $payload['exp'] = $payload['iat'] + 3600;
        }

        $signatureBaseString = base64_encode(Json::encode($header)) . '.' . base64_encode(Json::encode($payload));
        $signatureKey = isset($options['signatureKey']) ? $options['signatureKey'] : $this->clientSecret;
        $signature = $signatureMethod->generateSignature($signatureBaseString, $signatureKey);

        $assertion = $signatureBaseString . '.' . $signature;

        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->tokenUrl)
            ->setData(array_merge([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $assertion,
            ], $params));

        $response = $this->sendRequest($request);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }
}
