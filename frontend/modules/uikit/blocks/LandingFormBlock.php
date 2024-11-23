<?php

namespace app\modules\uikit\blocks;

use app\modules\cms\utility\RegisterUser;
use app\modules\cmsapi\frontend\utility\cmspageblock\CmsLandingFormPageBlock;
use app\modules\uikit\BaseUikitFormBlock;
use app\modules\uikit\Module;
use backend\modules\eventsadmin\controllers\UserProfileController;
use backend\modules\eventsadmin\utility\EventsAdminUtility;
use open20\amos\admin\models\UserProfile;
use open20\amos\attachments\models\File;
use open20\amos\community\models\Community;
use open20\amos\core\record\RecordDynamicModel;
use open20\amos\events\AmosEvents;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventInvitation;
use open20\amos\events\models\EventType;
use open20\amos\events\utility\EventsUtility;
use luya\cms\frontend\blockgroups\MainGroup;
use Mustache_Engine;
use trk\uikit\Uikit;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class LandingFormBlock extends BaseUikitFormBlock
{
    public $cacheEnabled = false;

    public $errorMessages;

    /**
     * @inheritdoc
     */
    protected $component = "landingform";

    /**
     *
     * @return string
     */
    public function name()
    {
        return Module::t('landingform');
    }

    /**
     * @inheritdoc
     */
    public function blockGroup()
    {
        return MainGroup::class;
    }

    /**
     * @inheritdoc
     */
    public function icon()
    {
        return 'featured_play_list';
    }



    /**
     *
     * @param array $params
     * @return string
     */
    public function frontend(array $params = array())
    {
        $currentUrl = \Yii::$app->request->absoluteUrl;
        // questo pezzo di codice funziona solo per il click su spid dentro il form dell'evento
        if (strpos($currentUrl, 'spid-done') === false) {
            if (strpos($currentUrl, '?') !== false) {
                $currentUrl .= '&spid-done=1';
            } else {
                $currentUrl .= '?spid-done=1';
            }
//            \Yii::$app->session->remove('IDM');
        }
        \Yii::$app->session->set('redirect_url_spid', $currentUrl);
        ///---------------------

        $configs = $this->getValues();
        $cfg = Uikit::configs($configs);
        $result = $this->getPostResponse();
        $get = $_GET;


        if ($result == self::NO_POST || $result == self::ERROR_VALIDATION) {
            $user = null;
            $social = isset(Yii::$app->request->get()['social']) ? Yii::$app->request->get()['social'] : null;
            $socialdone = isset(Yii::$app->request->get()['social-done']) ? Yii::$app->request->get()['social-done'] : null;

            //redirect to endpoint
            if (!empty($social)) {
                return $this->getUserSocial($social);
            }
            if (!empty($socialdone)) {
                $user = \Yii::$app->session->get('socialAuthUserProfile');
            }

            if (!Uikit::element('data', $params, '')) {
                $configs["id"] = Uikit::unique($this->component);
                $params['data'] = $cfg;
                $params['debug'] = $this->config();
                $params['request'] = $this->request->post();
                $params['filters'] = $this->filters;
                $model = $this->buildModel();
                if (!is_null($user) && $user instanceof \Hybridauth\User\Profile) {
                    $var = $cfg['user_name_form_field'];
                    $model->$var = $user->firstName;

                    $var = $cfg['user_surname_form_field'];
                    $model->$var = $user->lastName;

                    $var = $cfg['to_form_field'];
                    $model->$var = $user->email;

                    $model->userSocial = Json::encode($user);
                    $model->datiRecuperatiDaSocial = 1;
                    $model->socialScelto = $socialdone;
                }


                $var = $cfg['user_name_form_field'];
                if (!empty($get['pName'])) {
                    $model->$var = $get['pName'];
                }
                $var = $cfg['user_surname_form_field'];
                if (!empty($get['pSurname'])) {
                    $model->$var = $get['pSurname'];
                }
                $var = $cfg['to_form_field'];
                if (!empty($get['pEmail'])) {
                    $getEmail = $get['pEmail'];
                    $getEmail = str_replace(' ', '+', $getEmail);
                    $model->$var = $getEmail;
                }

                $sessionIDM = \Yii::$app->session->get('IDM');
//                pr($sessionIDM);
                if ($sessionIDM) {
                    // var_dump($sessionIDM);
                    if (!empty($sessionIDM['nome'])) {
                        $var = $cfg['user_name_form_field'];
                        $model->$var = $sessionIDM['nome'];
                    }

                    if (!empty($sessionIDM['cognome'])) {
                        $var = $cfg['user_surname_form_field'];
                        $model->$var = $sessionIDM['cognome'];
                    }

                    if (!empty($sessionIDM['emailAddress'])) {
                        $var = $cfg['to_form_field'];
                        $model->$var = $sessionIDM['emailAddress'];
                    }
                    try {
                        if (!empty($sessionIDM['codiceFiscale']) && $sessionIDM['codiceFiscale'] != 'NON_DISPONIBILE') {
                            $model->fiscal_code = $sessionIDM['codiceFiscale'];
                        }
                    } catch (Exception $e) {

                    }
                }

                if ($result == self::ERROR_VALIDATION) {
                    $model->load($this->request->post());
                    foreach ($this->errorMessages as $attribute => $message){
                        if(is_array($message)){
                            $message = implode(', ', $message);
                        }
                        $model->addError($attribute, $message);
                    }
                }

                // if is logged
                if (!\Yii::$app->user->isGuest) {
                    $userProfile = UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
                    if ($userProfile) {
                        foreach ($model->attributes as $attribute => $value) {
                            $profileAttribute = CmsLandingFormPageBlock::getUserProfileAttribute($attribute);
                            if ($profileAttribute) {
                                $model->$attribute = CmsLandingFormPageBlock::parseValueForLuya($userProfile, $profileAttribute);
//                                print_r($profileAttribute."=>".$userProfile->$profileAttribute."###");
                                //   var_dump($userProfile->$profileAttribute.'-----');
                            }
                        }

                        if (array_key_exists('preference_tags', $model->attributes)) {
                            $model->preference_tags = \backend\modules\eventsadmin\controllers\UserProfileController::loadTagPreferences($userProfile);
                        }

                        if (array_key_exists('privacy', $model->attributes)) {
                            if ($model->hasProperty('privacy')) {
                                $model->privacy = true;
                            }
                        }

                        if (array_key_exists('privacy_2', $model->attributes)) {
                            if ($userProfile->privacy_2) {
                                $model->privacy_2 = true;
                            }
                        }

                        $var = $cfg['to_form_field'];
                        if ($model->hasProperty($var)) {
                            $model->$var = $userProfile->user->email;
                        }
                    }

                    if (\Yii::$app->request->isPost) {
                        $post = \Yii::$app->request->post();
                        if (!empty($post['update_tags'])) {
                            $preferencesTags = \Yii::$app->request->post('preferences_tags');
                            EventsAdminUtility::savePreferencesTags($userProfile, $preferencesTags);
                            if (isset(\Yii::$app->request->post('RecordDynamicModel')['privacy_2'])) {
                                $userProfile->privacy_2 = \Yii::$app->request->post('RecordDynamicModel')['privacy_2'];
                                $userProfile->save(false);
                            }
                        }
                    }
                }
                $params['model'] = $model;

            }
            if (!empty($cfg['community_id'])) {
                /** @var  $event Event */
                $event = $this->getEvent($cfg['community_id']);
                if ($event) {
                    $params['event'] = $event;
                    if ($event->eventType->event_type == EventType::TYPE_INFORMATIVE) {
                        return '';
                    }

                    $params['data'] = $this->getSocialRegConfig($event, $params['data']);
                    $currentPariticipants = $event->checkParticipantsQuantity();
                    if (!\Yii::$app->user->isGuest) {
                        if (EventsUtility::isEventParticipant($event->id, \Yii::$app->user->id)) {
                            $btnWebex = '';
                            $alreadyRegistered = "<h3>" . AmosEvents::t('amosevents', "Sei già registrato all'evento.") . "</h3>";
                            $redirectUrl = \Yii::$app->request->absoluteUrl;
                            $btnUnsubscribe = Html::a(AmosEvents::t('amosevents', 'Disiscriviti'), \Yii::$app->params['platform']['backendUrl'] . "/events/event-dashboard/confirm-unsubscribe?id=" . $event->id.'&urlRedirect='.$redirectUrl, [
                                'class' => 'btn btn-default btn-lg',
                                'data-confirm' => AmosEvents::t('amosevents', "Sei sicuro di cancellarti dall'evento?")
                            ]);

                            if ($event->eventType->webmeeting_webex && !empty($event->webmeeting_webex_id) && !empty($event->webMeetingWebex)) {
                                if (!$event->webexIsClosed()) {
                                    if ($event->canGoToWebexUrl()) {
                                        $btnWebex = Html::a(AmosEvents::t('amosevents', 'Avvia Videoconferenza'), $event->webMeetingWebex->web_link, [
                                            'class' => 'btn btn-primary btn-lg',
                                            'target' => '_blank',
                                            'title' => AmosEvents::t('amosevents', "Avvia la videoconferenza")
                                        ]);
                                    } else {
                                        $label = AmosEvents::t('amosevents', "La sessione di videoconferenza non è ancora iniziata. Quando il pulsante sarà attivo sarai rediretto all'area dedicata alla videoconferenza.");
                                        if ($event->webexIsClosed()) {
                                            $label = AmosEvents::t('amosevents', "La videoconferenza webex si è conclusa.");
                                        }
                                        $btnWebex = Html::a('Avvia Videoconferenza', 'javascript:void(0)', [
                                            'class' => 'btn btn-primary btn-lg',
                                            'disabled' => true,
                                            'title' => $label
                                        ]);
                                    }
                                }

                            }

                            $btnCompanions = '';
                            $btnCommunity = EventsUtility::getButtonCommunity($event, \Yii::$app->params['platform']['backendUrl'], 'btn btn-primary btn-lg');
                            $btnDownloadTicket = EventsUtility::getButtonDownloadTicket($event, \Yii::$app->params['platform']['backendUrl']);
                            if($event->enable_companions) {
                                $btnCompanions = Html::a(\Yii::t('site', "Gestisci partecipanti aggiuntivi"), \Yii::$app->params['platform']['backendUrl'].'/events/event/companions?id='. $event->id, [
                                    'title' => \Yii::t('site', "Gestisci partecipanti aggiuntivi"),
                                    'class' => 'btn btn-primary btn-lg'
                                ]);
                            }
                            $alreadyRegistered .= $btnWebex . $btnCommunity . $btnDownloadTicket .$btnCompanions. $btnUnsubscribe;
//                            $alreadyRegistered.= "<p style='color:#FFF;'>" . \Yii::t('app', "se desideri cancellarti da questo evento clicca <a href='{link}'>qui</a>", [
//                                    'link' => \Yii::$app->params['platform']['backendUrl'] . "/events/event-dashboard/confirm-unsubscribe?id=" . $event->id
//                                ]) . "</p>";

//                            pr($userProfile->attributes);die;
                            if ($userProfile && empty($userProfile->privacy_2)) {
                                $tag_preference_form = \Yii::$app->controller->renderPartial('@frontend/modules/uikit/views/landingform/preference_tags_form', ['model' => $model]);
                                $alreadyRegistered .= $tag_preference_form;
                            }
                            return $alreadyRegistered;
                        }

                    }
                    if (!$event->isSubscribtionsOpened()) {
                        return "<h3>" . AmosEvents::t('amosevents', "Registrazioni chiuse") . "</h3>";
                    }

                    if ($event->eventType->limited_seats == true
                        && ($currentPariticipants >= $event->seats_available)
                        && !$event->manage_waiting_list) {
                        return "<h3>" . AmosEvents::t('amosevents', "Registrazioni chiuse, i posti sono esauriti") . "</h3>";
                    }
                }
            }
            return $this->view->render($this->getViewFileName('php'), $params, $this);
        } else {
            if(\Yii::$app->language == 'en-GB'){
                $changeLangUrl='?language=en-GB';
            }
            if ($result == self::SAVED) {
                return $this->goTo($cfg['tks_page'].$changeLangUrl);
            } else {
                if ($result == self::SAVED_NOACCOUNT) {
                    return $this->goTo($cfg['tks_page_no_account'].$changeLangUrl);
                } else {
                    if ($result == self::SAVED_WAITING) {
                        return $this->goTo($cfg['tks_waiting_page'].$changeLangUrl);
                    } else {
                        if ($result == self::ALREADY_PRESENT) {
                            return $this->goTo($cfg['already_present_page'].$changeLangUrl);
                        }
                    }
                }
            }
        }
    }

    /**
     * @return int
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    private function getPostResponse()
    {
        $ret = self::NO_POST;
        $user = null;
        if ($this->request->isPost) {
            $configs = $this->getValues();
            $data = Uikit::configs($configs);
            $post = $this->request->post();

            if (isset($post[self::FORM_ID_FILED_NAME]) && $data['id'] == $post[self::FORM_ID_FILED_NAME]) {
                $model = $this->buildModel();

                if ($model->load($this->request->post()) && $model->validate() && $this->isNotOverflowCompanions($model, $data)) {
                    $var = $data['already_present_field'];
                    $attachmentsFields = $this->getAttachmentsFields($data);
                    if (!empty($attachmentsFields) || !$this->already_present($model, $data)) {
                        $model->save();
                        $connection = Yii::$app->db;
                        foreach ($model->attributes as $fld => $vlr) {
                            if (in_array($fld, $attachmentsFields)) {

                                $file = File::find()->andWhere(['itemId' => $model->id,
                                    'attribute' => $fld, 'model' => RecordDynamicModel::className(),
                                    'table_name_form' => $model->getTableName()])->one();
                                $fileUrlProvv = $file->getUrl();
                                if (strpos($fileUrlProvv, '/it') == 0) {
                                    $fileUrlProvv = substr($fileUrlProvv, 3);
                                }
                                $fileUrl = \Yii::$app->params['platform']['backendUrl'] . $fileUrlProvv;
                                $connection->createCommand()->update($model->getTableName(), [$fld => $fileUrl],
                                    "id = {$model->id}")->execute();
                                $model->$fld = $fileUrl;
                            }
                        }
                        if (!$this->hasError) {
                            $isWaiting = $this->isSeatOverflow($model, $data);

                            if ($data['register_on_platform']) {
                                $register = $this->buildRegisterUser($data);
                                $user = $register->registerToPlatform($model, $data, $isWaiting);
                            } else {
                                if ($data['send_mail']) {
                                    $this->sendMail($model, $data, $isWaiting);
                                }
                            }
                            if ($data['send_content_by_email'] && !empty($data['send_content_by_email_text'])) {
                                $this->sendContentByEmail($model, $data, $post);
                            }
                            $ret = $isWaiting ? self::SAVED_WAITING : (($data['register_on_platform'] == false || ($data['register_on_platform']
                                    && !is_null($user))) ? self::SAVED : self::SAVED_NOACCOUNT);
                        } else {
                            Uikit::trace($model->getErrors());
                            die();
                        }
                    } else {
                        $ret = self::ALREADY_PRESENT;
                    }
                } else {
                    $this->errorMessages = $model->getErrors();
//                    var_dump($model->getErrors());die;

                    $ret = self::ERROR_VALIDATION;

                }
            }
        }
        return $ret;
    }

    /**
     * @param $model
     * @param $data
     * @return bool
     * @throws InvalidConfigException
     */
    public function isNotOverflowCompanions($model, $data){

        $max_seats = $data['seats_available'];
        if (!empty($max_seats)) {
            $event = $this->getEvent($data['community_id']);
            if ($event) {
                $now_seats = $event->checkParticipantsQuantity();
            } else {
                $now_seats = $this->countSeats($model);
            }

            if($event->enable_companions){
                if(!empty(\Yii::$app->request->post('RecordDynamicModel')['n_companions'])){
                    $nCompanions =  \Yii::$app->request->post('RecordDynamicModel')['n_companions'];
                    $remainingSeats = $max_seats - $now_seats;
//                    pr($remainingSeats,'remaining');
//                    pr($max_seats,'$max_seats');
//                    pr($now_seats,'$now_seats');
//                    pr($nCompanions,'ncompanions ');
//                    die;
                    if(($remainingSeats) >= ($nCompanions + 1)){
                        return true;
                    }
                    $model->addError('n_companions',\Yii::t('site',"Hai inserito troppi partecipanti aggiuntivi, sono rimasti solo {x} posti", ['x' => $remainingSeats]));
                    return false;
                }

            }
        }
        return true;
    }


    /**
     * buildRegisterUser
     *
     * @param  array $data
     *
     * @return RegisterUser
     */
    private function buildRegisterUser(array $data)
    {
        $register = new RegisterUser();
        $register->setCommunityID($data['community_id']);
        $register->setFacilitatorID($data['facilitator_id']);
        $register->setEmailField($data['to_form_field']);
        $register->setNameField($data['user_name_form_field']);
        $register->setSurnameField($data['user_surname_form_field']);
        $register->setProvacyField($data['privacy_form_field']);
        $register->setSendCredential($data['send_credential']);
        $register->setEmail_after_login_text($data['email_after_login_text']);
        $register->setEmail_layout_after_login($data['email_layout_after_login']);
        $register->setEmail_subject_after_login($data['email_subject_after_login']);
        $register->setFrom_email($data['from_email']);
        $register->setEmail_after_login($data['email_after_login']);
        $register->setCreate_account_field($data['register_on_platform_field']);
        $register->setEmail_text_new_account($data['email_text_new_account']);

        foreach ($data['items'] as $item) {
            $getMethod = "get" . $item->field . "Field";
            if ($register->hasMethod($getMethod)) {
                $value = $register->$getMethod();
                if (empty($value)) {
                    $setMethod = "set" . $item->field . "Field";
                    if ($register->hasMethod($setMethod)) {
                        $register->$setMethod($item->field);
                    }
                }
            }
        }

        return $register;
    }

    /**
     * countSeats
     *
     * @param  RecordDynamicModel $model
     *
     * @return integer
     */
    private function countSeats($model)
    {
        return (new Query())
            ->from($model->tableName)
            ->count();
    }

    /**
     * @param $model
     * @param $data
     * @return bool
     * @throws InvalidConfigException
     */
    private function isSeatOverflow($model, $data)
    {
        $max_seats = $data['seats_available'];
        if (!empty($max_seats)) {
            $event = $this->getEvent($data['community_id']);
            if ($event) {
                $now_seats = $event->checkParticipantsQuantity();

            } else {
                $now_seats = $this->countSeats($model);
            }

            if ($now_seats >= $max_seats) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @param RecordDynamicModel $model
     */
    public function sendMail($model, $data, $waiting)
    {
        $m = new Mustache_Engine;

        $mailModule = Yii::$app->getModule("email");
        if (isset($mailModule)) {
            $from = $data['from_email'];

            if (!empty($data['email_layout'])) {
                $mailModule->defaultLayout = $data['email_layout'];
            }

            if ($waiting) {
                $text = $m->render($data['email_waiting_list_text'], $model);
            } else {
                $text = $m->render($data['email_text'], $model);
            }
            $ccn = [];
            if (!empty($data['ccn_email'])) {
                $ccn = [$data['ccn_email']];
            }

            $toField = $data['to_form_field'];
            $tos = [$model->$toField];
            $result = $mailModule->send($from, $tos, $data['email_subject'], $text, [], $ccn, []);
        }
        return $result;
    }

    /**
     * @param $community
     * @return mixed
     * @throws InvalidConfigException
     * @throws InvalidConfigException
     */
    public function checkIfCanRegister($community)
    {
        $context = $community->context;
        if ($context == 'open20\amos\events\models\Event') {
            $event = Event::find()->andWhere([
                'community_id' => $community->id])->one();
            $event = Event::find()->andWhere(['community_id' => $community->id])->one();
            return $event->canSubscribeAutomatic();
        }
    }

    /**
     *
     * @param type $model
     * @param type $data
     * @return boolean
     */
    private function already_present($model, $data)
    {
        $ret = false;

        $var = $data['already_present_field'];
        if ($data['check_in_event_already_present']) {
            $ret = !is_null($model->findOne("['" . $var . "' => '" . $model->$var . "']"));
        } else {
            $community = Community::findOne($data['community_id']);
            $context = $community->context;
            if ($context == 'open20\amos\events\models\Event') {
                $event = Event::find()->andWhere(['community_id' => $community->id])->one();
                $eventinvitation = EventInvitation::find()->andWhere([
                    'event_id' => $event->id, "$var" => $model->$var])->one();
                $ret = !is_null($eventinvitation);
            }
        }
        return $ret;
    }

    /**
     *
     * @param type $model
     * @param type $data
     * @param type $post
     * @return type
     */
    public function sendContentByEmail($model, $data, $post)
    {
        $m = new Mustache_Engine;

        $mailModule = Yii::$app->getModule("email");
        if (isset($mailModule)) {
            $from = $data['from_email'];

            if (!empty($data['email_layout'])) {
                $mailModule->defaultLayout = $data['email_layout'];
            }
            $content = $this->prepareContent($post, $data, $model);
            $params = ArrayHelper::toArray($model);
            $params['content'] = $content;
            $text = $m->render($data['send_content_by_email_text'], $params);

            $ccn = [];
            if (!empty($data['ccn_email'])) {
                $ccn = [$data['ccn_email']];
            }

            $toField = $data['to_form_field'];


            $result = $mailModule->send($from, $ccn, $data['email_subject'], $text, [], [], []);
        }
        return $result;
    }

    /**
     *
     * @param type $post
     * @param type $data
     * @param type $model
     * @return type
     */
    public function prepareContent($post, $data, $model)
    {
        $content = [];

        foreach ($post['RecordDynamicModel'] as $k => $v) {
            if (!empty($v)) {
                $content[] = ucfirst($k) . ": " . $v . "<br>";
            }
        }
        $attachmentsFields = $this->getAttachmentsFields($data);

        foreach ($model->attributes as $fld => $vlr) {
            if (in_array($fld, $attachmentsFields)) {
                $content[] = ucfirst($fld) . ": " . $model->$fld . "<br>";
            }
        }
        $text = implode("", $content);

        return $text;
    }

    /**
     *
     * @param type $data
     * @return type
     */
    public function getAttachmentsFields($data)
    {
        $fields = [];
        foreach ($data['items'] as $k => $v) {
            if ($v['type'] == 'attachmentsInput') {
                $fields[] = $v['field'];
            }
        }
        return $fields;
    }

    /**
     * @param $community_id
     * @return null
     * @throws InvalidConfigException
     */
    public function getEvent($community_id)
    {
        $event = null;
        $community = Community::findOne($community_id);
        if ($community) {
            $context = $community->context;
            if ($context == 'open20\amos\events\models\Event') {
                $event = Event::find()->andWhere(['community_id' => $community_id])->one();
            }
        }
        return $event;
    }

    /**
     * @param $event
     * @param $cfg
     * @return mixed
     */
    public function getSocialRegConfig($event, $cfg)
    {
        $landing = $event->eventLanding;
        if ($landing) {
            $cfg['facebook_reg'] = $landing->facebook_reg;
            $cfg['twitter_reg'] = $landing->twitter_reg;
            $cfg['linkedin_reg'] = $landing->linkedin_reg;
            $cfg['goolge_reg'] = $landing->goolge_reg;
            $cfg['apple_reg'] = $landing->apple_reg;
            $cfg['spid_cns_reg'] = $landing->spid_cns_reg;
        }
        return $cfg;
    }

}