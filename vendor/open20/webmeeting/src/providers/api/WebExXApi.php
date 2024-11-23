<?php

/**
 * Meetings api
 * 
 * https://developer.webex.com/docs/api/v1/xapi
 * 
 * The xAPI allows developers to programmatically invoke commands 
 * and query the status of devices that run Webex RoomOS software.
 * 
 * MethodDescription
 * GET      https://webexapis.com/v1/xapi/status                    Query Status
 * POST     https://webexapis.com/v1/xapi/command/{commandName}     Execute Command
 * 
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting API
 */

namespace open20\webmeeting\providers\api;

/**
 * WebExApi
 * 
 */
class WebExXApi 
{
    /**
     * 
     * @var type
     */
    protected $apiCall = 'xapi';
    
    /**
     * Query Status
     * GET      https://webexapis.com/v1/xapi/status
     * 
     * @param type $params
     */
    public function xApiStatus($params) {
        $apiResponse = $this->apiRequest(
            $this->apiCall . '/status',
            'GET',
            [
                'deviceId' => $params['deviceId'],
                'name' => $params['name']
            ]
        );
    }

    /**
     * https://developer.webex.com/docs/api/v1/xapi/execute-command
     * 
     * Pass params via body
     * $deviceId    required
     * $arguments   required
     * $body
     * 
     * Execute Command
     * POST     https://webexapis.com/v1/xapi/command/{commandName}
     */
    public function xApiCommand($params) {
        $apiResponse = $this->apiRequest(
            $this->apiCall . '/command/' . $params['commandName'],
            'POST',
            [
                'deviceId' => $params['deviceId'],
                'arguments' => $params['arguments']
            ]
        );
    }
}