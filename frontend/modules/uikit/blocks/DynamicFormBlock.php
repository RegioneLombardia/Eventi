<?php

namespace app\modules\uikit\blocks;

use app\modules\cms\utility\RegisterUser;
use app\modules\uikit\BaseUikitDynamicFormBlock;
use app\modules\uikit\BaseUikitFormBlock;
use app\modules\uikit\Module;
use backend\modules\operations\models\LandingFormEventTable;
use open20\amos\admin\models\UserProfile;
use open20\amos\core\record\RecordDynamicModel;
use open20\amos\core\user\User;
use \Hybridauth\User\Profile;
use luya\cms\frontend\blockgroups\MainGroup;
use Mustache_Engine;
use trk\uikit\Uikit;
use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use open20\amos\community\models\Community;
use open20\amos\events\models\Event;
use yii\validators\FileValidator;

class DynamicFormBlock extends BaseUikitDynamicFormBlock
{
    public $modelError;

    public $cacheEnabled = false;

    public $compiledValues = [];

    /**
     * @inheritdoc
     */
    protected $component = "dynamicform";

    /**
     *
     * @return string
     */
    public function name()
    {
        return Module::t('dynamicform');
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
     * @param array $params
     * @return \app\modules\uikit\User|mixed|string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function frontend(array $params = array())
    {
        $currentUrl = \Yii::$app->request->absoluteUrl;
        if (strpos($currentUrl, 'spid-done') === false) {
            if (strpos($currentUrl, '?') !== false) {
                $currentUrl .= '&spid-done=1';
            } else {
                $currentUrl .= '?spid-done=1';
            }
            \Yii::$app->session->remove('IDM');
        }
        \Yii::$app->session->set('redirect_url_spid', $currentUrl);

        $configs = $this->getValues();
        $cfg = Uikit::configs($configs);
        $result = $this->getPostResponse();

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
                if (!empty($cfg['privacy_form_field'])) {
                    $model->addRule($cfg['privacy_form_field'], 'required', ['requiredValue' => 1, 'message' => Yii::t('app', 'E\' obbligatorio aver preso visione dell’informativa sul trattamento dei dati personali')]);
                }

                if (!is_null($user) && $user instanceof \Hybridauth\User\Profile) {
                    $var = $cfg['user_name_form_field'];
                    $model->$var = $user->firstName;
                    $var = $cfg['user_surname_form_field'];
                    $model->$var = $user->lastName;

                    $var = $cfg['to_form_field'];
                    $model->$var = $user->email;
                    $model->userSocial = Json::encode($user);
                    $model->datiRecuperatiDaSocial = 1;
                    $model->socialScelto = $social;


                } else {
//                    if ($cfg['autofill_if_logged']) {
                    if (!\Yii::$app->user->isGuest) {
                        if (!empty($cfg['user_name_form_field'] && !empty($cfg['user_surname_form_field']) && !empty($cfg['to_form_field'])))
                            /** @var User $loggedUser */
                            $user = User::findOne(\Yii::$app->user->id);
                        if ($user) {
                            $userProfile = $user->getProfile();
                            $var = $cfg['user_name_form_field'];
                            $model->$var = $userProfile->nome;
                            $var = $cfg['user_surname_form_field'];
                            $model->$var = $userProfile->cognome;
                            $var = $cfg['to_form_field'];
                            $model->$var = $user->email;
                        }
                    }
//                    }
                }
                //var_dump($sessionIDM);
                if (\Yii::$app->request->get('spid-done')) {
                    $params = $this->loadSpidParams($model, $cfg, $params);
                }
                if ($result == self::ERROR_VALIDATION) {
                    $model->load($this->request->post());
                }

                if (!empty($this->modelError)) {
                    foreach ($this->modelError as $attribute => $error) {
                        $model->addError($attribute, $error[0]);
                    }
                }
                $params['model'] = $model;
                $params['data'] = $cfg;
            }


            $navItem = Yii::$app->menu->current->getModel();
            if ($navItem) {
                $layoutFile = $navItem->getAttribute('layout_file');
                if ($layoutFile == 'mainDesign') {
                    return $this->view->render('DynamicFormBlockBi', $params, $this);
                }

            }
//            pr(Yii::$app->menu->current->getModel()->getAttribute('layout_file'), 'layout');


            return $this->view->render($this->getViewFileName('php'), $params, $this);
        } else {
            if ($result == self::SAVED) {
                return $this->goTo($cfg['tks_page']);
            } else {
                if ($result == self::SAVED_NOACCOUNT) {
                    return $this->goTo($cfg['tks_page_no_account']);
                } else if ($result == self::SAVED_NOACCOUNT_WAITING) {
                    return $this->goTo($cfg['tks_page_no_account_wait_list']);
                } else {
                    if ($result == self::SAVED_WAITING) {
                        return $this->goTo($cfg['tks_waiting_page']);
                    } else {
                        if ($result == self::ALREADY_PRESENT) {
                            return $this->goTo($cfg['already_present_page']);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $model
     * @param $cfg
     * @param $params
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function loadSpidParams($model, $cfg, $params)
    {
        $sessionIDM = \Yii::$app->session->get('IDM');
        if ($sessionIDM) {
            // var_dump($sessionIDM);
            $readonlyAttributes = [];
            $var = $cfg['user_name_form_field'];
            $model->$var = $sessionIDM['nome'];
            if (!empty($sessionIDM['nome'])) {
                $readonlyAttributes[] = $var;
            }

            $var = $cfg['user_surname_form_field'];
            $model->$var = $sessionIDM['cognome'];
            if (!empty($sessionIDM['cognome'])) {
                $readonlyAttributes[] = $var;
            }

            $var = $cfg['to_form_field'];
            $model->$var = $sessionIDM['emailAddress'];

            try {
                $model->fiscal_code = $sessionIDM['codiceFiscale'];
            } catch (Exception $e) {

            }
            $params['readonlyAttributes'] = $readonlyAttributes;

            // if spid is already connected to an account ( 'idm' is not in session, e viene fatto il login)
        } elseif (!\Yii::$app->user->isGuest) {
            $userProfile = UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
            if ($userProfile) {
                $readonlyAttributes = [];
                $var = $cfg['user_name_form_field'];
                $model->$var = $userProfile->nome;
                $readonlyAttributes[] = $var;

                $var = $cfg['user_surname_form_field'];
                $model->$var = $userProfile->cognome;
                $readonlyAttributes[] = $var;

                $var = $cfg['to_form_field'];
                $model->$var = $userProfile->user->email;

                try {
                    $model->fiscal_code = $userProfile->codice_fiscale;
                } catch (Exception $e) {

                }
                $params['readonlyAttributes'] = $readonlyAttributes;
            }
            return $params;
        }
        return $params;
    }


    /**
     * @return int
     * @throws \yii\base\InvalidConfigException
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
                $this->popolateTableLandingFormEventTables($data);

                if (!empty($data['privacy_form_field'])) {
                    $model->addRule($data['privacy_form_field'], 'required', ['requiredValue' => 1, 'message' => Yii::t('app', 'E\' obbligatorio aver preso visione dell’informativa sul trattamento dei dati personali')]);
                }

                $cloneModel = clone $model;
                if ($model->load($this->request->post()) && $this->parsePostValues($model, $configs) && $this->validateForm($cloneModel, $configs)) {
                    $this->parseAfterValidate($model, $configs);
                    $model->created_at = date('Y-m-d H:i:s');
                    $model->deleted_at = null;
                    $var = $data['already_present_field'];
                    $attachmentsFields = $this->getAttachmentsFields($data);
                    if (!$this->already_present($model, $data)) {
                        $model->save();
                        $connection = Yii::$app->db;
                        if (!empty($attachmentsFields)) {
                            foreach ($model->attributes as $fld => $vlr) {
                                if (in_array($fld, $attachmentsFields)) {
                                    $file = \open20\amos\attachments\models\File::find()->andWhere(['item_id' => $model->id,
                                        'attribute' => $fld, 'model' => \open20\amos\core\record\RecordDynamicModel::className(),
                                        'table_name_form' => $model->getTableName()])->one();
                                    if (!empty($file)) {
                                        $fileUrlProvv = $file->getWebUrl();
                                        if (strpos($fileUrlProvv, '/it') == 0) {
                                            $fileUrlProvv = substr($fileUrlProvv, 3);
                                        }

                                        $fileUrl = \Yii::$app->params['platform']['backendUrl'] . $fileUrlProvv;
                                        $connection->createCommand()->update($model->getTableName(), [$fld => $fileUrl],
                                            "id = {$model->id}")->execute();
                                        $model->$fld = $fileUrl;
                                    }
                                }
                            }
                        }
                        if (!$this->hasError) {
                            $giaRegistratoInPiattaforma = false;
                            $isWaiting = $this->isSeatOverflow($model, $data);

                            if ($data['register_on_platform']) {
                                $register = $this->buildRegisterUser($data);
                                $user = $register->registerToPlatform($model, $data, $isWaiting);
                                $giaRegistratoInPiattaforma = $register->gia_registrato_in_piattaforma;
                            } else {
                                if ($data['send_mail']) {
                                    $this->sendMail($model, $data, $isWaiting);
                                }
                            }
                            if ($data['send_content_by_email'] && !empty($data['send_content_by_email_text'])) {
                                $this->sendContentByEmail($model, $data, $post);
                            }

                            if ($isWaiting) {
                                if ($data['register_on_platform'] == false || ($data['register_on_platform'] && $giaRegistratoInPiattaforma)) {
                                    $ret = self::SAVED_WAITING;
                                } else {
                                    $ret = self::SAVED_NOACCOUNT_WAITING;
                                }
                            } else {
                                $ret = ($data['register_on_platform'] == false || ($data['register_on_platform'] && $giaRegistratoInPiattaforma)) ? self::SAVED : self::SAVED_NOACCOUNT;
                            }

                        } else {
                            if (!empty($model->getErrors())) {
                                $this->modelError = $model->getErrors();
                            }
                            // Uikit::trace($model->getErrors());
                        }
                    } else {
                        $ret = self::ALREADY_PRESENT;
                    }
                } else {
                    if (!empty($cloneModel->getErrors())) {
                        $ret = self::ERROR_VALIDATION;
                        $this->modelError = $cloneModel->getErrors();
                    }
                    //  Uikit::trace($model->getErrors());
                }
            }
        }
        return $ret;
    }

    public function validateForm($cloneModel, $configs)
    {
        if ($cloneModel->load($this->request->post()) && $this->parsePostValues($cloneModel, $configs) && $cloneModel->validate()) {
            return true;
        }
        return false;
    }

    /**
     * @param $model
     * @param $configs
     * @return bool
     */
    public function parsePostValues($model, $configs)
    {
        foreach ($configs['items'] as $item) {
            if ($item['type'] == 'dynamicInputText') {
                $attribute = $item['field'];
                $tmp = [];
                foreach ($model->$attribute as $dynamicAttribute => $values) {
                    foreach ($values as $key => $val) {
                        $tmp[$key][$dynamicAttribute] = $val;
                    }
                }
                if (!empty($tmp)) {
                    $model->$attribute = json_encode($tmp);
                }
            }
        }
        return true;
    }

    /**
     * @param $model
     * @param $configs
     * @return bool
     */
    public function parseAfterValidate($model, $configs)
    {
        foreach ($configs['items'] as $item) {
            $attribute = $item['field'];
            if ($item['type'] == 'selectMultiple') {
                if (!empty($model->$attribute)) {
                    $model->$attribute = json_encode($model->$attribute);
                }
            }
        }
        return true;
    }

    /**
     * @param $data
     * @param $event_id
     * @throws \yii\base\InvalidConfigException
     */
    public function popolateTableLandingFormEventTables($data)
    {
        if (!empty($data['table']) && !empty($data['community_id'])) {
            $table = $data['table'];
            $event = $this->getEvent($data['community_id']);
            if ($event) {
                $linkTable = LandingFormEventTable::find()->andWhere(['event_id' => $event->id])->one();
                if (empty($linkTable)) {
                    $linkTable = new LandingFormEventTable();
                    $linkTable->event_id = $event->id;
                }
                $linkTable->table = $table;
                $linkTable->save(false);
            }
        }

    }


    /**
     * buildRegisterUser
     *
     * @param array $data
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
        $register->setNot_email_after_login_wait_list($data['not_email_after_login_wait_list']);
        $register->setCreate_account_field($data['register_on_platform_field']);
        $register->setEmail_text_new_account($data['email_text_new_account']);
        $register->setEmail_text_new_account_wait_list($data['email_text_new_account_wait_list']);
        return $register;
    }

    /**
     * countSeats
     *
     * @param RecordDynamicModel $model
     *
     * @return integer
     */
    private function countSeats($model)
    {
        try {
            return (new Query())
                ->from($model->tableName)
                ->andWhere(['OR', ['deleted_at' => null], ['deleted_at' => '']])
                ->count();
        } catch (\yii\db\Exception $e) {
            return (new Query())
                ->from($model->tableName)
                ->count();
        }

    }

    /**
     * isSeatOverflow
     *
     * @param RecordDynamicModel $model
     * @param boolean $data
     *
     * @return void
     */
    private function isSeatOverflow($model, $data)
    {
        $max_seats = $data['seats_available'];
        if (!empty($max_seats)) {

            $now_seats = $this->countSeats($model);
            if ($now_seats > $max_seats) {
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
        $post = $this->request->post();

        $mailModule = Yii::$app->getModule("email");
        if (isset($mailModule)) {
            $from = $data['from_email'];

            if (!empty($data['email_layout'])) {
                $mailModule->defaultLayout = $data['email_layout'];
            }

            $content = $this->prepareContent($post, $data, $model);
            $params = \yii\helpers\ArrayHelper::toArray($model);
            $params['content'] = $content;
            $params['field_id'] = hash('md5',$model->id);
            $params = ArrayHelper::merge($params, $this->compiledValues);
//
            if ($waiting) {
                $text = $m->render($data['email_waiting_list_text'], $params);
            } else {
                $text = $m->render($data['email_text'], $params);
            }

            $ccn = [];
            if (!empty($data['ccn_email'])) {
                if (empty($data['send_content_by_email'])) {
                    $explode = explode(';', $data['ccn_email']);
                    $ccn = array_map('trim', $explode);
                }
            }

            $toField = $data['to_form_field'];
            $tos = $model->$toField;
            $result = $mailModule->send($from, $tos, $data['email_subject'], $text, [], $ccn, []);
        }
        return $result;
    }

    /**
     * @param $community
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws InvalidConfigException
     */
    public function checkIfCanRegister($community)
    {
        $context = $community->context;
        if ($context == 'open20\amos\events\models\Event') {
            $event = \open20\amos\events\models\Event::find()->andWhere([
                'community_id' => $community->id])->one();
            $event = \open20\amos\events\models\Event::find()->andWhere(['community_id' => $community->id])->one();
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
        if (!empty($var)) {
            if ($data['check_in_event_already_present']) {
                $ret = !empty($model->findOne("['" . $var . "' => '" . $model->$var . "']"));
            } else {
                $community = Community::findOne($data['community_id']);
                $context = $community->context;
                if ($context == 'open20\amos\events\models\Event') {
                    $event = Event::find()->andWhere(['community_id' => $community->id])->one();
                    $eventinvitation = \open20\amos\events\models\EventInvitation::find()
                        ->andWhere(['event_id' => $event->id])
                        ->andWhere(["$var" => $model->$var])->one();
                    $ret = !is_null($eventinvitation);
                }
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
            $params = \yii\helpers\ArrayHelper::toArray($model);
            $params['content'] = $content;
            $params['field_id'] = hash('md5',$model->id);

            $text = $m->render($data['send_content_by_email_text'], $params);

            $ccn = [];
            if (!empty($data['ccn_email'])) {
//                $ccn = $data['ccn_email'];
                $explode = explode(';', $data['ccn_email']);
                $ccn = array_map('trim', $explode);

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
     * @param RecordDynamicModel $model
     * @return
     */
    public function prepareContent($post, $data, $model)
    {
        $content = [];
        $privacyAttributes = $data['privacy_form_field'];

        foreach ($post['RecordDynamicModel'] as $attribute => $v) {
            $useBreakLine = true;
            $type = $model->attributeTypes()[$attribute];
            if (!empty($v) && $attribute != 'recaptcha' && !in_array($type, ['conferma_email'])) {
                if ($type == 'select') {
                    $v = $model->attributeSubvalues()[$attribute][$v];
                }else if($type == 'selectMultiple'){
                    $tmpV = '';
                    foreach ($v as $elem){
                        $tmpV .= "<li>".$model->attributeSubvalues()[$attribute][$elem]."</li>";
                    }
                    $v = "<ul style='margin-top:0px'>".$tmpV."</ul>";
                    $useBreakLine = false;
                }
                else if($type == 'dynamicInputText'){
                    $v = self::formatArrayValue($v);
                    $useBreakLine = false;
                }

                $label = $model->getAttributeLabel($attribute);
                if ($attribute == $privacyAttributes) {
                    $v = \Yii::$app->formatter->asBoolean($v);
                }

                $br = '';
                if ($useBreakLine) {
                    $br = "<br>";
                }
                if (!empty($v)) {
                    $this->compiledValues[$attribute] = $v;
                    $content[] = "<strong>" . $label . "</strong>" . ": " . $v . $br;
                }
            }
        }
        $attachmentsFields = $this->getAttachmentsFields($data);

        foreach ($model->attributes as $fld => $vlr) {
            if (in_array($fld, $attachmentsFields)) {
                $label = $model->getAttributeLabel($fld);
                $this->compiledValues[$fld] = $model->$fld;

                $content[] = "<strong>" . $label . "</strong>" . ": " . $model->$fld . "<br>";
            }
        }
        $text = implode("", $content);
        return $text;
    }

    /**
     * @param $value
     * @return string
     */
    public static function formatArrayValue($value)
    {
        $return = $value;
        if (is_array($value) && !empty($value)) {
            $rowElems = [];
            $tmp = [];
            $isEmpty = true;
            //preparo di dati in modo coerente
            foreach ($value as $attr => $elems) {
                foreach ($elems as $key => $val) {
                    $tmp[$key][$attr] = $val;
                    if (!empty($val)) {
                        $isEmpty = false;
                    }
                }
            }
            if ($isEmpty) {
                return '';
            }

            //formatto per la visulizzazione
            foreach ($tmp as $elem) {
                $strElem = [];
                foreach ($elem as $attr => $val) {
                    $strElem[] = $attr . ': ' . $val;
                }
                $rowElems[] = "<li>" . implode(' | ', $strElem) . "</li>";
            }
            $return = "<ul style='margin-top:0px;'>" . implode("", $rowElems) . "</ul>";
        }
        return $return;
    }


    /**
     *
     * @param  $data
     * @return
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
     * @throws \yii\base\InvalidConfigException
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
}