<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 03/11/2021
 * Time: 10:39
 */

namespace open20\webmeeting\models;


use open20\webmeeting\providers\api\WebExMeeting;
use open20\webmeeting\utility\WebMeetingUtility;
use open20\webmeeting\WebMeetingModule;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Hybridauth\Data;


class ConnectionWebex extends Model
{

    /**
     *
     * @var type
     */
    public $coHostUserEmail;

    /**
     *
     * @var type
     */
    public $apiToCall;

    /**
     *
     * @var type
     */
    public $timezone;

    /**
     *
     * @var type
     */
    public $webMeetingInviteeModel;

    /**
     * @var
     */
    public $webMeetingModule;


    public $refreshToken;


    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->webMeetingModule = \Yii::$app->getModule(WebMeetingModule::getModuleName());


        $this->coHostUserEmail = ArrayHelper::map(
            WebMeetingUtility::getMeetingHostUsers(),
            'id',
            'value'
        );

        $this->timezone = ArrayHelper::map(
            WebMeetingUtility::getMeetingTimezone(),
            'id',
            'value'
        );

        $this->webMeetingInviteeModel = $this->webMeetingModule::instance()
            ->createModel('WebMeetingInviteeModel');

        $this->apiToCall = $this->webMeetingModule
            ->getHybridService(new WebExMeeting());

       $this->authenticationAndRefreshToken();
    }

    /**
     * @param $classApi
     * @param $method
     * @param $apiParams
     * @return mixed
     */
    public function callApi($classApi, $method, $apiParams){
        $apiToCall = $this->webMeetingModule->getHybridService(new $classApi());
        $apiResponse = $apiToCall->$method($apiParams);
        return $apiResponse;
    }

    /**
     *
     */
    public function authenticationAndRefreshToken(){
//        $this->auth();
        $this->refreshToken();
    }

    /**
     *
     */
    public function refreshToken(){
        $tokenModel = $this->webMeetingModule::instance()->createModel('WebMeetingTokenModel');
        $this->refreshToken = $tokenModel->find()
            ->andWhere(['user_id' => 1])
            ->one();

        if ($this->refreshToken) {
            $this->webMeetingModule
                ->getHybridService()
                ->setAccessTokenByRefreshToken($this->refreshToken);

            $tokenLife = strtotime($this->refreshToken->updated_at) + $this->refreshToken->expires_in;

            $todayDate = strtotime(WebMeetingModule::getTodayDate());
            $expired = $tokenLife <= $todayDate;

            if ($expired) {
                $token = $this->webMeetingModule->getHybridService()->refreshAccessToken();
                $data = (new Data\Parser())->parse($token);
                $this->refreshToken->access_token = $data->access_token;
                $this->refreshToken->expires_in = $data->expires_in;
                $this->refreshToken->save();
            }
        }
    }



    public function auth()
    {
        $tokenModel = $this->webMeetingModule::instance()->createModel('WebMeetingTokenModel');
        $refreshToken = $tokenModel->find()
            ->andWhere(['user_id' => 1])
            ->one();

        if (empty($refreshToken)) {
            $isConnected = $this->webMeetingModule->getHybridService()->authenticate();

            $tokens = $this->apiToCall->getAccessToken();
            $tokenModel = $this->webMeetingModule::instance()->createModel('WebMeetingTokenModel');
            $refreshToken = $tokenModel->find()
                ->andWhere(['refresh_token' => $tokens['refresh_token']])
                ->one();

            if (empty($refreshToken)) {
                $tokenModel->user_id = 1;   // Always ADMIN user
                $tokenModel->access_token = $tokens['access_token'];
                $tokenModel->refresh_token = $tokens['refresh_token'];
                $tokenModel->expires_in = $tokens['expires_in'];
                $tokenModel->expires_at = $tokens['expires_at'];
                $tokenModel->save();
            }
        }

    }

    /**
     * @param $apiResponse
     */
    public function showErrors($apiResponse){
        $flashSaveMessage = $apiResponse->message;
        $tmp = [];
        foreach ($apiResponse->errors as $error) {
            $tmp[] = $error->description;
        }
        $flashSaveMessage .= '<br />' . implode("<br />", $tmp);
        \Yii::$app->getSession()->addFlash('danger', \open20\webmeeting\WebMeetingModule::_t($flashSaveMessage));
        \Yii::getLogger()->log($apiResponse, \yii\log\Logger::LEVEL_ERROR);
    }


    /**
     * @param $apiResponse
     * @param $webmeeting
     */
    public function saveResponse($apiResponse, $webmeeting){
        $apiResponse['id'] = $webmeeting->id;
        $newApiResponse = [StringHelper::basename(get_class($webmeeting)) => $apiResponse];
        $webmeeting->load($newApiResponse);
        $webmeeting->save();
    }


}