<?php


/**
 * 
 * People
 * People are registered users of Webex Teams. Searching and viewing People
 * requires an auth token with a scope of 
 * spark:people_read.
 * 
 * Viewing the list of all People in your Organization requires an administrator
 * auth token with 
 * spark-admin:people_read scope.
 * 
 * Adding, updating, and removing People requires an administrator auth token
 * with the 
 * spark-admin:people_write scope.
 * 
 * To learn more about managing people in a room see the Memberships API.
 * For information about how to allocate Hybrid Services proscriptions to people,
 * see the Managing Hybrid Services guide.
 * 
 * MethodDescription
 * GET      https://webexapis.com/v1/people                 List People
 * POST     https://webexapis.com/v1/people                 Create a Person
 * GET      https://webexapis.com/v1/people/{personId}      Get Person Details
 * PUT      https://webexapis.com/v1/people/{personId}      Update a Person
 * DELETE   https://webexapis.com/v1/people/{personId}      Delete a Person
 * GET      https://webexapis.com/v1/people/me              Get My Own Details
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
class WebExPeople
{
    /**
     * {@inheritdoc}
     */
    protected $apiCall = 'people';
    
    /**
     * List People
     * GET      https://webexapis.com/v1/people
     */
    public function listPeople() {}

    /**
     * Create a Person
     * POST     https://webexapis.com/v1/people
     */
    public function createPerson() {}

    /**
     * Get Person Details
     * GET      https://webexapis.com/v1/people/{personId}
     */
    public function getPersonDetails($personId) {}

    /**
     * Update a Person
     * PUT      https://webexapis.com/v1/people/{personId}
     */
    public function updatePerson($personId) {}

    /**
     * Delete a Person
     * DELETE   https://webexapis.com/v1/people/{personId}
     */
    public function deletePerson($personId) {}

    /**
     * Get My Own Details
     * GET      https://webexapis.com/v1/people/me
     */
    public function getMyOwnDetails() {}
}
