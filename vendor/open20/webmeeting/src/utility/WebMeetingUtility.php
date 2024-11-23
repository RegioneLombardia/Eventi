<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Utility
 */

namespace open20\webmeeting\utility;

use open20\webmeeting\WebMeetingModule;
use open20\webmeeting\models\WebMeetingTimezoneModel;
use open20\webmeeting\models\WebMeetingHostUsers;
use open20\webmeeting\models\WebMeetingInviteeModel;

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\models\UserProfile;
use open20\amos\community\models\Community;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\user\User;
use open20\amos\core\utilities\Email;
use yii\data\ActiveDataProvider;

use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * WebMeetingutility
 */
class WebMeetingUtility extends \yii\base\BaseObject
{
    /**
     * Return an array of all time zones available
     *
     * @return type array
     */
    public static function getMeetingTimezone()
    {
        $timezone = WebMeetingTimezoneModel::find()->all();
        $tmp = [];
        foreach($timezone as $value) {
            $tmp[] = ['id' => $value->tz_value, 'value' => $value->timezone];
        }

        return $tmp;
    }

    /**
     * Return array with info about host users available on platform
     * @return type array
     */
    public static function getMeetingHostUsers()
    {
        $userHosts = WebMeetingHostUsers::find()->all();

        $tmp = [];
        foreach($userHosts as $user) {
            $tmp[] = ['id' => $user['email'], 'value' => $user['display_name']];
        }

        return $tmp;
    }

    /**
     * @param type $webMeetingModule
     * @param type $model
     * @param type $emails
     * @return type array of emails of removed invitees
     */
    public static function saveInvitedUsers($webMeetingModule, $model, $emails)
    {
        $oldInvitees = WebMeetingInviteeModel::find()
            ->select('email')
            ->andWhere(['webmeeting_id' => $model->id])
            ->asArray()
            ->all();

        $tmp = [];
        foreach($oldInvitees as $invitee) {
            $tmp[] = $invitee['email'];
        }

        $removeInvitees = array_diff($tmp, $emails);

        WebMeetingInviteeModel::deleteAll(['webmeeting_id' => $model->id]);
        $userIds = User::find()
            ->select('id')
            ->andWhere(['email' => $emails])
            ->asArray()
            ->all();

        $users = UserProfile::find()
            ->andWhere(['user_id' => $userIds])
            ->all();

        $invitee = $webMeetingModule::instance()->createModel('WebMeetingInviteeModel');
        foreach($users as $user) {
            if (empty($invitee->meeting_invitee_id)) {
                $invitee->setIsNewRecord(true);
                $invitee->id = null;
                $invitee->webmeeting_id = $model->id;
                $invitee->meeting_id = $model->meeting_id;
                $invitee->user_id = $user->user->id;
                $invitee->email = $user->user->email;
                $invitee->display_name = $user->getNomeCognome();
                $invitee->co_host = false;
                $invitee->save();
            }
        }

        return $removeInvitees;
    }

    /**
     * Prepare an array with all user to invitee in WebEx API format
     * @param type $emails
     * @param type $coHost
     * @return type array with all invitees in WebEx format
     */
    public static function prepareUsersToInvitee($emails, $coHost = false)
    {
        $invitees = [];
        if ($coHost === true) {
            $invitees = WebMeetingHostUsers::find()
                ->andWhere(['email' => $emails])
                ->all();
        } else {
            $userIds = User::find()->select('id')
                ->andWhere(['email' => $emails])
                ->asArray()
                ->all();
            $tmp = [];
            foreach($userIds as $uid) {
                $tmp[] = $uid['id'];
            }
            
            $invitees = UserProfile::find()
                ->andWhere(['user_id' => $tmp])
                ->all();
        }

        $tmp = [];
        foreach($invitees as $user) {
            $tmp[] = [
                'user_id' => $user->user_id,
                'email' => $coHost ? $user->email : $user->user->email,
                'display_name' => $coHost ? $user->displayName : $user->getNomeCognome(),
                'coHost' => $coHost
            ];
        }

        return $tmp;
    }

    /**
     * @param $emails
     * @param bool $coHost
     * @param bool $sendEmail
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function prepareInviteeForBulkInsert($emails, $coHost = false, $sendEmail = false)
    {
        $invitees = [];
        if ($coHost === true) {
            $invitees = WebMeetingHostUsers::find()
                ->andWhere(['email' => $emails])
                ->all();
        } else {
            $userIds = User::find()->select('id')
                ->andWhere(['email' => $emails])
                ->asArray()
                ->all();
            $tmp = [];
            foreach($userIds as $uid) {
                $tmp[] = $uid['id'];
            }

            $invitees = UserProfile::find()
                ->andWhere(['user_id' => $tmp])
                ->all();
        }

        $tmp = [];
        foreach($invitees as $user) {
            $tmp[] = [
                'email' => $coHost ? $user->email : $user->user->email,
                'displayName' => $coHost ? $user->displayName : $user->getNomeCognome(),
                'coHost' => $coHost,
                'sendEmail' => $sendEmail
            ];
        }

        return $tmp;
    }

    /**
     * @param type $model   istance of WebMeetingModel
     * @param type $invitee array with invitee data
     * @return boolean
     */
    public static function sendWebexMeetInvitationEmail($model, $invitee)
    {
        $subject = WebMeetingModule::_t('#webex_invitation_subject', [
            'title' => $model->title
        ]);

        $body = Email::renderMailPartial(
            '@vendor/open20/webmeeting/src/views/web-meeting/mail/meet-invitation',
            [
                'invitee' => $invitee,
                'title' => $model->title,
                'web_link' => Url::to(
                    Yii::$app->params['platform']['frontendUrl']
                    . WebMeetingModule::getWebexWidgetPage($model->id)
                ),
                'begin_date' => Yii::$app->formatter->asDatetime($model->start, null),
                'end_date' => Yii::$app->formatter->asDatetime($model->end, null),
                'agenda' => $model->agenda,
            ],
            $invitee['user_id']
        );

        return self::sendMail(
            [$invitee['email']],
            $subject,
            $body
        );
    }

    /**
     * @param type $model   istance of WebMeetingModel
     * @param type $invitee array with invitee data
     * @return boolean
     */
    public static function sendWebexMeetUpdatedEmail($model, $invitee)
    {
        $subject = WebMeetingModule::_t('#webex_invitation_subject', [
            'title' => $model->title
        ]);

        $body = Email::renderMailPartial(
            '@vendor/open20/webmeeting/src/views/web-meeting/mail/meet-invitation',
            [
                'invitee' => $invitee,
                'title' => $model->title,
                'web_link' => Url::to(
                    Yii::$app->params['platform']['frontendUrl']
                    . WebMeetingModule::getWebexWidgetPage($model->id)
                ),
                'begin_date' => Yii::$app->formatter->asDatetime($model->start, null),
                'end_date' => Yii::$app->formatter->asDatetime($model->end, null),
                'agenda' => $model->agenda,
            ],
            $invitee['user_id']
        );

        return self::sendMail(
            [$invitee['email']],
            $subject,
            $body
        );
    }

    /**
     * @param type $model   istance of WebMeetingModel
     * @param type $invitee array with invitee data
     * @return boolean
     */
    public static function sendWebexMeetDeletedEmail($model, $invitee)
    {
        $subject = WebMeetingModule::_t('#webex_invitation_subject', [
            'title' => $model->title
        ]);

        $body = Email::renderMailPartial(
            '@vendor/open20/webmeeting/src/views/web-meeting/mail/meet-deleted',
            [
                'invitee' => $invitee,
                'message' => WebMeetingModule::_t('webex_deleted_meet_email', [
                    'title' => $model->title,
                ]),
            ],
            $invitee['user_id']
        );

        return self::sendMail(
            [$invitee['email']],
            $subject,
            $body
        );
    }

    /**
     * @param type $model   istance of WebMeetingModel
     * @param type $invitee array with invitee data
     * @return boolean
     */
    public static function sendWebexMeetInviteeRemovedEmail($model, $invitee)
    {
        $subject = WebMeetingModule::_t('#webex_invitation_subject', [
            'title' => $model->title
        ]);

        $body = Email::renderMailPartial(
            '@vendor/open20/webmeeting/src/views/web-meeting/mail/meet-remove-invitee',
            [
                'invitee' => $invitee,
                'message' => WebMeetingModule::_t('#webex_removed_meet_invitee_email', [
                    'title' => $model->title,
                ]),
            ],
            $invitee['user_id']
        );

        return self::sendMail(
            [$invitee['email']],
            $subject,
            $body
        );
    }

    /**
     * @param type $emailTo email to send
     * @param type $subject email subject
     * @param type $body    email body
     * @param type $attachs list of attachments
     */
   public static function sendMail($emailTo, $subject, $body, $attachs = [])
    {
        $retVal = false;
        try {
            $retVal = Email::sendMail(
                Yii::$app->params['supportEmail'],
                $emailTo,
                $subject,
                $body,
                $attachs
            );
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }

        return $retVal;
    }

    /**
     *
     * @param type $scopee
     * @return type
     */
    public static function getCurrentScopeName($scope)
    {
        if (empty($scope)) {
            return 'Piattaforma';
        }

        $communityId = isset($scope['community'])
            ? $scope['community']
            : null;

        if ($communityId) {
            $community = Community::findOne($communityId);
            return $community->name;
        }

        return null;
    }

}