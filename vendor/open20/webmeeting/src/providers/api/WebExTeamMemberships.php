<?php

/**
 * Team Memberships
 * 
 * https://developer.webex.com/docs/api/v1/team-memberships
 * 
 * Team Memberships represent a person's relationship to a team. Use this API
 * to list members of any team that you're in or create memberships to invite
 * someone to a team. Team memberships can also be updated to make someone
 * a moderator or deleted to remove them from the team.
 * 
 * Just like in the Webex Teams app, you must be a member of the team in order
 * to list its memberships or invite people.
 * 
 * MethodDescription
 * GET  https://webexapis.com/v1/team/memberships   List Team Memberships
 * POST https://webexapis.com/v1/team/memberships   Create a Team Membership
 * GET  https://webexapis.com/v1/team/memberships/{membershipId}    Get Team Membership Details
 * PUT  https://webexapis.com/v1/team/memberships/{membershipId}    Update a Team Membership
 * DELETE   https://webexapis.com/v1/team/memberships/{membershipId}    Delete a Team Membership
 * 
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
class WebExTeamMemberships
{
    /**
     * {@inheritdoc}
     */
    protected $apiCall = 'teams/memberships';

    /**
     * {@inheritdoc}
     * @var type 
     */
    public $webex;
    
    /**
     * List Team Memberships
     * https://developer.webex.com/docs/api/v1/team-memberships/list-team-memberships
     * 
     * Query Parameters
     * teamId   stringRequired  List memberships for a team, by ID.
     * max      number          Limit the maximum number of team memberships
     *                              in the response. Default: 100
     * 
     * GET  https://webexapis.com/v1/team/memberships
     */
    public function listTeamMemberships($params) {
        $apiResponse = $this->webex->apiRequest(
            $this->$apiCall,
            'GET',
            [
                'teamId' => $params['teamId'],
                'max' => $params['max']
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Create a Team Membership
     * https://developer.webex.com/docs/api/v1/team-memberships/create-a-team-membership
     * 
     * 
     * Body Parameters
     * teamId       stringRequired  The team ID.
     * personId     string          The person ID.
     * personEmail  string          The email address of the person.
     * isModerator  boolean         Whether or not the participant is a team moderator.
     * 
     * POST https://webexapis.com/v1/team/memberships
     */
    public function createTeamMembership($params)
    {
         $apiResponse = $this->webex->apiRequest(
            $this->$apiCall,
            'POST',
            [
                'teamId' => $params['teamId'],
                'personId' => $params['personId'],
                'personEmail' => $params['personEmail'],
                'isModerator' => $params['isModerator'],
            ]
        );
        
        return $apiResponse;
    }

    /**
     * Get Team Membership Details
     * 
     * URI Parameters
     * membershipId     stringRequired      The unique identifier for the team membership.
     * 
     * GET  https://webexapis.com/v1/team/memberships/{membershipId}    
     */
    public function getTeamMembershipDetails($params) {
        $apiResponse = $this->webex->apiRequest(
            $this->$apiCall,
            'GET',
            [
                'membershipId' => $params['membershipId']
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Update a Team Membership
     * https://developer.webex.com/docs/api/v1/team-memberships/get-team-membership-details
     * 
     * Shows details for a team membership, by ID.
     * Specify the team membership ID in the membershipId URI parameter.
     * 
     * PUT  https://webexapis.com/v1/team/memberships/{membershipId}
     */
    public function updateTeamMembership($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->$apiCall,
            'PUT',
            [
                'membershipId' => $params['membershipId']
            ],
            [
                'Content-Type' => 'application/json',
            ],
            false
        );
        
        return $apiResponse;
    }
    
    /**
     * Delete a Team
     * https://webexapis.com/v1/team/memberships/{membershipId}
     * 
     */
    public function deleteTeamMembership($params) {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'DELETE',
            [
                'name' => $params['name']
            ]
        );
        
        return $apiResponse;
    }
    
}