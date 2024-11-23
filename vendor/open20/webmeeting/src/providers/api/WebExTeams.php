<?php

/**
 * Teams
 * 
 * https://developer.webex.com/docs/api/v1/teams
 * 
 * Teams are groups of people with a set of rooms that are visible
 * to all members of that team. This API is used to manage
 * the teams themselves. Teams are created and deleted with this API.
 * You can also update a team to change its name, for example.
 * 
 * MethodDescription
 * GET      https://webexapis.com/v1/teams              List Teams
 * POST     https://webexapis.com/v1/teams              Create a Team
 * GET      https://webexapis.com/v1/teams/{teamId}     Get Team Details
 * PUT      https://webexapis.com/v1/teams/{teamId}     Update a Team
 * DELETE   https://webexapis.com/v1/teams/{teamId}     Delete a Team
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
class WebExTeams
{
    /**
     * {@inheritdoc}
     */
    protected $apiCall = 'teams';
    
    /**
     *
     * @var type
     */
    public $webex;
    
    /**
     * List Teams
     * GET      https://webexapis.com/v1/teams
     * 
     * @param type $max
     */
    public function listTeams($params) {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'GET',
            [
                'max' => $params['max']
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Create a Team
     * https://developer.webex.com/docs/api/v1/teams/create-a-team
     * 
     * POST
     * name -> required
     */
    public function createTeam($params)
    {
         $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'POST',
            [
                'name' => $params['name']
            ],
            [
                'Content-Type' => 'application/json',
            ],
            false
        );
        
        return $apiResponse;
    }

    /**
     * Get Team Details
     * GET      https://webexapis.com/v1/teams/{teamId}
     */
    public function teamDetails($params) {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'GET',
            [
                $params['teamId']
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Update a Team
     * PUT      https://webexapis.com/v1/teams/{teamId}
     * 
     * @param type $teamId
     * @param type $name
     */
    public function updateTeam($params)
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/' . $params['teamId'], 
            'PUT',
            [
                'name' => $params['name']
            ]
        );
        
        return $apiResponse;
    }
    
    /**
     * Delete a Team
     * DELETE   https://webexapis.com/v1/teams/{teamId}
     */
    public function deleteTeam($params) {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall . '/' . $params['teamId'], 
            'DELETE'
        );
        
        return $apiResponse;
    }
    
}