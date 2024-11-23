<?php

/**
 * Webhooks
 * 
 * https://developer.webex.com/docs/api/v1/webhooks
 * 
 * Webhooks allow your app to be notified via HTTP when a specific event occurs
 * in Webex Teams. For example, your app can register a webhook to be notified
 * when a new message is posted into a specific room.
 * 
 * Events trigger in near real-time allowing your app and backend IT systems to
 * stay in sync with new content and room activity.
 * 
 * Check the Webhooks Guide and our blog regularly for announcements of additional
 * webhook resources and event types.
 * 
 * MethodDescription
 * GET      https://webexapis.com/v1/webhooks               List Webhooks
 * POST     https://webexapis.com/v1/webhooks               Create a Webhook
 * GET      https://webexapis.com/v1/webhooks/{webhookId}   Get Webhook Details
 * PUT      https://webexapis.com/v1/webhooks/{webhookId}   Update a Webhook
 * DELETE   https://webexapis.com/v1/webhooks/{webhookId}   Delete a Webhook
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
class WebExWebhooks
{
    /**
     * {@inheritdoc}
     */
    protected $apiCall = 'webhooks';

    /**
     * GET      https://webexapis.com/v1/webhooks
     */
    public function listWebhooks()
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'GET',
            [
            ]
        );
        
        return $apiResponse; 
    }
    
    /**
     * POST     https://webexapis.com/v1/webhooks
     */
    public function createWebhook()
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'POST',
            [
            ]
        );
        
        return $apiResponse; 
    }
    
    /**
     * GET      https://webexapis.com/v1/webhooks/{webhookId}
     */
    public function getWebhookDetails()
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'GET',
            [
            ]
        );
        
        return $apiResponse; 
    }
    
    /**
     * PUT      https://webexapis.com/v1/webhooks/{webhookId}
     */
    public function updateWebhook()
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'PUT',
            [
            ]
        );
        
        return $apiResponse; 
    }
    
    /**
     * DELETE   https://webexapis.com/v1/webhooks/{webhookId}
     */
    public function deleteWebhook()
    {
        $apiResponse = $this->webex->apiRequest(
            $this->apiCall,
            'DELETE',
            [
            ]
        );
        
        return $apiResponse; 
    }
    
}