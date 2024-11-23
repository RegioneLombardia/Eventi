<?php

/**
 * Hybridauth
 * https://hybridauth.github.io | https://github.com/hybridauth/hybridauth
 * (c) 2020 Hybridauth authors | https://hybridauth.github.io/proscription.html
 * 
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting API
 */
namespace open20\webmeeting\providers;

use Hybridauth\Exception\InvalidArgumentException;
use Hybridauth\Exception\UnexpectedApiResponseException;
use Hybridauth\Exception\InvalidApplicationCredentialsException;
use Hybridauth\Exception\UnexpectedValueException;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Data;
use Hybridauth\User;

use yii\helpers\ArrayHelper;

/**
 * WebEx OAuth2 provider adapter.
 *
 * Example:
 *
 *   $config = [
 *       'callback' => Hybridauth\HttpClient\Util::getCurrentUrl(),
 *       'keys' => ['id' => '', 'team_id' => '', 'key_id' => '', 'key_file' => '', 'key_content' => ''],
 *       'scope' => 'name email',
 *
 *        // WebEx's custom auth url params
 *       'authorize_url_parameters' => [
 *          'response_type' => 'code'
            'client_id' => Issued when creating your app
 *          'redirect_uri' => Must match one of the URIs provided during integration registration
 *          'scope' => A space-separated list of scopes being requested by your integration (see below)
 *          'state' => A unique string that will be passed back to your integration upon completion (see below)
 *       ]
 *   ];
 *
 *   $adapter = new Hybridauth\Provider\WebEx($config);
 *
 *   try {
 *       $adapter->authenticate();
 *
 *       $userProfile = $adapter->getUserProfile();
 *       $tokens = $adapter->getAccessToken();
 *       $response = $adapter->setUserStatus("Hybridauth test message..");
 *   } catch (\Exception $e) {
 *       echo $e->getMessage() ;
 *   }
 *
 * Requires:
 *
 * composer require codercat/jwk-to-pem
 * composer require firebase/php-jwt
 *
 */
class WebEx extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    public $serviceToCall = null;
    
    /**
     * {@inheritdoc}
     */
    protected $scope = 'spark:all';

    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://webexapis.com/v1/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://webexapis.com/v1/authorize';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://webexapis.com/v1/access_token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://developer.webex.com/docs/integrations';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();
        
        /* optional: determine how exchange Authorization Code with an Access Token */
        $this->tokenExchangeParameters = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->callback
        ];
        $this->tokenExchangeMethod = 'POST';
        $this->tokenExchangeHeaders = [
            'Authorization' => 'Basic ' . base64_encode($this->clientId 
            .  ':'
            . $this->clientSecret)
        ];
        
        $this->AuthorizeUrlParameters['scope'] = 'spark-compliance:memberships_read spark-admin:resource_groups_read spark-admin:broadworks_subscribers_write meeting:recordings_read spark:all analytics:read_all meeting:preferences_write spark-admin:people_write meeting:admin_recordings_read spark-admin:organizations_read spark-admin:places_read meeting:schedules_write spark-compliance:team_memberships_read spark-compliance:team_memberships_write spark-admin:devices_read spark-admin:hybrid_clusters_read spark-compliance:messages_read meeting:admin_schedule_read spark-admin:devices_write spark-admin:workspaces_write meeting:admin_schedule_write meeting:schedules_read spark-compliance:memberships_write spark-admin:broadworks_enterprises_read identity:placeonetimepassword_create spark-admin:roles_read meeting:recordings_write meeting:preferences_read spark-admin:workspaces_read spark-admin:resource_group_memberships_read spark-compliance:events_read spark-admin:resource_group_memberships_write spark-compliance:rooms_read spark-admin:call_qualities_read spark-compliance:messages_write spark-admin:broadworks_subscribers_read spark:kms meeting:admin_recordings_write spark-admin:hybrid_connectors_read audit:events_read spark-compliance:teams_read spark-admin:places_write spark-admin:proscriptions_read spark-admin:people_read';

        $this->AuthorizeUrlParameters['redirect_uri'] = $this->callback;
        $this->apiRequestHeaders['Content-Type'] = 'application/json';
    }
    
    /**
     * Refreshing an Access Token
     *
     * RFC6749: If the authorization server issued a refresh token to the
     * client, the client makes a refresh request to the token endpoint by
     * adding the following parameters ... in the HTTP request entity-body:
     *
     *    - grant_type     REQUIRED. Value MUST be set to "refresh_token".
     *    - refresh_token  REQUIRED. The refresh token issued to the client.
     *    - scope          OPTIONAL.
     *
     * http://tools.ietf.org/html/rfc6749#section-6
     *
     * This method is similar to exchangeCodeForAccessToken(). The only
     * difference is here we exchange refresh_token for a new access_token.
     *
     * @param array $parameters
     *
     * @return string|null Raw Provider API response, or null if we cannot refresh
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws InvalidAccessTokenException
     */
    public function setAccessTokenByRefreshToken($refreshTokenModel)
    {
        $this->setAccessToken([
            "expires_at" => $refreshTokenModel->expires_at,
            "access_token" => $refreshTokenModel->access_token,
            "refresh_token" => $refreshTokenModel->refresh_token,
        ]);
    }
    
    /**
     * 
     * @param type $parameters
     * @return type
     */
    public function refreshAccessToken($parameters = [])
    {
        $this->tokenRefreshParameters['grant_type'] = 'refresh_token';
        $this->tokenRefreshParameters['client_secret'] = $this->tokenExchangeParameters['client_secret'];
        $this->tokenRefreshParameters['client_id'] = $this->tokenExchangeParameters['client_id'];
        
        return parent::refreshAccessToken($parameters);
    }
    
    /**
     * TDB test it!
     */
    public function __call($name, $params)
    {
        $apiResponse = null;
        try {
           if (!empty($this->serviceToCall)) {
               $params = array_shift($params);
               $invitees = isset($params['invitees'])
                    ? $params['invitees']
                    : null;
               
                $params = $this->convertParamsToApi($params);
                if (!empty($invitees)) {
                    unset($params['invitees']);
                    $params = ArrayHelper::merge(
                        $params,
                        ['invitees' => $this->convertParamsToApi($invitees)]
                    );
               }
               
               $this->serviceToCall->webex = $this;
               $apiResponse = call_user_func(array($this->serviceToCall, $name), $params);
            }
        }
        catch (\Exception $e) {
            /**
             * This fellow will catch everything else
            */
            $apiResponse = $this->getHttpClient()->getResponseBody();

            \Yii::getLogger()->log($apiResponse, \yii\log\Logger::LEVEL_ERROR);
        }

        return $this->checkApiResponse($apiResponse);
    }

    /**
     * 
     * @param type $apiResponse
     * @return boolean
     */
    public function checkApiResponse($apiResponse)
    {
        $api = json_decode($apiResponse);
        if (isset($api->message)) {
            return $api;
        }
        
        return $this->convertApiToParams($apiResponse);
    }
    
    /**
     * 
     * @param type $params
     */
    public function convertParamsToApi($params)
    {
        $apiFields = [
            'meeting_id' => 'id',
            'allow_any_user_to_be_co_host' => 'allowAnyUserToBeCoHost',
            'enabled_auto_record_meeting' => 'enabledAutoRecordMeeting',
            'dial_in_ip_address' => 'dialInIpAddress',
            'sip_address' => 'sipAddress',
            'web_link'=> 'webLink',
            'site_url'=> 'siteUrl',
            'host_key'=> 'hostKey',
            'host_email' => 'hostEmail',
            'host_display_name' => 'hostDisplayName',
            'host_user_id' => 'hostUserId',     
            'meeting_type' => 'meetingType',
            'phone_and_video_system_password' => 'phoneAndVideoSystemPassword',
            'meeting_number' => 'meetingNumber',

            'display_name' => 'displayName',
            'co_host' => 'coHost',

            'team_id' => 'teamId',

            'meeting_invitee_id' => 'meetingInviteeId',
            
            'enabled_join_before_host' => 'enabledJoinBeforeHost',
            'enable_connect_audio_before_host' => 'enableConnectAudioBeforeHost',
            'join_before_host_minutes' => 'joinBeforeHostMinutes',
            'exclude_password' => 'excludePassword',
            'public_meeting' => 'publicMeeting',
            'reminder_time' => 'reminderTime',
            'allow_first_user_to_be_co_host' => 'allowFirstUserToBeCoHost',
            'allow_authenticated_devices' => 'allowAuthenticatedDevices',
            'send_email' => 'sendEmail',
            'host_email' => 'hostEmail',
        ];
        $tmp = [];
        foreach($params as $key => $value) {
            if (array_key_exists($key, $apiFields)) {
                $tmp[$apiFields[$key]] = $value;
            } else {
                $tmp[$key] = $value;
            }
        }
        
        return $tmp;
    }
    
    /**
     * 
     * @param type $params
     */
    public function convertApiToParams($apiResponse)
    {
        $apiFields = [
            'id' => 'meeting_id',
            'allowAnyUserToBeCoHost' => 'allow_any_user_to_be_co_host',
            'enabledAutoRecordMeeting' => 'enabled_auto_record_meeting',
            'dialInIpAddress' => 'dial_in_ip_address',
            'sipAddress' => 'sip_address',
            'webLink' => 'web_link',
            'siteUrl' => 'site_url',
            'hostKey' => 'host_key',
            'hostEmail' => 'host_email',
            'hostDisplayName' => 'host_display_name',
            'hostUserId' => 'host_user_id',
            'meetingType' => 'meeting_type',
            'phoneAndVideoSystemPassword' => 'phone_and_video_system_password',
            'meetingNumber' => 'meeting_number',

            'displayName' => 'display_name',
            'coHost' => 'co_host',

            'teamId' => 'team_id',

            'meetingId' => 'meeting_id',
            'meetingInviteeId' => 'meeting_invitee_id',
        ];
        $tmp = [];
        foreach($apiResponse as $key => $value) {
            if (array_key_exists($key, $apiFields)) {
                if (is_bool($value)) {
                    $value = (int)$value;
                }
                $tmp[$apiFields[$key]] = $value;
            } else {
                $tmp[$key] = $value;
            }
        }
        
        return $tmp;
    }
    
    /**
     * Response Codes
     * The list below describes the common success and error responses
     * you should expect from the API.
     * 
     */
    public function getStatusRequest() {
        $errorCodes = [
            200 => 'OK',                    // Successful request with body content.
            204 => 'No Content',            // Successful request without body content.
            400	=> 'Bad Request',           // The request was invalid or cannot be otherwise served.
                                            // An accompanying error message will explain further.
            401 => 'Unauthorized',          // Authentication credentials were missing or incorrect.
            403 => 'Forbidden',             // The request is understood, but it has been refused
                                            //      or access is not allowed.
            404 => 'Not Found',             // The URI requested is invalid or the resource requested,
                                            //      such as a user, does not exist. 
                                            //      Also returned when the requested format
                                            //      is not supported by the requested method.
            405 => 'Method Not Allowed',    // The request was made to a resource using
                                            //      an HTTP request method that is not supported.
            409 => 'Conflict',              // The request could not be processed because
                                            //      it conflicts with some established rule
                                            //      of the system. For example, a person
                                            //      may not be added to a room more than once.
            410 => 'Gone',                  // The requested resource is no longer available.
            415 => 'Unsupported Media Type',    // The request was made to a resource without
                                                // specifying a media type or used a media
                                                // type that is not supported.
            423 => 'Locked',                // The requested resource is temporarily unavailable.
                                            //      A Retry-After header may be present
                                            //      that specifies how many seconds you need
                                            //      to wait before attempting the request again.
            428 => 'Precondition Required', // File(s) cannot be scanned for malware
                                            //  and need to be force downloaded.
            429 => 'Too Many Requests',     // Too many requests have been sent
                                            //      in a given amount of time and the request
                                            //      has been rate limited. A Retry-After header
                                            //      should be present that specifies how many
                                            //      seconds you need to wait before a successful
                                            //      request can be made.
            500 => 'Internal Server Error', // Something went wrong on the server. If the issue
                                            //      persists, feel free to contact
                                            //      the Webex Developer Support team.
            502 => 'Bad Gateway',           // The server received an invalid response
                                            //      from an upstream server while processing
                                            //      the request. Try again later.
            503 => 'Service Unavailable',   // Server is overloaded with requests.
                                            //      Try again later.
            504 => 'Gateway Timeout',       // An upstream server failed to respond on time.
                                            //      If your query uses max parameter, please try
                                            //      to reduce it.
        ];
    }
}
