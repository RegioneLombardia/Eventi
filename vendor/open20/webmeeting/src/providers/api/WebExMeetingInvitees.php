<?php

/**
 * https://developer.webex.com/docs/api/v1/meeting-invitees
 * 
 * This API manages invitees' relationships to a meeting.
 * You can use the Meeting Invitees API to list, create, update, and delete
 * invitees.
 * 
 * MethodDescription
 * GET      https://webexapis.com/v1/meetingInvitees                    List Meeting Invitees
 * POST     https://webexapis.com/v1/meetingInvitees                    Create a Meeting Invitee
 * GET      https://webexapis.com/v1/meetingInvitees/{meetingInviteeId} Get a Meeting Invitee
 * PUT      https://webexapis.com/v1/meetingInvitees/{meetingInviteeId} Update a Meeting Invitee
 * DELETE   https://webexapis.com/v1/meetingInvitees/{meetingInviteeId} Delete a Meeting Invitee
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
 * 
 */
class WebExMeetingInvitees
{
    /**
     * {@inheritdoc}
     */
    protected $apiCall = 'meetingInvitees';
    
    /**
     * List Meeting Invitees
     * 
     * GET     https://webexapis.com/v1/meetingInvitees    List Meeting Invitees
     * 
     * Query Parameters
     * meetingId    string  Required    Unique identifier for the meeting for which
     *                                      invitees are being requested.
     *                                      The meeting can be meeting series,
     *                                      scheduled meeting or meeting instance
     *                                      which has ended or is ongoing.
     * 
     * max          number              Limit the maximum number of meeting invitees
     *                                      in the response, up to 100.
     *                                      Default: 10
     */
    public function listMeetingInvitees($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'GET',
            [
                'meetingId' => $params['meetingId'],
                'max' => $params['max'],
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Create a Meeting Invitee
     * Invites a person to attend a meeting.
     * Identify the invitee in the request body, by email address.
     * 
     * POST    https://webexapis.com/v1/meetingInvitees
     */
    public function createMeetingInvitee($params)
    {
       $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'POST',
            [
                'meetingId' => $params['meetingId'],
                'email' => $params['email'],
                'displayName' => $params['displayName'],
                'coHost' => $params['coHost'],
            ]
        );
        
        return $apiResponse; 
    }

    /**
     * Create a Meeting Invitee
     * Invites a person to attend a meeting.
     * Identify the invitee in the request body, by email address.
     *
     * POST    https://webexapis.com/v1/meetingInvitees/bulkInsert
     */
    public function createMeetingInviteeBulkInsert($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall.'/bulkInsert',
            'POST',
            [
                'meetingId' => $params['meetingId'],
                'hostEmail' => $params['hostEmail'],
                'items' => $params['items'],
            ]
        );

        return $apiResponse;
    }


    /**
     * Get a Meeting Invitee
     * Retrieves details for a meeting invitee identified by a meetingInviteeId in the URI.
     * 
     * GET     https://webexapis.com/v1/meetingInvitees/{meetingInviteeId}
     * 
     * URI Parameters
     * meetingInviteeId string  Required    Unique identifier for the invitee whose details are being requested.
     */
    public function getMeetingInvitee($params)
    {
       $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/' . $params['meetingInviteeId'],
            'GET',
            [
            ]
        );
        
        return $apiResponse; 
    }
    
    /**
     * PUT      https://webexapis.com/v1/meetingInvitees/{meetingInviteeId}
     * 
     * URI Parameters
     * meetingInviteeId string  Required    Unique identifier for the invitee to be updated.
     *                                          This parameter only applies to an invitee to a meeting series
     *                                          or a scheduled meeting. It doesn't apply to an invitee
     *                                          to an ended or ongoing meeting instance.
     * 
     * Body Parameters
     * email            string  Required    Email address for meeting invitee.
     * displayName      string              Display name for meeting invitee. The maximum length of displayName
     *                                          is 384 bytes. If displayName is not specified and the email
     *                                          has been associated with an existing Webex account,
     *                                          the display name associated with the Webex account will be used.
     *                                          If displayName is not specified and the email has not been
     *                                          associated with an existing Webex account,
     *                                          the email will be used instead.
     * coHost           boolean             Whether or not invitee is a designated alternate host for the meeting.
     *                                          See Add Alternate Hosts for Cisco Webex Meetings for more details.
     */
    public function updateMeetingInvitee($params)
    {
       $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/' . $params['meetingInviteeId'],
            'PUT',
            [
                'email' => $params['email'],
                'displayName' => $params['displayName'],
                'coHost' => $params['coHost'],
            ]
        );
        
        return $apiResponse; 
    }
    
    /**
     * DELETE   https://webexapis.com/v1/meetingInvitees/{meetingInviteeId}
     * 
     * URI Parameters
     * meetingInviteeId string  Required    Unique identifier for the invitee to be removed. This parameter only
     *                                          applies to an invitee to a meeting series or a scheduled meeting.
     *                                          It doesn't apply to an invitee to an ended or ongoing
     *                                          meeting instance.
     */
    public function deleteMeetingInvitee($params)
    {
        $query = '';
        if(!empty($params['query'])){
            $query = '?'.$params['query'];
        }
       $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/' . $params['meetingInviteeId'].$query,
            'DELETE'
        );
        
        return $apiResponse; 
    }

}