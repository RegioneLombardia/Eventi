<?php

namespace app\modules\cms\utility;

use app\modules\cmsapi\frontend\models\CmsMailAfterLogin;
use backend\modules\eventsadmin\controllers\UserProfileController;
use backend\modules\eventsadmin\utility\EventsAdminUtility;
use open20\amos\admin\models\TokenGroup;
use open20\amos\admin\models\UserProfile;
use open20\amos\admin\utility\UserProfileUtility;
use open20\amos\community\models\Community;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\core\record\RecordDynamicModel;
use open20\amos\core\user\User;
use open20\amos\events\AmosEvents;
use open20\amos\events\controllers\EventController;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventGroupReferentMm;
use open20\amos\events\models\EventInvitation;
use open20\amos\events\utility\EventsUtility;
use open20\amos\socialauth\models\SocialAuthUsers;
use open20\amos\socialauth\utility\SocialAuthUtility;
use Exception;
use Hybrid_User_Profile;
use Hybridauth\User\Profile;
use Mustache_Engine;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class RegisterUser extends BaseObject
{
    private $name_field = 'name';
    private $surname_field = 'surname';
    private $email_field = 'email';
    private $privacy_field = 'privacy';
    private $sex_field;
    private $age_field;
    private $county_field;
    private $city_field;
    private $telefon_field;
    private $fiscal_code_field;
    private $company_field;
    private $community_id;
    private $facilitator_id = null;
    private $send_credential = false;
    public $is_waiting = false;
    private $create_account_field;
    private $email_layout_after_login;
    private $from_email;
    private $email_subject_after_login;
    private $email_after_login_text;
    private $email_after_login;
    private $email_text_new_account;

    public function getEmail_after_login()
    {
        return $this->email_after_login;
    }

    public function setEmail_after_login($email_after_login)
    {
        $this->email_after_login = boolval($email_after_login);
    }

    public function getNameField()
    {
        return $this->name_field;
    }

    public function setNameField($name)
    {
        if (!empty($name)) {
            $this->name_field = $name;
        }
    }

    public function getSurnameField()
    {
        return $this->surname_field;
    }

    public function setSurnameField($name)
    {
        if (!empty($name)) {
            $this->surname_field = $name;
        }
    }

    public function getEmialField()
    {
        return $this->email_field;
    }

    public function setEmailField($name)
    {
        if (!empty($name)) {
            $this->email_field = $name;
        }
    }

    public function getPrivacyField()
    {
        return $this->privacy_field;
    }

    public function setProvacyField($name)
    {
        if (!empty($name)) {
            $this->privacy_field = $name;
        }
    }

    public function getCommunityID()
    {
        return $this->community_id;
    }

    public function setCommunityID($id)
    {
        if (!empty($id)) {
            $this->community_id = $id;
        }
    }

    public function getFacilitatorID()
    {
        return $this->facilitator_id;
    }

    public function setFacilitatorID($id)
    {
        $this->facilitator_id = $id;
    }

    public function getSendCredential()
    {
        return $this->send_credential;
    }

    public function setSendCredential($send)
    {
        $this->send_credential = $send;
    }

    public function getCreate_account_field()
    {
        return $this->create_account_field;
    }

    public function setCreate_account_field($create_account_field)
    {
        $this->create_account_field = $create_account_field;
    }

    public function getEmail_layout_after_login()
    {
        return $this->email_layout_after_login;
    }

    public function getFrom_email()
    {
        return $this->from_email;
    }

    public function getEmail_subject_after_login()
    {
        return $this->email_subject_after_login;
    }

    public function getEmail_after_login_text()
    {
        return $this->email_after_login_text;
    }

    public function setEmail_layout_after_login($email_layout_after_login)
    {
        $this->email_layout_after_login = $email_layout_after_login;
    }

    public function setFrom_email($from_email)
    {
        $this->from_email = $from_email;
    }

    public function setEmail_subject_after_login($email_subject_after_login)
    {
        $this->email_subject_after_login = $email_subject_after_login;
    }

    public function setEmail_after_login_text($email_after_login_text)
    {
        $this->email_after_login_text = $email_after_login_text;
    }

    public function getEmail_text_new_account()
    {
        return $this->email_text_new_account;
    }

    public function setEmail_text_new_account($email_text_new_account)
    {
        $this->email_text_new_account = $email_text_new_account;
    }

    public function getSexField()
    {
        return $this->sex_field;
    }

    public function getAgeField()
    {
        return $this->age_field;
    }

    public function getCountyField()
    {
        return $this->county_field;
    }

    public function getCityField()
    {
        return $this->city_field;
    }

    public function getTelefonField()
    {
        return $this->telefon_field;
    }

    public function getFiscalCodeField()
    {
        return $this->fiscal_code_field;
    }

    public function getCompanyField()
    {
        return $this->company_field;
    }

    public function setSexField($sex_field)
    {
        $this->sex_field = $sex_field;
    }

    public function setAgeField($age_field)
    {
        $this->age_field = $age_field;
    }

    public function setCountyField($county_field)
    {
        $this->county_field = $county_field;
    }

    public function setCityField($city_field)
    {
        $this->city_field = $city_field;
    }

    public function setTelefonField($telefon_field)
    {
        $this->telefon_field = $telefon_field;
    }

    public function setFiscalCodeField($fiscal_code_field)
    {
        $this->fiscal_code_field = $fiscal_code_field;
    }

    public function setCompanyField($company_field)
    {
        $this->company_field = $company_field;
    }

    /**
     * @param $model
     * @return null
     * @throws InvalidConfigException
     */
    public function registerToPlatform($model, $data, $isWaiting)
    {
        $emailField = $this->getEmialField();

        $giaRegistratoInPiattaforma = 0;
        $user = $this->isEmailRegisteredInPoi($model->$emailField);
        if (!is_null($user)) {
            $giaRegistratoInPiattaforma = 1;
            if ($data['send_mail']) {
                $community = Community::findOne($this->getCommunityID());
                $this->sendMail($model, $data, $isWaiting, $user, $isWaiting);
            }
        }
        $user = $this->RegisterUserToPlatform($model,
            $giaRegistratoInPiattaforma, $data, $isWaiting);
        return $user;
    }

    /**
     * @param $model
     * @param $giaRegistratoInPiattaforma
     * @return null
     * @throws InvalidConfigException
     */
    public function RegisterUserToPlatform($model, $giaRegistratoInPiattaforma,
                                           $data, $isWaiting = false)
    {
        $nameField = $this->getNameField();
        $surnameField = $this->getSurnameField();
        $emailField = $this->getEmialField();
        $privacyField = $this->getPrivacyField();

        /** @var  $community  Community */
        $community = Community::findOne($this->getCommunityID());
        $user = null;
        $accept_to_create_account = true;
        if ($data['register_on_platform_addicted']) {
            $cr_act_field = $this->getCreate_account_field();
            if (!empty($cr_act_field)) {
                $accept_to_create_account = boolval($model->$cr_act_field);
            }
        }
        if ($accept_to_create_account) {
            // creo un nuovo utente
            if ((empty($giaRegistratoInPiattaforma) || $giaRegistratoInPiattaforma == 0)) {
                UserProfileUtility::createNewAccount($model->$nameField,
                    $model->$surnameField, $model->$emailField, 1,
                    $this->getSendCredential());
                $user = User::find()->andWhere(['email' => $model->$emailField])->one();
                if ($user) {
                    if (!$this->getSendCredential()) {
                        $user->generatePasswordResetToken();
                        $user->save(false);
                    }
                    /** @var  $profile UserProfile */
                    $profile = $user->userProfile;
                    $profile->privacy_2 = $model->privacy_2;

                    $preferencesTags = \Yii::$app->request->post('preferences_tags');
                    EventsAdminUtility::savePreferencesTags($profile, $preferencesTags);

                    $profile->facilitatore_id = $this->getFacilitatorID();
                    $profile->first_access_redirect_url = !empty($this->getCommunityID())
                        ? '/community/join?id=' . $this->getCommunityID() : '';
                    if ($this->getEmail_after_login()) {
                        $cms_mail_after_login = $this->saveCmsMailAfterLogin($model);
                        $profile->first_access_redirect_url = \Yii::$app->params['platform']['frontendUrl'] . '/api/1/send-mail-after-login?id=' . $cms_mail_after_login->id . '&redirect=/community/join?id=' . $this->getCommunityID();
                    }
                    if (!$this->getSendCredential()) {
                        if ($data['send_mail']) {
                            $this->sendNewAccountMail($user, $model, $data, $isWaiting);
                        }
                    }

                    if($community) {
                        $event = Event::find()->andWhere(['community_id' => $community->id])->one();
                        if ($event) {
                            $profile->dg_of_registration = $event->event_group_referent_id;
                        }
                    }
                    $profile->user_profile_role_id = 7;
                    $profile->user_profile_role_other = '';
                    $profile->created_by = $profile->user_id;
                    $profile->date_privacy = date('Y-m-d H:i:d');
                    $profile->privacy = !empty($privacyField) ? $model->$privacyField
                        : 0;
                    $this->setUserProfileMoreFields($profile, $data, $model);
                    $profile->save(false);
                    $this->registerToCommunity($community, $user, $isWaiting);

                    //associo il social all'utente
                    if (!empty($user) && !empty($model->userSocial)) {
                        $userSocial = Json::decode($model->userSocial);
                        $socialProfile = $this->getClassHybridUserProfile($userSocial);
                        $this->createSocialUser($user->userProfile,
                            $socialProfile, $model->socialScelto);
                    }

                    $spidData = \Yii::$app->session->get('IDM');
                    if (!empty($user) && $spidData) {
                        $createdSpidUser = SocialAuthUtility::createIdmUser($spidData, $user->id);
                    }
                }
            } else {
                $user = User::find()->andWhere(['email' => $model->$emailField])->one();
                if ($user) {
                    $profile = $user->userProfile;
                    $this->registerToCommunity($community, $user, $isWaiting);
                    $this->setUserProfileMoreFields($profile, $data, $model);
                    $profile->privacy_2 = $model->privacy_2;

                    $preferencesTags = \Yii::$app->request->post('preferences_tags');
                    EventsAdminUtility::savePreferencesTags($profile, $preferencesTags);
                    $profile->save(false);
                }
            }
//            $model->user_id = $user->id;
//            $model->save();
        }
        $ok = $this->registerInvitation($user->id, $community, $model);
        if($ok) {
            $this->assignAutomaticSeat($user->id, $community);
            $this->assignToDg($user->id, $community, $model);
        }
        EventsUtility::setCookieForCache();
        return $user;
    }



    /**
     *
     */
    public function setFieldTypes()
    {
        $this->setAgeField('age');
        $this->setCityField('city');
        $this->setCountyField('country');
        $this->setCompanyField('company');
        $this->setFiscalCodeField('fiscal_code');
        $this->setSexField('sex');
        $this->setTelefonField('telefon');
    }

    /**
     *
     * @param UserProfile $userprofile
     */
    protected function setUserProfileMoreFields(UserProfile $userprofile,
                                                array $data, $model)
    {
        $this->setFieldTypes();
        if (!empty($this->getAgeField())) {
            $age = $this->getAgeField();
            if (empty($userprofile->user_profile_age_group_id) && !empty($model->$age)) {
                $userprofile->user_profile_age_group_id = $model->$age;
            }
        }
        if (!empty($this->getCityField())) {
            $city = $this->getCityField();
            if (empty($userprofile->nascita_comuni_id) && !empty($model->$city)) {
                $userprofile->nascita_comuni_id = $model->$city;
            }
        }
        if (!empty($this->getCountyField())) {
            $conty = $this->getCountyField();
            if (empty($userprofile->nascita_province_id) && !empty($model->$conty)) {
                $userprofile->nascita_province_id = $model->$conty;
            }
        }
        if (!empty($this->getCompanyField())) {
            $company = $this->getCompanyField();
            if (empty($userprofile->azienda) && !empty($model->$company)) {
                $userprofile->azienda = $model->$company;
            }
        }

        if (!empty($this->getFiscalCodeField())) {
            $code_f = $this->getFiscalCodeField();
            if (empty($userprofile->codice_fiscale) && !empty($model->$code_f)) {
                $userprofile->codice_fiscale = $model->$code_f;
            }
        }

        if (!empty($this->getSexField())) {
            $sex = $this->getSexField();
            if (empty($userprofile->sesso) && !empty($model->$sex)) {
                $userprofile->sesso = $model->$sex;
            }
        }

        if (!empty($this->getTelefonField())) {
            $telefon = $this->getTelefonField();
            if (empty($userprofile->telefono) && !empty($model->$telefon)) {
                $userprofile->telefono = $model->$telefon;
            }
        }
    }

    /**
     * @param $community
     * @param $user
     * @param bool $isWaiting
     * @return bool
     * @throws InvalidConfigException
     */
    public function registerToCommunity($community, $user, $isWaiting = false)
    {
        if ($community) {
            $moduleCommunity = Yii::$app->getModule('community');
            if ($moduleCommunity) {
                //subscribe to community father if is time period
                $this->subscribeToCommunityFather($user, $moduleCommunity);

                $count = CommunityUserMm::find()->andWhere(['user_id' => $user->id, 'community_id' => $community->id])->count();
                if ($count == 0) {
                    $context = $community->context;
                    if ($context == 'open20\amos\events\models\Event') {
                        $role = Event::EVENT_PARTICIPANT;
                    } else {
                        $role = CommunityUserMm::ROLE_PARTICIPANT;
                    }

                    if ($isWaiting) {
                        $status = CommunityUserMm::STATUS_WAITING_OK_COMMUNITY_MANAGER;
                    } else {
                        $status = CommunityUserMm::STATUS_ACTIVE;
                    }
                    $moduleCommunity->createCommunityUser($community->id, $status, $role, $user->id);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $user
     * @param $moduleCommunity
     * @throws InvalidConfigException
     */
    public function subscribeToCommunityFather($user, $moduleCommunity)
    {
        /** @var  $event Event */
        $event = $this->getEvent();

        if ($event && $event->is_time_period) {
            $eventFather = Event::findOne($event->event_id);
            if ($eventFather) {

                $count = CommunityUserMm::find()->andWhere(['user_id' => $user->id, 'community_id' => $eventFather->community_id])->count();
                if ($count == 0) {
                    $status = CommunityUserMm::STATUS_ACTIVE;
                    $role = Event::EVENT_PARTICIPANT;
                    $moduleCommunity->createCommunityUser($eventFather->community_id, $status, $role, $user->id);
                }
            }

        }
    }

    /**
     * @param $array
     * @return Hybrid_User_Profile
     */
    public function getClassHybridUserProfile($array)
    {
        $socialProfile = new \Hybridauth\User\Profile();
        foreach ($array as $key => $value) {
            $socialProfile->{$key} = $value;
        }
        return $socialProfile;
    }

    /**
     * @param $email
     */
    public function isEmailRegisteredInPoi($email)
    {
        $user = User::find()->andWhere(
            ['LIKE', 'email', $email]
        )->one();
        return $user;
    }

    /**
     * @param UserProfile $userProfile
     * @param Profile $socialProfile
     * @param $provider
     * @return bool|SocialAuthUsers
     */
    public function createSocialUser($userProfile, Profile $socialProfile, $provider)
    {
        try {
            /**
             * @var $socialUser SocialAuthUsers
             */
            $socialUser = new SocialAuthUsers();

            /**
             * @var $socialProfileArray array User profile from provider
             */
            $socialProfileArray = (array)$socialProfile;
            $socialProfileArray['provider'] = $provider;
            $socialProfileArray['user_id'] = $userProfile->user_id;

            /**
             * If all data can be loaded to new record
             */
            if ($socialUser->load(['SocialAuthUsers' => $socialProfileArray])) {
                /**
                 * Is valid social user
                 */
                if ($socialUser->validate()) {
                    $socialUser->save();
                    return $socialUser;
                } else {
                    \Yii::$app->session->addFlash('danger',
                        \Yii::t('amossocialauth',
                            'Unable to Link The Social Profile'));
                    return false;
                }
            } else {
                \Yii::$app->session->addFlash('danger',
                    \Yii::t('amossocialauth',
                        'Invalid Social Profile, Try again'));
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * @param $user_id
     * @param $community
     * @return null
     * @throws InvalidConfigException
     */
    public function assignAutomaticSeat($user_id, $community)
    {
        $context = $community->context;
        if ($context == 'open20\amos\events\models\Event') {
            $event = Event::find()->andWhere(['community_id' => $community->id])->one();
            if ($event->seats_management) {
                $seat = $event->assignAutomaticSeats($user_id);
                return $seat;
            }
        }
        return null;
    }

    /**
     * @param $user_id
     * @param $community
     * @param $model
     * @return bool
     * @throws InvalidConfigException
     */
    public function assignToDg($user_id, $community, $model)
    {
        $context = $community->context;
        if ($context == 'open20\amos\events\models\Event') {
            $event = Event::find()->andWhere(['community_id' => $community->id])->one();
            if ($event && $event->event_group_referent_id) {
                $member = new EventGroupReferentMm();
                $member->user_id = $user_id;
                $member->event_group_referent_id = $event->event_group_referent_id;
                $member->exclude_from_query = 0;
                $member->save(false);
                return true;
            }
        }
    }

    /**
     * @param $user_id
     * @param $community
     * @param $model
     * @return bool
     * @throws InvalidConfigException
     */
    public function registerInvitation($user_id, $community, $model)
    {
        $context = $community->context;
        if ($context == 'open20\amos\events\models\Event') {
            $event = Event::find()->andWhere(['community_id' => $community->id])->one();
            $gdpr = [];

            $eventInvitation = EventInvitation::find()
                ->andWhere(['event_id' =>$event->id, 'user_id' => $user_id])
                ->andWhere(['deleted_at' => null])->one();

            if(empty($eventInvitation)) {
                //ADD PARTICIPANT
                $nameField = $this->getNameField();
                $surnameField = $this->getSurnameField();
                $emailField = $this->getEmialField();

                $dataParticipant ['nome'] = $model->$nameField;
                $dataParticipant ['cognome'] = $model->$surnameField;
                $dataParticipant ['email'] = $model->$emailField;
                $eventControllers = new EventController('event',
                    \Yii::$app->getModule('events'));
                $invitation = $eventControllers->addParticipant($event->id, $dataParticipant, $user_id, $gdpr);

                //ADD COMPANIONS
                $n = 0;
                if (!empty(\Yii::$app->request->post('RecordDynamicModel')['n_companions'])) {
                    $n = \Yii::$app->request->post('RecordDynamicModel')['n_companions'];
                }

                $enableCompanions = \Yii::$app->request->post('enable_companions');
                if ($invitation && $event->enable_companions && !empty($n) && $enableCompanions) {
                    $companions = $invitation->generateCompanions($n);
                    foreach ($companions as $companion) {
                        $eventControllers->addCompanion($event->id, $invitation, $companion);
                    }
                }
                \open20\amos\core\models\UserActivityLog::registerLog(AmosEvents::t('amosevents', 'Registrazione ad un evento'), $event, Event::LOG_TYPE_SUBSCRIBE_EVENT, null, $user_id);

                return true;
            }
        }
        return false;
    }

    /**
     *
     * @param type $model
     * @param type $data
     */
    public function saveCmsMailAfterLogin($model)
    {
        $appLink = \Yii::$app->params['platform']['backendUrl'] . '/';
        $link = $appLink . 'community/join?id=' . $this->community_id;

        $m = new Mustache_Engine;
        $cms_after = new CmsMailAfterLogin();

        $cms_after->layout_email = $this->getEmail_layout_after_login();
        $cms_after->email_from = $this->getFrom_email();
        $toField = $this->email_field;
        $tos = $model->$toField;
        $cms_after->email_to = $tos;
        $cms_after->subject = $this->getEmail_subject_after_login();
        $params = ArrayHelper::toArray($model);
        $params['link'] = $link;
        $text = $m->render($this->getEmail_after_login_text(),
            $params);
        $cms_after->body = $text;
        $cms_after->save(false);
        return $cms_after;
    }

    /**
     *
     * @param RecordDynamicModel $model
     */
    public function sendMail($model, $data, $waiting, $user = null, $isWaiting = null)
    {
        $linkToken = "";
        $event = $this->getEvent();
        $mailup = false;

        $m = new Mustache_Engine;
        $result = "";
        if (!is_null($user) && !empty($data['token_group_string_code'])) {

            $linkToken = $this->getLinkWithToken($user->id,
                $data['token_group_string_code']);
        }
        $params = ArrayHelper::toArray($model);
        $params['token'] = $linkToken;
        if ($event) {
            $linkToken .= "&url_previous=" . urlencode(EventsUtility::getUrlLanding($event));
            if ($event->multilanguage) {
                list($subject, $text, $mailup) = $this->multilanguageEmail($event, $isWaiting, $user);
                $data['email_subject'] = $subject;
            } else {
                $eventTemplates = $event->eventEmailTemplates;
                if ($eventTemplates) {
                    if ($isWaiting) {
                        $text = $eventTemplates->info_waiting_list;
                        $subject = $eventTemplates->info_waiting_list_subject;

                    } else {
                        $text = $eventTemplates->confirm_registration;
                        $subject = $eventTemplates->confirm_registration_subject;
                        if ($event->eventType->webmeeting_webex) {
                            $text = $eventTemplates->webmeeting_webex_confirm_reg;
                            $subject = $eventTemplates->webmeeting_webex_confirm_reg_subject;
                        }
                    }

                    $subject = \open20\amos\events\utility\EventMailUtility::parseEmailWithParams($event, $user->id, $subject, false);
                    $text = \open20\amos\events\utility\EventMailUtility::parseEmailWithParams($event, $user->id, $text);

                    $data['email_subject'] = $subject;
                    $mailup = true;
                }
            }
        } else {
            if ($isWaiting) {
                $text = $m->render($data['email_waiting_list_text'], $params);
            } else {
                $text = $m->render($data['email_text'], $params);
            }
        }
//        pr($text, 'fdfd');
        $result = $this->baseSendMail($model, $data, $text, $mailup, $event, $isWaiting);

        return $result;
    }

    /**
     * @param $event
     * @param $isWaiting
     * @param $user
     * @return array
     * @throws InvalidConfigException
     */
    public function multilanguageEmail($event, $isWaiting, $user)
    {
        $currentLang = \Yii::$app->language;
        \Yii::$app->language = 'it-IT';
        $eventTemplates = $event->eventEmailTemplates;
        if ($eventTemplates) {
//            pr($eventTemplates->attributes, 'it');
            if ($isWaiting) {
                $text = $eventTemplates->info_waiting_list;
                $subject = $eventTemplates->info_waiting_list_subject;

            } else {
                $text = $eventTemplates->confirm_registration;
                $subject = $eventTemplates->confirm_registration_subject;
                if ($event->eventType->webmeeting_webex) {
                    $text = $eventTemplates->webmeeting_webex_confirm_reg;
                    $subject = $eventTemplates->webmeeting_webex_confirm_reg_subject;
                }
            }

            $subject = \open20\amos\events\utility\EventMailUtility::parseEmailWithParams($event, $isWaiting->id, $subject, false);
            $text = \open20\amos\events\utility\EventMailUtility::parseEmailWithParams($event, $user->id, $text);
            // ---------------------
            \Yii::$app->language = 'en-GB';
            $eventTemplatesEng = \open20\amos\events\models\EventEmailTemplates::find()->andWhere(['event_id' => $event->id])->one();
//            pr($eventTemplatesEng->attributes, 'eng');

            $eventEng = Event::findOne($event->id);
            if ($isWaiting) {
                $textEng = $eventTemplatesEng->info_waiting_list;
            } else {
                $textEng = $eventTemplatesEng->confirm_registration;
            }
            $textEng = \open20\amos\events\utility\EventMailUtility::parseEmailWithParams($eventEng, $user->id, $textEng);

            $text .= "<hr><br>" . $textEng;

            $data['email_subject'] = $subject;
            $mailup = true;

            \Yii::$app->language = $currentLang;
            return [$subject, $text, $mailup];
        }
        \Yii::$app->language = $currentLang;
//die;
    }


    /**
     * @param $user
     * @param $model
     * @param $data
     * @param bool $isWaiting
     * @return bool|mixed
     * @throws InvalidConfigException
     */
    public function sendNewAccountMail($user, $model, $data, $isWaiting = false)
    {
        $m = new Mustache_Engine;
        $event = $this->getEvent();
        $mailup = false;

        $result = false;
        $linkToken = "";
        $appLink = Yii::$app->params['platform'] ['backendUrl'] . "/";
        if (!is_null($user) && !empty($data['token_group_string_code'])) {

            $linkToken = $this->getLinkWithToken($user->id,
                $data['token_group_string_code']);
        } else {
            $linkToken = $appLink . 'admin/security/insert-auth-data?token=' . $user->password_reset_token;
        }
        $params = ArrayHelper::toArray($model);
        $params['token'] = $linkToken;
        if ($event) {
            $linkToken .= "&url_previous=" . urlencode(EventsUtility::getUrlLanding($event));
            if ($event->eventType->webmeeting_webex) {
                list($subject, $text, $mailup) = $this->multilanguageEmailNewAccount($event, $isWaiting, $user, $model);
                $data['email_subject'] = $subject;
            } else {
                $eventTemplates = $event->eventEmailTemplates;
                if ($eventTemplates) {
                    if ($isWaiting) {
                        $text = $eventTemplates->info_waiting_list;
                        $subject = $eventTemplates->info_waiting_list_subject;

                    } else {
                        $text = $eventTemplates->confirm_registration;
                        $subject = $eventTemplates->confirm_registration_subject;
                        if ($event->eventType->webmeeting_webex) {
                            $text = $eventTemplates->webmeeting_webex_confirm_reg;
                            $subject = $eventTemplates->webmeeting_webex_confirm_reg_subject;
                        }
                    }

                    if (!empty($linkToken) && empty($model->userSocial) && empty(\Yii::$app->session->get('IDM'))) {
                        $linkReg = "<p>Per completare la registrazione <a href='$linkToken'>clicca qui</a></p>";
                        $linkReg .= "<p>In caso di problemi con il precedente link copia il seguente indirizzo ed incollalo nella barra indirizzo del tuo browser <a href='$linkToken'>.$linkToken.</a></p>";

                        if (strpos($text, "Gentile {NOME} {COGNOME},") >= 0) {
                            $text = str_replace("Gentile {NOME} {COGNOME},", "Gentile {NOME} {COGNOME},<br>" . $linkReg, $text);
                        } else {
                            $text .= $linkReg;
                        }
                    }
                    $subject = \open20\amos\events\utility\EventMailUtility::parseEmailWithParams($event, $user->id, $subject, false);
                    $text = \open20\amos\events\utility\EventMailUtility::parseEmailWithParams($event, $user->id, $text);

                    $data['email_subject'] = $subject;
                    $mailup = true;
                }
            }
        } else {
            $text = $m->render($this->email_text_new_account, $params);
        }
        $result = $this->baseSendMail($model, $data, $text, $mailup, $event, $isWaiting);
        return $result;
    }

    /**
     * @param $event
     * @param $isWaiting
     * @param $user
     * @param $model
     * @return array
     * @throws InvalidConfigException
     */
    public function multilanguageEmailNewAccount($event, $isWaiting, $user, $model)
    {
        $currentLang = \Yii::$app->language;
        \Yii::$app->language = 'it-IT';
        $eventTemplates = $event->eventEmailTemplates;
        if ($eventTemplates) {
            if ($isWaiting) {
                $text = $eventTemplates->info_waiting_list;
                $subject = $eventTemplates->info_waiting_list_subject;

            } else {
                $text = $eventTemplates->confirm_registration;
                $subject = $eventTemplates->confirm_registration_subject;
                if ($event->eventType->webmeeting_webex) {
                    $text = $eventTemplates->webmeeting_webex_confirm_reg;
                    $subject = $eventTemplates->webmeeting_webex_confirm_reg_subject;
                }
            }

            if (!empty($linkToken) && empty($model->userSocial) && empty(\Yii::$app->session->get('IDM'))) {
                $linkReg = "<p>Per completare la registrazione <a href='$linkToken'>clicca qui</a></p>";
                $linkReg .= "<p>In caso di problemi con il precedente link copia il seguente indirizzo ed incollalo nella barra indirizzo del tuo browser <a href='$linkToken'>.$linkToken.</a></p>";

                if (strpos($text, "Gentile {NOME} {COGNOME},") >= 0) {
                    $text = str_replace("Gentile {NOME} {COGNOME},", "Gentile {NOME} {COGNOME},<br>" . $linkReg, $text);
                } else {
                    $text .= $linkReg;
                }
            }
            $subject = \open20\amos\events\utility\EventMailUtility::parseEmailWithParams($event, $user->id, $subject, false);
            $text = \open20\amos\events\utility\EventMailUtility::parseEmailWithParams($event, $user->id, $text);

            $data['email_subject'] = $subject;
            $mailup = true;

            // ---------------------
            \Yii::$app->language = 'en-GB';
            $eventTemplatesEng = \open20\amos\events\models\EventEmailTemplates::find()->andWhere(['event_id' => $event->id])->one();
            if ($isWaiting) {
                $textEng = $eventTemplates->info_waiting_list;
            } else {
                $textEng = $eventTemplates->confirm_registration;
                if ($event->eventType->webmeeting_webex) {
                    $textEng = $eventTemplates->webmeeting_webex_confirm_reg;
                }
            }

            if (!empty($linkToken) && empty($model->userSocial) && empty(\Yii::$app->session->get('IDM'))) {
                $linkReg = "<p>Per completare la registrazione <a href='$linkToken'>clicca qui</a></p>";
                $linkReg .= "<p>In caso di problemi con il precedente link copia il seguente indirizzo ed incollalo nella barra indirizzo del tuo browser <a href='$linkToken'>.$linkToken.</a></p>";

                if (strpos($textEng, "Gentile {NOME} {COGNOME},") >= 0) {
                    $textEng = str_replace("Gentile {NOME} {COGNOME},", "Gentile {NOME} {COGNOME},<br>" . $linkReg, $textEng);
                } else {
                    $textEng .= $linkReg;
                }
            }
            \Yii::$app->language = $currentLang;

            $text .= "<hr><br>" . $textEng;
            return [$subject, $text, $mailup];

        }
        \Yii::$app->language = $currentLang;

    }

    /**
     * @param $model
     * @param $data
     * @param $message
     * @param bool $mailup
     * @param null $event
     * @return mixed
     */
    private function baseSendMail($model, $data, $message, $mailup = false, $event = null, $isWaiting = false)
    {
        $mailModule = Yii::$app->getModule("email");
        if (isset($mailModule)) {
            $from = $data['from_email'];
            if (empty($form)) {
                $from = Yii::$app->params['supportEmail'];
            }

            if (!empty($data['email_layout'])) {
                $mailModule->defaultLayout = $data['email_layout'];
            }

            $text = $message;
            $ccn = [];
            if (!empty($data['ccn_email'])) {
                $ccn = [$data['ccn_email']];
            }

            $toField = $data['to_form_field'];
            $tos = [$model->$toField];
            if ($mailup) {
                $footer = \open20\amos\events\models\EventEmailTemplates::FOOTER_TYPE_TAG_AND_UNSUBSCRIBE;
                if ($isWaiting) {
                    $footer = \open20\amos\events\models\EventEmailTemplates::FOOTER_TYPE_ALL_FOOTER;
                }
                $result = \open20\amos\events\utility\EventMailUtility::sendEmailTest($from, $tos, $data['email_subject'], $message, $event, $footer);
            } else {
                $result = $mailModule->send($from, $tos, $data['email_subject'],
                    $text, [], $ccn, []);
            }
        }
        return $result;
    }

    /**
     *
     * @param type $user_id
     * @param type $event_string
     * @return string
     */
    public function getLinkWithToken($user_id, $event_string)
    {
        $link = null;
        $tokengroup = TokenGroup::getTokenGroup($event_string);

        if ($tokengroup) {

            $tokenUser = $tokengroup->generateSingleTokenUser($user_id);
            if (!empty($tokenUser)) {
                $link = $tokenUser->getBackendTokenLink();
            }
        }
        return $link;
    }

    /**
     * @return null
     * @throws InvalidConfigException
     */
    public function getEvent()
    {
        $event = null;
        $community_id = $this->getCommunityID();
        $community = Community::findOne($community_id);
        if ($community) {
            $context = $community->context;
            if ($context == 'open20\amos\events\models\Event') {
                $event = Event::find()->andWhere(['community_id' => $community_id])->one();
            }
        }
        return $event;
    }
}