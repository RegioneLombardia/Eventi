<?php

/**
 * https://developer.webex.com/docs/api/v1/meeting-participants
 * 
 * Get a meeting participant details of a live or post meeting. The participantId
 * is required to identify the meeting and the participant.
 * 
 * MethodDescription
 * GET    https://webexapis.com/v1/meetingParticipants              List Meeting Participants
 * GET    https://webexapis.com/v1/meetingParticipants/participantId{?hostEmail}
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
class WebExMeetingParticipants
{
    /**
     * {@inheritdoc}
     */
    protected $apiCall = 'meetingParticipants';
    
    /**
     * List Meeting Participants
     * 
     * GET     https://webexapis.com/v1/meetingParticipants  List Meeting Participants
     * 
     * Query Parameters
     * meetingId    string  Required    Unique identifier for the meeting
     * 
     * hostEmail    string              Email address for the meeting host. This parameter 
     *                                      is only used if the user or application calling
     *                                      the API has the admin-level scopes, the admin
     *                                      may specify the email of a user in a site
     *                                      they manage and the API will return meeting
     *                                      participants of the meetings that are
     *                                      hosted by that user.
     * 
     * Response Properties
     * id           string              The participant id to uniquely identify the meeting
     *                                      and the participant.
     * 
     * orgId        string              The id to identify the orgnaization.
     * 
     * host         boolean             The value is true or flase, it indicates 
     *                                      if the participant is the host of the meeting.
     * 
     * coHost       boolean             The value is true or flase, it indicates 
     *                                      if the participant has host privilege in the meeting.
     * 
     * spaceModerator   boolean         The value is true or false, it indicates 
     *                                      if the participant is the team space moderator. 
     *                                      This field returns only if the meeting 
     *                                      is associated with a Webex Teams space.
     * 
     * email        string              The email address of the participant
     * 
     * displayName  string              The name of the participant.
     * 
     * invitee      boolean             The value is true or flase, it indicates 
     *                                      if the participant is invited to the meeting or not.
     * 
     * video        string              The value is on or off, it indicates the participant's 
     *                                      video is on in the meeting.
     * 
     * muted        boolean             The value is true or false, it indicates whether 
     *                                      the participant's audio is muted.
     * 
     * state        string              The value can be "lobby" or "joined"
     * 
     * deviceType   string              The type of device.
     * 
     * audioType    string              The audio call type, it can be "pstn" or "voip".
     * 
     * joinedTime   string              The time when the device joined.
     * 
     * leftTime     string              leftTime is the exact moment when a specific 
     *                                      device left the meeting. In some cases, 
     *                                      there will be a delay of when leftTime is posted.
     * 
     * correlationId    string          An internal id that associated with each join.
     * 
     */
    public function listMeetingParticipants($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/' . $params['id'],
            'GET',
            [
                'hostEmail' => $params['hostEmail'],
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Get Meeting Participant Details
     * Get a meeting participant details of a live or post meeting.
     * 
     * URI Parameters
     * participantId    string  Required        The unique identifier for the meeting and the participant.
     * 
     * Query Parameters
     * hostEmail        string                  Email address for the meeting host. This parameter 
     *                                              is only used if the user or application calling 
     *                                              the API has the admin-level scopes, the 
     *                                              admin may specify the email of a user in a site 
     *                                              they manage and the API will return meeting 
     *                                              participants of the meetings that are hosted by that user.
     * 
     * GET    https://webexapis.com/v1/meetingParticipants/participantId{?hostEmail}
     */
    public function getMeetingParticipantDetails($params)
    {
       $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/' . params['participantId'],
            'GET',
            [
                'hostEmail' => $params['email'],
            ]
        );
       
        return $apiResponse;
    }
    
    /**
     * Update a Participant
     * To mute, unmute, expel, or admit a participant in a live meeting.
     * The participantId is required to identify the meeting and the participant.
     * The attribute 'expel' always takes precedence over 'admit' and 'muted'.
     * The request can have all 'expel', 'admit' and 'muted' or any of them.
     * 
     * PUT     https://webexapis.com/v1/meetingParticipants/participantId
     * 
     * URI Parameters
     * participantId    string  Required    Unique identifier for the meeting and the participant
     * 
     * 
     * Body Parameters
     * muted            boolean             The value is true or false, and means to mute 
     *                                          or unmute the audio of a participant.
     * admit            boolean             The value can be true or false. The value of true 
     *                                          is to admit a participant to the meeting if the participant 
     *                                          is in the lobby, No-Op if the participant is not 
     *                                          in the lobby or when the value is set to false.
     * expel            boolean             The attribute is exclusive and its value can be true or false. 
     *                                          The value of true means that the participant 
     *                                          will be expelled from the meeting, the value of false means No-Op.
     * Response Properties
     * id               string              The participant id to uniquely identify the meeting and the participant.
     * 
     * orgId            string              The id to identify the orgnaization.
     * 
     * host             boolean             The value is true or flase, it indicates 
     *                                          if the participant is the host of the meeting.
     * 
     * coHost           boolean             The value is true or flase, it indicates
     *                                          if the participant has host privilege in the meeting.
     * 
     * spaceModerator   boolean             The value is true or false, it indicates 
     *                                          if the participant is the team space moderator. 
     *                                          This field returns only if the meeting is associated 
     *                                          with a Webex Teams space.
     * email            string              The email address of the participant
     * 
     * displayName      string              The name of the participant.
     * 
     * invitee          boolean             The value is true or flase, it indicates 
     *                                          if the participant is invited to the meeting or not.
     * 
     * video            string              The value is on or off, it indicates the participant's 
     *                                          video is on in the meeting.
     * muted            boolean             The value is true or false, it indicates whether 
     *                                          the participant's audio is muted.
     * state            string              The value can be "lobby" or "joined"
     * 
     * deviceType       string              The type of device.
     * 
     * audioType        string              The audio call type, it can be "pstn" or "voip".
     * 
     * joinedTime       string              The time when the device joined.
     * 
     * leftTime         string              leftTime is the exact moment when a specific 
     *                                          device left the meeting. In some cases, there 
     *                                          will be a delay of when leftTime is posted.
     * correlationId    string              An internal id that associated with each join.
     */
    public function updateMeetingParticipant($params)
    {
        $params['muted'] = ($params['muted'] == 0) ? false : true;
        $params['admit'] = ($params['admit'] == 0) ? false : true;
        $params['expel'] = ($params['expel'] == 0) ? false : true;
        
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/' . $params['participantId'],
            'PUT',
            [
                'muted' => $params['muted'],
                'admit' => $params['admit'],
                'expel' => $params['expel']
            ]
        );
        
        return $apiResponse; 
    }
    
}