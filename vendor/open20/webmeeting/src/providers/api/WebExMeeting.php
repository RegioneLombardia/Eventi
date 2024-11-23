<?php

/**
 * Meetings api
 * 
 * https://developer.webex.com/docs/api/v1/meetings
 * 
 * Meetings are virtual conferences where users can collaborate in real time
 * using audio, video, content sharing, chat, online whiteboards,
 * and more to collaborate.
 * 
 * This API focuses primarily on the scheduling and management of meetings.
 * You can use the Meetings API to list, create, get, update, and delete meetings
 * 
 * Several types of meeting objects are supported by this API, such as meeting
 * series, scheduled meeting, and ended or in-progress meeting instances.
 * See the Webex Meetings guide for more information about the types of meetings
 * 
 * MethodDescription
 * GET      https://webexapis.com/v1/meetings               List Meetings of a Meeting Series
 * POST     https://webexapis.com/v1/meetings               Create a Meeting
 * GET      https://webexapis.com/v1/meetings/{meetingId}   Get a Meeting
 * GET      https://webexapis.com/v1/meetings               List Meetings
 * PUT      https://webexapis.com/v1/meetings/{meetingId}   Update a Meeting
 * DELETE   https://webexapis.com/v1/meetings/{meetingId}   Delete a Meeting
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
class WebExMeeting 
{
    /**
     * {@inheritdoc}
     */
   protected $apiCall = 'meetings';
    
    /**
     * List Meetings of a Meeting Series
     * https://developer.webex.com/docs/api/v1/meetings/list-meetings-of-a-meeting-series
     *
     * GET      https://webexapis.com/v1/meetings
     *
     * Query Parameters
     * meetingSeriesId      stringRequired
     * Unique identifier for the meeting series.
     *
     * max                  number
     * Limit the maximum number of meetings in the response, up to 100, Default: 10
     *
     * from                 string
     * Start date and time (inclusive) for the range for which meetings
     * are to be returned in any ISO 8601 compliant format. 
     * from cannot be before current date and time or after to.
     * Default: Current date and time
     *
     * to                   String              
     * End date and time (exclusive) for the range for which meetings
     * are to be returned in any ISO 8601 compliant format.
     * to cannot be before current date and time or before from.
     * Default: If `from` is specified, the default value is `from`
     * plus 7 days; if `from` is not specified, the default value
     * is current date and time plus 7 days.
     *
     * meetingType          string
     * Meeting type for the meeting objects being requested.
     * If not specified, return meetings of all types.
     * Possible values: 
     *      scheduledMeeting, 
     *      meeting
     *
     * state                string
     * Meeting state for the meetings being requested. If not specified,
     * return meetings of all states. See the Webex Meetings guide
     * for more information about the states of meetings.
     * Possible values:
     *      scheduled,
     *      ready,
     *      lobby,
     *      inProgress,
     *      ended,
     *      missed
     *
     * isModified           boolean
     * Flag identifying whether or not only to retrieve scheduled meeting
     * instances which have been modified. This parameter only applies
     * to scheduled meetings. If it's true, only return modified scheduled
     * meetings; if it's false, only return unmodified scheduled meetings;
     * if not specified, all scheduled meetings will be returned.
     *
     */
    public function listMeetingsOfMeeting($params)
    {
        $params['isModified'] = ($params['isModified'] == 0) ? 'false' : 'true';
        
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'GET',
            [
                'meetingSeriesId' => $params['meetingSeriesId'],
                'max' => $params['max'],
                'from' => $params['from'],
                'to' => $params['to'],
                'meetingType' => $params['meetingType'],
                'state' => $params['state'],
                'isModified' => $params['isModified']
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Create a Meeting
     * https://developer.webex.com/docs/api/v1/meetings/create-a-meeting
     *
     * Body Parameters
     * title        string  Required
     * Meeting title. The title can be a maximum of 128 characters long.
     *
     * agenda       string
     * Meeting agenda. The agenda can be a maximum of 2500 characters long.
     *
     * password     string  Required
     * Meeting password. Must conform to the site's password complexity settings.
     * Read password management for details.
     *
     * start        string  Required      
     * Date and time for the start of meeting in any ISO 8601 compliant format.
     * start cannot be before current date and time or after end.
     * Duration between start and end cannot be shorter than 10 minutes or longer than 24 hours.
     *
     * end                          string      Required      
     * Date and time for the end of meeting in any ISO 8601 compliant format.
     * end cannot be before current date and time or before start.
     * Duration between start and end cannot be shorter than 10 minutes or longer than 24 hours.
     *
     * timezone                     string
     * Time zone in which meeting was originally scheduled (conforming with
     * the IANA time zone database).
     *
     * recurrence                   string
     * Meeting series recurrence rule (conforming with RFC 2445), applying only
     * to meeting series. It doesn't apply to a scheduled meeting or an ended
     * or ongoing meeting instance.
     *
     * enabledAutoRecordMeeting     boolean     Required
     * Whether or not meeting is recorded automatically.
     *
     * allowAnyUserToBeCoHost       boolean     Required
     * Whether or not to allow any invitee to be a cohost.
     *
     * -----------------------------------------------------------------------------------------------------
     * enabledJoinBeforeHost        boolean
     * Whether or not to allow any attendee to join the meeting before
     * the host joins the meeting.
     *
     * enableConnectAudioBeforeHost boolean 
     * Whether or not to allow any attendee to connect audio in the meeting before
     * the host joins the meeting.
     * This attribute is only applicable if the enabledJoinBeforeHost attribute is set to true.
     *
     * joinBeforeHostMinutes        number  
     * The number of minutes an attendee can join the meeting before the meeting start
     * time and the host joins. This attribute is only applicable if the enabledJoinBeforeHost
     * attribute is set to true. Valid options are 0, 5, 10 and 15.
     * Default is 0 if not specified.
     *
     * excludePassword              boolean
     * Whether or not to exclude password from the meeting email invitation.
     *
     * publicMeeting                boolean
     * Whether or not to allow the meeting to be listed on the public calendar.
     *
     * reminderTime                 number
     * The number of minutes before the meeting begins, for sending
     * an email reminder to the host.
     *
     * allowFirstUserToBeCoHost     boolean
     * Whether or not to allow the first attendee of the meeting with a host account
     * on the target site to become a cohost.
     * The target site is specified by siteUrl parameter when creating the meeting;
     * if not specified, it's user's preferred site.
     *
     * allowAuthenticatedDevices    boolean 
     * Whether or not to allow authenticated video devices
     * in the meeting's organization to start or join the meeting without a prompt.
     *
     * sendEmail                    boolean
     * Whether or not to send emails to host and invitees. It is an optional field and default value is true.
     *
     * hostEmail                    string
     * Email address for the meeting host. This attribute should only be set if the user or application 
     * calling the API has the admin-level scopes. When used, the admin may specify the email of a user 
     * in a site they manage to be the meeting host.
     *
     * siteUrl                      string
     * URL of the Webex site which the meeting is created on. If not specified, the meeting is created
     * on user's preferred site. All available Webex sites and preferred site of the user can be retrieved
     * by Get Site List API.
     *
     * registration                 object
     * Meeting registration. When this option is enabled, meeting invitee must register personal
     * information in order to join the meeting. Meeting invitee will receive an email
     * with a registration link for the registration. When the registration form has been
     * submitted and approved, an email with a real meeting link will be received.
     * By clicking that link the meeting invitee can join the meeting. Please note that
     * meeting registration does not apply to a meeting when it's a recurring meeting
     * with recurrence field or it has no password, or the Join Before Host option
     * is enabled for the meeting. Read Register for a Meeting in Cisco Webex Meetings for details.
     *
     * autoAcceptRequest            string
     * Whether or not meeting registration request is accepted automatically.
     *
     * requireFirstName             string
     * Whether or not registrant's first name is required for meeting registration. 
     * This option must always be true.
     *
     * requireLastName              string
     * Whether or not registrant's last name is required for meeting registration. 
     * This option must always be true.
     *
     * requireEmail                 string
     * Whether or not registrant's email is required for meeting registration.
     * This option must always be true.
     *
     * requireJobTitle              string
     * Whether or not registrant's job title is required for meeting registration.
     *
     * requireCompanyName           string
     * Whether or not registrant's company name is required for meeting registration.
     *
     * requireAddress1              string
     * Whether or not registrant's 1st address is required for meeting registration.
     *
     * requireAddress2              string
     * Whether or not registrant's 2nd address is required for meeting registration.
     *
     * requireCity                  string
     * Whether or not registrant's city is required for meeting registration.
     *
     * requireState                 string
     * Whether or not registrant's state is required for meeting registration.
     *
     * requireZipCode               string
     * Whether or not registrant's zip code is required for meeting registration.
     *
     * requireCountryRegion         string
     * Whether or not registrant's country or region is required for meeting registration.
     *
     * requireWorkPhone             string
     * Whether or not registrant's work phone number is required for meeting registration.
     *
     * requireFax                   string
     * Whether or not registrant's fax number is required for meeting registration.
     *
     * integrationTags              array
     * External keys created by an integration application in its own domain. They could be 
     * Zendesk ticket IDs, Jira IDs, Salesforce Opportunity IDs, etc. The integration 
     * application queries meetings by a key in its own domain. The maximum 
     * size of integrationTags is 3 and each item of integrationTags can be 
     * a maximum of 64 characters long
     *
     * -----------------------------------------------------------------------------------------------------
     * 
     * invitees                     array
     * Invitees for meeting. The maximum size of invitees is 1000.
     *
     * POST     https://webexapis.com/v1/meetings
     *
     * RETURN THIS VALUES
     * Response Properties
     * id                           string
     *      Unique identifier for meeting. If it's a meeting series, the id is used to identify
     *      the entire series; if it's a scheduled meeting from a series, the id is used
     *      to identify that scheduled meeting; if it's a meeting instance that is happening
     *      or has happened, the id is used to identify that instance.
     *
     * meetingNumber                string
     *      Meeting number. This attribute applies to meeting series, scheduled meeting
     *      and meeting instance, but it does not apply to meeting instances which have ended.
     *
     * title                        string
     *      Meeting title. This attribute can be modified for meeting serie
     *      or scheduled meeting by Update a Meeting API.
     *
     * agenda                       string
     *      Meeting agenda. The agenda can be a maximum of 2500 characters long.
     *      This attribute can be modified for meeting series or scheduled meeting
     *      by Update a Meeting API.
     *
     * password                     string
     *      Meeting password. This attribute applies to meeting series, scheduled meeting
     *      and in-progress meeting instance, but it does not apply to meeting instances
     *      which have ended. It can be modified for meeting series or scheduled meeting
     *      by Update a Meeting API.
     *
     * phoneAndVideoSystemPassword  string
     *      8-digit numeric password to join meeting from audio and
     *      video devices. This attribute applies to meeting series,
     *      scheduled meeting and in-progress meeting instance,
     *      but it does not apply to meeting instances which have ended.
     *
     * meetingType                  enum
     * Meeting type.
     *          meetingSeries
     *              Master of a scheduled series of meetings which consists of one or more
     *              scheduled meeting based on a recurrence rule.
     *          scheduledMeeting
     *              Instance from a master meeting series.
     *          meeting
     *              meeting instance that is actually happening or has happened.
     *
     * state                    enum
     * Meeting state.
     *      active
     *          This state only applies to meeting series. It indicates that one or more
     *          future scheduled meeting exist for this meeting series.
     *      scheduled
     *          This state only applies to scheduled meeting. It indicates that the meeting
     *          is scheduled in the future.
     *      ready
     *          This state only applies to scheduled meeting. It indicates that
     *          this scheduled meeting is ready to start or join now.
     *      lobby
     *          This state only applies to meeting instance. It indicates that a locked meeting
     *          has been joined by participants, but no hosts have joined.
     *      inProgress
     *          This state applies to meeting series and meeting instance. For meeting series,
     *          this state indicates that an instance of this series is happening now;
     *          for meeting instance, it indicates that the meeting has been joined and unlocked.
     *      ended
     *          This state applies to scheduled meeting and meeting instance.
     *          For scheduled meeting, it indicates that this scheduled meeting was started
     *          and is now over; for meeting instance, this state indicates that
     *          this meeting instance has concluded.
     *      missed
     *          This state only applies to scheduled meeting. It indicates that
     *          the meeting was scheduled in the past but never happened.
     *      expired
     *          This state only applies to meeting series. It indicates that all scheduled
     *          meetings of this series have passed.
     *
     * timezone                     string
     * Time zone of start and end, conforming with the IANA time zone database.
     *
     * start                        string
     * Start time for meeting in ISO 8601 compliant format. If the meeting
     * is a meeting series, start is the date and time the first meeting
     * of the series starts; if the meeting is a meeting series and current filter
     * is true, start is the date and time the upcoming or ongoing meeting
     * of the series starts; if the meeting is a scheduled meeting
     * from a meeting series, start is the date and time when that scheduled
     * meeting starts; if the meeting is a meeting instance that has happened
     * or is happening, start is the date and time that instance actually starts.
     * This attribute can be modified for meeting series or scheduled
     * meeting by Update a Meeting API.
     *
     * end                          string
     * End time for meeting in ISO 8601 compliant format. If the meeting is a meeting
     * series, end is the date and time the first meeting of the series ends;
     * if the meeting is a meeting series and current filter is true,
     * end is the date and time the upcoming or ongoing meeting of the series ends;
     * if the meeting is a scheduled meeting from a meeting series, end is the date
     * and time when that scheduled meeting ends; if the meeting is a meeting instance
     * that has happened, end is the date and time that instance actually ends;
     * if a meeting intance is in progress, end is not available.
     * This attribute can be modified for meeting series or scheduled meeting
     * by Update a Meeting API.
     *
     * recurrence                   string
     * Meeting series recurrence rule (conforming with RFC 2445), applying only
     * to recurring meeting series. It does not apply to meeting series with
     * only one scheduled meeting. This attribute can be modified for meeting
     * series by Update a Meeting API.
     *
     * hostUserId                   string
     * Unique identifier for meeting host. It's in the format of
     * Base64Encode(ciscospark://us/PEOPLE/hostUserId). For example, a hostUserId
     * is 7BABBE99-B43E-4D3F-9147-A1E9D46C9CA0, the actual value for it is
     * Y2lzY29zcGFyazovL3VzL1BFT1BMRS83QkFCQkU5OS1CNDNFLTREM0YtOTE0Ny1BMUU5RDQ2QzlDQTA=.
     *
     * hostDisplayName              string
     * Display name for meeting host.
     *
     * hostEmail                    string
     * Email address for meeting host.
     *
     * hostKey                      string
     * Key for joining meeting as host.
     *
     * siteUrl                      string
     * Site URL for the meeting.
     *
     * webLink                      string
     * Link to meeting information page where meeting client will be launched
     * if the meeting is ready for start or join.
     *
     * sipAddress                   string
     * SIP address for callback from a video system.
     *
     * dialInIpAddress              string
     * IP address for callback from a video system.
     *
     * enabledAutoRecordMeeting     boolean
     * Whether or not meeting is recorded automatically. This attribute
     * can be modified for meeting series or scheduled meeting
     * by Update a Meeting API.
     *
     * allowAnyUserToBeCoHost       boolean
     * Whether or not to allow any invitee to be a cohost. This attribute
     * can be modified for meeting series or scheduled meeting
     * by Update a Meeting API.
     *
     * telephony                    object
     * Information for callbacks from meeting to phone or for joining a teleconference
     * using a phone.
     *      accessCode      string      Code for authenticating a user to join teleconference. Users join
     *                                      the teleconference using the call-in number or the global call-in number,
     *                                      followed by the value of the accessCode.
     *      callInNumbers   array       Array of call-in numbers for joining teleconference from a phone.
     *                                      label   Label for call-in number.
     *                                              Possible values: Call-in toll-free number (US/Canada)
     *                                      callInNumber    Call-in number to join teleconference from a phone.
     *                                                      Possible values: 123456789
     *                                      tollType        Type of toll for the call-in number.
     *                                                      Possible values: toll, tollFree
     * 
     * links        object          HATEOAS information of global call-in numbers for joining teleconference
     *                                  from a phone.
     *                                  rel     string      Link relation describing how the target resource
     *                                                      is related to the current context (conforming with RFC5998).
     *                                  href    string      Target resource URI (conforming with RFC5998).
     *                                  method  string      Target resource method (conforming with RFC5998).
     * 
     * -----------------------------------------------------------------------------------------------------
     * enabledJoinBeforeHost        boolean
     * Whether or not to allow any attendee to join the meeting before the host joins the meeting. 
     * The enabledJoinBeforeHost attribute can be modified for meeting series or scheduled meeting 
     * by Update a Meeting API
     *
     * enableConnectAudioBeforeHost boolean
     * Whether or not to allow any attendee to connect audio in the meeting before host joins the meeting. 
     * This attribute is only applicable if the enabledJoinBeforeHost attribute is set to true. 
     * The enableConnectAudioBeforeHost attribute can be modified for meeting series or scheduled 
     * meeting by Update a Meeting API.
     *
     * joinBeforeHostMinutes        number
     * the number of minutes an attendee can join the meeting before the meeting start time and the host joins. 
     * This attribute is only applicable if the enabledJoinBeforeHost attribute is set to true. 
     * The joinBeforeHostMinutes attribute can be modified for meeting series or scheduled meeting 
     * by Update a Meeting API. Valid options are 0, 5, 10 and 15. Default is 0 if not specified.
     *
     * excludePassword              boolean
     * Whether or not to exclude password from the meeting email invitation.
     *
     * publicMeeting                boolean
     * Whether or not to allow the meeting to be listed on the public calendar.
     *
     * reminderTime                 number
     * The number of minutes before the meeting begins, for sending an email reminder to the host.
     *
     * allowFirstUserToBeCoHost     boolean
     * Whether or not to allow the first attendee of the meeting with a host account on the target 
     * site to become a cohost. The target site is specified by siteUrl parameter when creating the meeting; 
     * if not specified, it's user's preferred site. The allowFirstUserToBeCoHost attribute 
     * can be modified for meeting series or scheduled meeting by Update a Meeting API.
     *
     * allowAuthenticatedDevices    boolean
     * Whether or not to allow authenticated video devices in the meeting's organization to start 
     * or join the meeting without a prompt. This attribute can be modified for meeting series 
     * or scheduled meeting by Update a Meeting API.
     *
     * registration                 object
     * Meeting registration. When this option is enabled, meeting invitee must register personal information 
     * in order to join the meeting. Meeting invitee will receive an email with a registration link 
     * for the registration. When the registration form has been submitted and approved, an email 
     * with a real meeting link will be received. By clicking that link the meeting invitee can 
     * join the meeting. Please note that meeting registration does not apply to a meeting when 
     * it's a recurring meeting with recurrence field or it has no password, or the Join Before Host 
     * option is enabled for the meeting. Read Register for a Meeting in Cisco Webex Meetings for details.
     *
     * autoAcceptRequest            boolean
     * Whether or not meeting registration request is accepted automatically.
     *
     * requireFirstName             boolean
     * Whether or not registrant's first name is required for meeting registration.
     *
     * requireLastName              boolean
     * Whether or not registrant's last name is required for meeting registration.
     *
     * requireEmail                 boolean
     * Whether or not registrant's email is required for meeting registration.
     *
     * requireJobTitle              boolean
     * Whether or not registrant's job title is required for meeting registration.
     *
     * requireCompanyName           boolean
     * Whether or not registrant's company name is required for meeting registration.
     *
     * requireAddress1              boolean
     * Whether or not registrant's 1st address is required for meeting registration.
     *
     * requireAddress2              boolean
     * Whether or not registrant's 2nd address is required for meeting registration.
     *
     * requireCity                  boolean
     * Whether or not registrant's city is required for meeting registration.
     *
     * requireState                 boolean
     * Whether or not registrant's state is required for meeting registration.
     *
     * requireZipCode               boolean
     * Whether or not registrant's zip code is required for meeting registration.
     *
     * requireCountryRegion         boolean
     * Whether or not registrant's country or region is required for meeting registration.
     *
     * requireWorkPhone             boolean
     * Whether or not registrant's work phone number is required for meeting registration.
     *
     * requireFax                   boolean
     * Whether or not registrant's fax number is required for meeting registration.
     * -----------------------------------------------------------------------------------------------------
     *
     */
    public function createMeeting($params)
    {
        $params['enabledAutoRecordMeeting'] = ($params['enabledAutoRecordMeeting'] == 0) ? false : true;
        $params['allowAnyUserToBeCoHost'] = ($params['allowAnyUserToBeCoHost'] == 0) ? false : true;
        $apiParams =  [
            'title' => $params['title'],
            'agenda' => $params['agenda'],
            'password' => $params['password'],
            'start' => $params['start'],
            'end' => $params['end'],
            'timezone' => $params['timezone'],
            'recurrence' => $params['recurrence'],
            'enabledAutoRecordMeeting' => $params['enabledAutoRecordMeeting'],
            'allowAnyUserToBeCoHost' => $params['allowAnyUserToBeCoHost'],
            'sendEmail' => false,
            'invitees' => $params['invitees'],
        ];

        $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'POST',
            $apiParams
        );
        return $apiResponse;
    }
    
    /**
     * Get a Meeting
     * https://developer.webex.com/docs/api/v1/meetings/get-a-meeting
     * 
     * URI Parameters
     * meetingId    stringRequired      Unique identifier for the meeting being requested.
     * 
     * Query Parameters
     * current      boolean             Whether or not to retrieve only the current scheduled meeting
     *                                      of the meeting series, i.e. the meeting ready to join or start
     *                                      or the upcoming meeting of the meeting series. If it's true,
     *                                      return details for the current scheduled meeting of the series,
     *                                      i.e. the scheduled meeting ready to join or start or the upcoming
     *                                      scheduled meeting of the meeting series. If it's false or not specified,
     *                                      return details for the entire meeting series. This parameter
     *                                      only applies to meeting series. Default: false
     * 
     * GET      https://webexapis.com/v1/meetings/{meetingId}
     */
    public function getMeeting($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/' . $params['meetingId'], 
            'GET',
            [
                'name' => $params['name']
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * List Meetings
     * https://developer.webex.com/docs/api/v1/meetings/list-meetings
     * 
     * Query Parameters
     * meetingNumber        string          Meeting number for the meeting objects being requested. meetingNumber
     *                                          and webLink are mutually exclusive. If it's an exceptional meeting
     *                                          from a meeting series, the exceptional meeting instead of the master
     *                                          meeting series is returned.
     * webLink              string          URL encoded link to information page for the meeting objects being requested.
     *                                          meetingNumber and webLink are mutually exclusive.
     * meetingType          string          Meeting type for the meeting objects being requested. This parameter
     *                                          will be ignored if meetingNumber or webLink is specified.
     *                                          Possible values: meetingSeries, scheduledMeeting, meeting
     *                                          Default: meetingSeries
     * state                string          Meeting state for the meeting objects being requested. If not specified,
     *                                          return meetings of all states. This parameter will be ignored
     *                                          if meetingNumber or webLink is specified. See the Webex Meetings
     *                                          guide for more information about the states of meetings.
     *                                          Possible values: active, scheduled, ready, lobby, inProgress, ended,
     *                                              missed, expired
     * participantEmail     string          Meeting participant email address for the meeting objects being requested.
     *                                          This parameter will be ignored if meetingNumber or webLink is specified.
     * current              boolean         Flag identifying to retrieve the current scheduled meeting of the meeting
     *                                          series or the entire meeting series. This parameter only applies
     *                                          to scenarios where meetingNumber is specified and the meeting is not
     *                                          an exceptional meeting from a meeting series. If it's true, return
     *                                          the scheduled meeting of the meeting series which is ready to join
     *                                          or start or the upcoming scheduled meeting of the meeting series;
     *                                          if it's false, return the entire meeting series. Default: true
     * from                 string          Start date and time (inclusive) in any ISO 8601 compliant format for
     *                                          the meeting objects being requested. This parameter will be ignored
     *                                          if meetingNumber or webLink is specified.
     *                                          Default: If `to` is specified, the default value is 7 days before `to`.
     *                                          If `to` is not specified and `meetingType` equals `meeting`, the default
     *                                          value is 7 days before current date and time. If `to` is not specified
     *                                          and `meetingType` equals `meetingSeries` or `scheduledMeeting`,
     *                                          the default value is current date and time.
     * to                   String          End date and time (exclusive) in any ISO 8601 compliant format for the
     *                                          meeting objects being requested. This parameter will be ignored if
     *                                          meetingNumber or webLink is specified. Default: If `from` is specified,
     *                                          the default value is `from` plus 7 days. If `from` is not specified
     *                                          and `meetingType` equals `meeting`, the default value is current date
     *                                          and time. If `from` is not specified and `meetingType` equals
     *                                          `meetingSeries` or `scheduledMeeting`, the default value is current
     *                                          date and time plus 7 days.
     * max                  number          Limit the maximum number of meetings in the response, up to 100.
     *                                          This parameter will be ignored if meetingNumber or webLink is specified.
     *                                          Default: 10
     * 
     * GET      https://webexapis.com/v1/meetings
     */
    public function listMeetings($params)
    {
        $params['current'] = ($params['current'] == 0) ? 'false' : 'true';

        $apiResponse = $this->webex->apiRequest(
            $this->apiCall, 
            'GET',
            [
                'meetingNumber' => $params['meetingNumber'],
                'webLink' => $params['webLink'],
                'meetingType' => $params['meetingType'],
                'state' => $params['state'],
                'participantEmail' => $params['participantEmail'],
                'current' => $params['current'],
                'from' => $params['from'],
                'to' => $params['to'],
                'max' => $params['max'],
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Update a Meeting
     * https://developer.webex.com/docs/api/v1/meetings/update-a-meeting
     * 
     * URI Parameters
     * meetingId    stringRequired      Unique identifier for the meeting to be updated. This parameter applies
     *                                      to meeting series and scheduled meetings. It doesn't apply to ended
     *                                      or in-progress meeting instances.
     * Body Parameters
     * title        stringRequired      Meeting title. The title can be a maximum of 128 characters long.
     * agenda       string              Meeting agenda. The agenda can be a maximum of 2500 characters long.
     * password     stringRequired      Meeting password. Must conform to the site's password complexity settings.
     *                                      Read password management for details.
     * start        stringRequired      Date and time for the start of meeting in any ISO 8601 compliant format.
     *                                      start cannot be before current date and time or after end. Duration between
     *                                      start and end cannot be shorter than 10 minutes or longer than 24 hours.
     * end          stringRequired      Date and time for the end of meeting in any ISO 8601 compliant format.
     *                                      end cannot be before current date and time or before start. Duration between
     *                                      start and end cannot be shorter than 10 minutes or longer than 24 hours.
     * timezone     string              Time zone in which meeting was originally scheduled (conforming with
     *                                      the IANA time zone database).
     * recurrence   string              Meeting series recurrence rule (conforming with RFC 2445), applying only
     *                                      to meeting series. It doesn't apply to a scheduled meeting or an ended
     *                                      or ongoing meeting instance.
     * enabledAutoRecordMeeting     booleanRequired     Whether or not meeting is recorded automatically.
     * allowAnyUserToBeCoHost       booleanRequired     Whether or not to allow any invitee to be a cohost.
     * 
     * PUT      https://webexapis.com/v1/meetings/{meetingId}
     */
    public function updateMeeting($params)
    {
        // Something was wrong on first 
        if (empty($params['meetingId'])) {
            return $this->createMeeting($params);
        }
        
        $params['enabledAutoRecordMeeting'] = ($params['enabledAutoRecordMeeting'] == 0) ? false : true;
        $params['allowAnyUserToBeCoHost'] = ($params['allowAnyUserToBeCoHost'] == 0) ? false : true;
        
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/' . $params['meetingId'], 
            'PUT',
            [
                'title' => $params['title'],
                'agenda' => $params['agenda'],
                'password' => $params['password'],
                'start' => $params['start'],
                'end' => $params['end'],
                'timezone' => $params['timezone'],
                'recurrence' => $params['recurrence'],
                'enabledAutoRecordMeeting' => $params['enabledAutoRecordMeeting'],
                'allowAnyUserToBeCoHost' => $params['allowAnyUserToBeCoHost'],
                'sendEmail' => false,
                'invitees' => [$params['invitees']],
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Delete a Meeting
     * 
     * URI Parameters
     * meetingId        stringRequired      Unique identifier for the meeting to be deleted. This parameter applies
     *                                          to meeting series and scheduled meetings. It doesn't apply to ended
     *                                          or in-progress meeting instances.
     * DELETE   https://webexapis.com/v1/meetings/{meetingId}
     */
    public function deleteMeeting($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/' . $params['meetingId'], 
            'DELETE'
        );
        
        return $apiResponse;
    }
}