<?php

/**
 * Meetings Preferences api
 * 
 * https://developer.webex.com/docs/api/v1/meeting-preferences
 * 
 * This API manages a user's meeting preferences, including Personal Meeting Room settings,
 * video and audio settings, meeting scheduling options, and site settings.
 * 
 * MethodDescription
 * GET      https://webexapis.com/v1/meetingPreferences                         Get Meeting Preference Details
 * GET      https://webexapis.com/v1/meetingPreferences/personalMeetingRoom     Get Personal Meeting Room Options
 * PUT      https://webexapis.com/v1/meetingPreferences/personalMeetingRoom     Update Personal Meeting Room Options
 * GET      https://webexapis.com/v1/meetingPreferences/audio                   Get Audio Options
 * PUT      https://webexapis.com/v1/meetingPreferences/audio                   Update Audio Options
 * GET      https://webexapis.com/v1/meetingPreferences/video                   Get Video Options
 * PUT      https://webexapis.com/v1/meetingPreferences/video                   Update Video Options
 * GET      https://webexapis.com/v1/meetingPreferences/schedulingOptions       Get Scheduling Options
 * PUT      https://webexapis.com/v1/meetingPreferences/schedulingOptions       Update Scheduling Options
 * GET      https://webexapis.com/v1/meetingPreferences/sites                   Get Site List
 * PUT      https://webexapis.com/v1/meetingPreferences/sites                   Update Default Site
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
class WebExMeetingPreferences
{
    /**
     * {@inheritdoc}
     */
    protected $apiCall = 'meetingPreferences';
    
    /**
     * Get Meetings Preference details
     * 
     * https://developer.webex.com/docs/api/v1/meeting-preferences/get-meeting-preference-details
     * 
     */
    public function getMeetingPreferenceDetails()
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'GET'
        );
        
        return $apiResponse;
    }
    
    /**
     * Get Personal Meeting Room Options
     * https://developer.webex.com/docs/api/v1/meeting-preferences/get-personal-meeting-room-options
     * 
     * Body Parameters
     * title        stringRequired      Meeting title. The title can be a maximum of 128 characters long.
     * agenda       string              Meeting agenda. The agenda can be a maximum of 2500 characters long.
     * password     stringRequired      Meeting password. Must conform to the site's password complexity settings.
     *                                      Read password management for details.
     * start        stringRequired      Date and time for the start of meeting in any ISO 8601 compliant format.
     *                                      start cannot be before current date and time or after end.
     *                                      Duration between start and end cannot be shorter than 10 minutes
     *                                      or longer than 24 hours.
     * end          stringRequired      Date and time for the end of meeting in any ISO 8601 compliant format.
     *                                      end cannot be before current date and time or before start.
     *                                      Duration between start and end cannot be shorter than 10 minutes
     *                                      or longer than 24 hours.
     * timezone     string              Time zone in which meeting was originally scheduled (conforming with
     *                                      the IANA time zone database).
     * recurrence   string              Meeting series recurrence rule (conforming with RFC 2445), applying only
     *                                      to meeting series. It doesn't apply to a scheduled meeting or an ended
     *                                      or ongoing meeting instance.
     * enabledAutoRecordMeeting     booleanRequired     Whether or not meeting is recorded automatically.
     * allowAnyUserToBeCoHost       booleanRequired     Whether or not to allow any invitee to be a cohost.
     * invitees     array               Invitees for meeting. The maximum size of invitees is 1000.
     * 
     */
    public function getPersonalMeetingRoomOptions()
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/personalMeetingRoom',
            'GET'
        );
        
        return $apiResponse;
    }
    
    /**
     * Update Personal Meeting Room Options
     * https://developer.webex.com/docs/api/v1/meeting-preferences/update-personal-meeting-room-options
     * 
     * Body Parameters
     * topic        stringRequired          Personal Meeting Room topic to be updated.
     * hostPin      stringRequired          Updated PIN for joining the room as host. The host PIN must be digits
     *                                          of a predefined length, e.g. 4 digits. It cannot contain sequential
     *                                          digits, such as 1234 or 4321, or repeated digits of the predefined
     *                                          length, such as 1111. The predefined length for host PIN can be viewed
     *                                          in user's My Personal Room page and it can only be changed
     *                                          by site administrator.
     * 
     * enabledAutoLock  booleanRequired     Update for option to automatically lock the Personal Room a number
     *                                          of minutes after a meeting starts. When a room is locked, invitees
     *                                          cannot enter until the owner admits them. The period after which
     *                                          the meeting is locked is defined by autoLockMinutes.
     * 
     * autoLockMinutes  numberRequired      Updated number of minutes after which the Personal Room is locked
     *                                          if enabledAutoLock is enabled. Valid options are 0, 5, 10, 15 and 20.
     * 
     * enabledNotifyHost    booleanRequired     Update for flag to enable notifying the owner of a Personal Room when
     *                                              someone enters the Personal Room lobby while the owner is not
     *                                              in the room.
     * 
     * supportCoHost        booleanRequired     Update for flag allowing other invitees to host a meetingCoHost
     *                                              in the Personal Room without the owner.
     * 
     * supportAnyoneAsCoHost    ooleanRequired      Update for flag allowing anyone with a host account on the
     *                                                  Webex site to be a cohost for meetings in the Personal Room.
     *                                                  If this flag is set false, the owner can choose cohosts
     *                                                  for Personal Room meetings.
     * 
     * coHosts          arrayRequired           Updated array defining cohosts for the room if the supportAnyoneAsCoHost
     *                                              attribute is set false.
     *                                              email
     *                                                  Email address for cohost. This attribute can be modified by
     *                                                  Update Personal Meeting Room Options API.
     *                                                  Possible values: john.andersen@example.com
     *                                              displayName
     *                                                  Display name for cohost. This attribute can be modified
     *                                                  by Update Personal Meeting Room Options API.
     *                                                  Possible values: John Andersen
     * 
     */
    public function updatePersonalMeetingRoomOptions($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/personalMeetingRoom',
            'PUT',
            [
                'hostPin' => $params['hostPin'],
                'enabledAutoLock' => $params['enabledAutoLock'],
                'autoLockMinutes' => $params['autoLockMinutes'],
                'enabledNotifyHost' => $params['enabledNotifyHost'],
                'supportCoHost' => $params['supportCoHost'],
                'supportAnyoneAsCoHost' => $params['supportAnyoneAsCoHost'],
                'coHosts' => $params['coHosts'],
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Get Audio Options
     * https://developer.webex.com/docs/api/v1/meeting-preferences/get-audio-options
     * 
     * URI Parameters
     * meetingId        stringRequired      Unique identifier for the meeting to be deleted. This parameter applies
     *                                          to meeting series and scheduled meetings. It doesn't apply to ended
     *                                          or in-progress meeting instances.
     */
    public function getAudioOptions($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/audio', 
            'GET'
        );
        
        return $apiResponse;
    }
    
    /**
     * Update Audio Options
     * https://developer.webex.com/docs/api/v1/meeting-preferences/update-audio-options
     * 
     */
    public function updateAudioOptions($params)
    {
         $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/audio', 
            'PUT',
            [
                'name' => $params['name']
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Get Video Options
     * GET      https://webexapis.com/v1/meetingPreferences/video
     */
    public function getVideoOptions($params)
    {
     $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/personalMeetingRoom', 
            'PUT',
            [
                'name' => $params['name']
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * PUT      https://webexapis.com/v1/meetingPreferences/video
     * 
     */
    public function updateVideoOptions()
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/personalMeetingRoom', 
            'PUT',
            [
                'name' => $params['name']
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Get Scheduling Options
     * GET      https://webexapis.com/v1/meetingPreferences/schedulingOptions
     * 
     */
    public function getSchedulingOptions($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/personalMeetingRoom', 
            'PUT',
            [
                'name' => $params['name']
            ]
        );
        
        return $apiResponse;
    }
        
    /**
     * Update Scheduling Options
     * PUT      https://webexapis.com/v1/meetingPreferences/schedulingOptions
     * 
     */
    public function updateSchedulingOptions($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/personalMeetingRoom', 
            'PUT',
            [
                'name' => $params['name']
            ]
        );
        
        return $apiResponse;
    }
        
    /**
     * Get Site List
     * GET      https://webexapis.com/v1/meetingPreferences/sites
     */
    public function getSiteList($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/personalMeetingRoom', 
            'PUT',
            [
                'name' => $params['name']
            ]
        );
        
        return $apiResponse;
    }
         
    /**
     * Update Default Site
     * PUT      https://webexapis.com/v1/meetingPreferences/sites
     */
    public function updateDefaultSite($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/personalMeetingRoom', 
            'PUT',
            [
                'name' => $params['name']
            ]
        );
        
        return $apiResponse;
    }

}