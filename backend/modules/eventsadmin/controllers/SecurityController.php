<?php


namespace backend\modules\eventsadmin\controllers;

use backend\modules\eventsadmin\models\AcceptPrivacyForm;
use backend\modules\eventsadmin\utility\EventsAdminEmailUtility;
use backend\modules\eventsadmin\utility\EventsAdminUtility;
use backend\modules\tickets\Module;
use open20\amos\admin\AmosAdmin;
use open20\amos\admin\models\ForgotPasswordForm;
use open20\amos\admin\models\LoginForm;
use open20\amos\admin\models\ProfileReactivationForm;
use open20\amos\admin\models\RegisterForm;
use open20\amos\admin\models\TokenUsers;
use open20\amos\admin\models\UserProfile;
use open20\amos\admin\models\UserProfileReactivationRequest;
use open20\amos\admin\utility\UserProfileUtility;
use open20\amos\core\controllers\BackendController;
use backend\modules\eventsadmin\models\FirstAccessForm;
use open20\amos\core\helpers\Html;
use open20\amos\core\interfaces\InvitationExternalInterface;
use open20\amos\core\module\AmosModule;
use open20\amos\core\user\User;
use open20\amos\events\AmosEvents;
use open20\amos\events\models\EventGroupReferentMm;
use open20\amos\socialauth\utility\SocialAuthUtility;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class SecurityController extends \open20\amos\admin\controllers\SecurityController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $result = ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'login-frontend',
                            'login-admin'
                        ],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'accept-privacy',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ]);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($action->id == 'login-frontend') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Login action and function called on login view.
     * @param null $redir
     * @return string|Response
     * @throws \Exception
     */
    public function actionLoginFrontend($redir = null, $redirect_post_reg = null)
    {
        \Yii::$app->session->set('beOrfFe','frontend');

        $this->setUpLayout('login');
        \Yii::$app->session->remove('redirect_url_spid');
        \Yii::$app->session->remove('redirect_post_reg');

        if (!empty($redir) && strpos(urldecode($redir), \Yii::$app->params['platform']['frontendUrl']) >= 0) {
            \Yii::$app->session->set('redirect_url_spid', urldecode($redir));
        }

        if (!empty($redirect_post_reg) && strpos(urldecode($redirect_post_reg), \Yii::$app->params['platform']['frontendUrl']) >= 0) {
            \Yii::$app->session->set('redirect_post_reg', $redirect_post_reg);
        }


        /** @var LoginForm $model */
        $model = $this->adminModule->createModel('LoginForm');
        $token = \Yii::$app->request->get('token');


        if (!Yii::$app->user->isGuest && empty($token)) {
            return $this->goHome();
        }

        //login by token and redirect
        if (!empty($token)) {
            $this->loginByToken($model, $token);
        }

        if ($model->load(Yii::$app->request->post())) {

            if ($this->adminModule->allowLoginWithEmailOrUsername) {
                $user = User::findByUsernameOrEmail($model->usernameOrEmail);
            } else {
                $user = User::findByUsername($model->username);
            }

            if (is_null($user)) {
                if ($this->adminModule->allowLoginWithEmailOrUsername) {
                    $inactiveUser = User::findByUsernameOrEmailInactive($model->usernameOrEmail);
                } else {
                    $inactiveUser = User::findByUsernameInactive($model->username);
                }

                if (!is_null($inactiveUser)) {
                    return $this->redirect('/admin/security/reactivate-profile?userdisabled');
                }

                // Trigger validation for password check
                $model->validate();

                return $this->render('login', [
                    'model' => $model,
                ]);
            }


            if ($model->login()) {
                /* per amos */
                if (isset(\Yii::$app->params['template-amos']) && \Yii::$app->params['template-amos']) {
                    $ruolo = \Yii::$app->authManager->getRole($model->ruolo);
                    $userId = \Yii::$app->getUser()->getId();
                    \Yii::$app->authManager->revokeAll($userId);
                    \Yii::$app->authManager->assign($ruolo, $userId);
                }

                //Autogenerated reset widgets
                if (isset(\Yii::$app->params['template-amos']) && \Yii::$app->params['template-amos'] && !is_null(Yii::$app->getModule('build'))) {
                    $this->run('/build/default/crea-dashboard');
                }

                // if google contact service enabled reload in session some contact data by google account
                AmosAdmin::fetchGoogleContacts();

                //Social Auth trigger
                $socialModule = Yii::$app->getModule('socialauth');

                //If the module is enabled then create social user
                if ($socialModule && $socialModule->id) {
                    //Provider is in session
                    $provider = Yii::$app->session->get('social-match');

                    //If is set social match i nett to link user
                    if ($provider) {
                        //pre-compile with social-auth session data
                        $socialProfile = \Yii::$app->session->get('social-profile');

                        //The user profile
                        $userProfile = $user->profile;

                        //Create link
                        $this->createSocialUser($userProfile, $socialProfile, $provider);
                    }
                }

                /** @var  $response  Response */
//                $response = $this->goBack();
////                $current = Url::();
//                $anchor = preg_match('/#(.)+/', $_SERVER['HTTP_REFERER']);
//
//                $url = $response->headers['location'].$anchor;
//                return $this->redirect($url);
                if (!empty($redir)) {
                    return $this->redirect($redir);
                }
                return $this->goBack();
            } else {
                return $this->render('login', [
                    'model' => $model,
                ]);
            }
        } else {

            //pre-compile with social-auth session data
            $socialProfile = \Yii::$app->session->get('social-profile');

            if ($socialProfile) {
                $model->username = $socialProfile->email;
            }

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }


    /**
     * @param null $token_invitation
     * @return bool|string|Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRegister($token_invitation = null)
    {
        if (!empty($_GET['sessionIdm'])) {
            \Yii::$app->session->set('IDM', unserialize($_GET['sessionIdm']));
        }

        if(empty($token_invitation)){
            $token_invitation = \Yii::$app->session->get('tokenInvitationEvent');
        }

        $redirect_post_reg = null;
        if (!empty(\Yii::$app->session->get('redirect_post_reg'))) {
            $redirect_post_reg =  \Yii::$app->session->get('redirect_post_reg');
        }

        $userImported = \amos\userimporter\models\UserImportTaskUser::find()
            ->andWhere(['token' => $token_invitation])->one();

        // cancello tutti i token scaduti
        if (!empty($userImported) && $userImported->expire_date < date('Y-m-d H:i:s')) {
            $allToken = \amos\userimporter\models\UserImportTaskUser::find()
                ->andWhere(['token' => $token_invitation])->all();
            foreach ($allToken as $token) {
                $token->token = null;
                $token->save(false);
            }
            $userImported = null;
        }

        $this->setUpLayout('login');

        if (!Yii::$app->user->isGuest) {
            Yii::$app->session->set('removeAfterLogout', 'true');
            Yii::$app->user->logout();
            Yii::$app->session->remove('removeAfterLogout');
        }

        /**
         * If signup is not enabled
         * */
        if (!$this->module->enableRegister) {
            if (!empty($this->module->textWarningForRegisterDisabled)) {
                Yii::$app->session->addFlash('warning',
                    AmosAdmin::t('amosadmin', $this->module->textWarningForRegisterDisabled));
            } else {
                Yii::$app->session->addFlash('danger', AmosAdmin::t('amosadmin', 'Signup Disabled'));
            }

            return $this->goHome();
        }

        /**
         * If the mail is not set i can't create user
         *
         * if(empty($userProfile->email)) {
         * Yii::$app->session->addFlash('danger', AmosAdmin::t('amosadmin', 'Unable to register, missing mail permission'));
         *
         * return $this->goHome();
         * } */
        /** @var \backend\modules\eventsadmin\models\RegisterForm $model */
        $model = $this->adminModule->createModel('RegisterForm');

        //pre-compile form datas from get params
        $getParams = \Yii::$app->request->get();

        //pre-compile with social-auth session data
        $socialProfile = \Yii::$app->session->get('social-profile');

        // Pre-compile with SPID session data
        $spidData = \Yii::$app->session->get('IDM');
        if (!empty($getParams['name']) && !empty($getParams['surname']) && !empty($getParams['email'])) {
            $model->nome = $getParams['name'];
            $model->cognome = $getParams['surname'];
            $model->email = $getParams['email'];
        } elseif ($socialProfile && $socialProfile->email) {
            $model->nome = $socialProfile->firstName;
            $model->cognome = $socialProfile->lastName;
            $model->email = $socialProfile->email;
        } elseif ($spidData) {
            if (!empty($spidData['emailAddress'])) {
                $model->email = $spidData['emailAddress'];
            }
            if (!empty($spidData['nome'])) {
                $model->nome = $spidData['nome'];
            }
            if (!empty($spidData['cognome'])) {
                $model->cognome = $spidData['cognome'];
            }
            if (!empty($spidData['codiceFiscale'])) {
                $model->codice_fiscale = $spidData['codiceFiscale'];
            }
        }
        $model->loadDataFromSocial();
//
        // Invitation User id
        $iuid = isset($getParams['iuid']) ? $getParams['iuid'] : null;

        if ($model->load(Yii::$app->request->post())) {
//            pr($_POST);die;

            $this->beforeRegisterNewUser($model);
            /**
             * @var $newUser integer False or UserId
             */
            $newUser = $this->adminModule->createNewAccount(
                $model->nome, $model->cognome, $model->email, $model->privacy, false, null, \Yii::$app->request->post('redirectUrl')
            );

            /**
             * If $newUser is false the user is not created
             */
            if (!$newUser || isset($newUser['error'])) {
                //Yii::$app->session->addFlash('danger', AmosAdmin::t('amosadmin', '#error_unable_to_register'));
                $result_message = [];
                $errorMail = ($model->email ? $model->email : '');
                array_push($result_message,
                    AmosAdmin::t('amosadmin', '#error_register_user', ['errorMail' => $errorMail]));

                //  Commentato quando è stato cambiato il messaggio di errore. La richiesta era di far vedere solamente il messaggio
                // di errore e non gli errori successivi in quanto ritenuti duplicati.
//                if ($newUser['messages']) {
//                    foreach ($newUser['messages'] as $message) {
//                        //Yii::$app->session->addFlash('danger', AmosAdmin::t('amosadmin', reset($message)));
//                        array_push($result_message, AmosAdmin::t('amosadmin', reset($message)));
//                    }
//                }

                return $this->render('security-message',
                    [
                        'title_message' => AmosAdmin::t('amosadmin', 'Spiacenti'),
                        'result_message' => $result_message,
                        'go_to_login_url' => Url::current()
                    ]);

                //return $this->goHome();
            }

            $userId = $newUser['user']->id;

            /** @var UserProfile $userProfileModel */
            $userProfileModel = $this->adminModule->createModel('UserProfile');
            /**
             * @var $newUserProfile UserProfile
             */
            $newUserProfile = $userProfileModel::findOne(['user_id' => $userId]);

            $createdSocialUser = null;
            $createdSpidUser = null;

            if ($newUserProfile) {
                $this->addExtraField($newUserProfile, $model);
                \Yii::$app->session->remove('tokenInvitationEvent');


                //associo il social all'utente
                if (!empty($model->userSocial)) {
                    $userSocial = Json::decode($model->userSocial);
                    $socialProfile = EventsAdminUtility::getClassHybridUserProfile($userSocial);
                    $createdSocialUser = $this->createSocialUser($newUserProfile, $socialProfile, $model->socialScelto);
                }
                if (!empty($spidData)) {
                    $createdSpidUser = SocialAuthUtility::createIdmUser($spidData, $newUserProfile->user_id);
                }
            }
            /**
             * If $newUser is false the user is not created
             */
            if (!$newUserProfile || !$newUserProfile->id) {
                //Yii::$app->session->addFlash('danger', AmosAdmin::t('amosadmin', 'Error when loading profile data, try again'));

                return $this->render('security-message',
                    [
                        'title_message' => AmosAdmin::t('amosadmin', 'Errore'),
                        'result_message' => AmosAdmin::t('amosadmin', 'Error when loading profile data, try again'),
                        'go_to_login_url' => Url::current()
                    ]);

                //return $this->goHome();
            }
            $this->afterRegisterNewUser($model, $newUserProfile);

            //Social Auth trigger
            $socialModule = \Yii::$app->getModule('socialauth');

            //If the module is enabled then create social user
            if ($socialModule && $socialModule->id) {
                //Provider is in session
                $provider = \Yii::$app->session->get('social-pending');

                //If is set social match i nett to link user
                if (!empty($provider)) {
                    $this->createSocialUser($newUserProfile, $socialProfile, $provider);
                }
            }

            $iuid = \Yii::$app->request->post('iuid');

            $communityId = \Yii::$app->request->post('community');
            $community = null;
            if (\Yii::$app->getModule('community')) {
                $community = \open20\amos\community\models\Community::findOne($communityId);
            }

            if (!empty($getParams['moduleName']) && !empty($getParams['contextModelId'])) {
                /** @var AmosModule $module */
                $module = Yii::$app->getModule($getParams['moduleName']);
                if (!is_null($module) && ($module instanceof InvitationExternalInterface)) {
                    $okUserContextAssociation = $module->addUserContextAssociation($userId, $getParams['contextModelId']);
                    if (!$okUserContextAssociation) {
                        Yii::$app->getSession()->addFlash('danger', AmosAdmin::t('amosadmin', '#user_context_association_error'));
                    }
                }
            }

            if (!empty($createdSocialUser) || !empty($createdSpidUser)) {
                $loginTimeout = Yii::$app->params['loginTimeout'] ?: 3600;
                Yii::$app->user->login($newUserProfile->user, $loginTimeout);
                if (\Yii::$app->request->get('mobile')) {
                    $user = \open20\amos\mobile\bridge\modules\v1\models\User::findOne(Yii::$app->user->id);
                    $user->refreshAccessToken('', '');

                    return $this->redirect(['/socialauth/social-auth/land', 'token' => $user->getAccessToken()]);
                }

                if(!empty($redirect_post_reg)){
                    \Yii::$app->session->remove('redirect_post_reg');
                    return $this->redirect($redirect_post_reg);

                }
                return $this->redirect('/dashboard');

            }
            $sent = UserProfileUtility::sendCredentialsMail($newUserProfile, $community);

            if (!$sent) {
                //Yii::$app->session->addFlash('danger', AmosAdmin::t('amosadmin', '#error_send_register_mail'));
                return $this->render('security-message',
                    [
                        'title_message' => AmosAdmin::t('amosadmin', '#error'),
                        'result_message' => AmosAdmin::t('amosadmin', '#error_send_register_mail')
                    ]);
            } else {
                //Yii::$app->session->addFlash('success', AmosAdmin::t('amosadmin', 'An email has been sent to') . ' ' . $model->email);

                // Sent notification email to invitation user
                if ($iuid != null) {
                    $sent = UserProfileUtility::sendUserAcceptRegistrationRequestMail($newUserProfile, $community, $iuid);
                }

                return $this->render('security-message',
                    [
                        'title_message' => AmosAdmin::t('amosadmin', '#msg_complete_registration_title'),
                        'result_message' => [
                            AmosAdmin::t('amosadmin', '#msg_complete_registration_result_1') . '<br>' . Html::tag('span',
                                $model->email),
                            AmosAdmin::t('amosadmin', '#msg_complete_registration_result_2')
                        ]
                    ]);
            }

            //return $this->goHome();

        }

        return $this->render(
            'register',
            [
                'model' => $model,
                'userImported' => $userImported,
                'iuid' => $iuid
            ]);
    }

    /**
     * @param $userProfile UserProfile
     * @param $model
     */
    public function addExtraField($userProfile, $model)
    {
        $tasksQuery = \amos\userimporter\models\UserImportTaskUser::find()
            ->innerJoinWith('userImportTask')
            ->andWhere(['token' => $model->token]);

        $userProfile->azienda = $model->azienda;
        $userProfile->telefono = $model->telefono;
        $userProfile->codice_fiscale = $model->codice_fiscale;
        $userProfile->nascita_comuni_id = $model->comune;
        $userProfile->nascita_nazioni_id = 1;
        $userProfile->nascita_province_id = $model->provincia;
        $userProfile->sesso = $model->sesso;
        if (!empty($_POST['RegisterForm']['privacy_2'])) {
            $userProfile->privacy_2 = $_POST['RegisterForm']['privacy_2'];
        }

        if (!empty($tasksQuery->groupBy('event_group_referent_id')->one())) {
            $userProfile->user_profile_age_group_id = $model->eta;
            $userProfile->is_imported = true;
        }
        $userProfile->save(false);
        $preferencesTags = \Yii::$app->request->post('preferencesTags');
        EventsAdminUtility::savePreferencesTags($userProfile, $preferencesTags);

        foreach ($tasksQuery->all() as $taskUser) {
            $member = new \open20\amos\events\models\EventGroupReferentMm();
            $member->user_id = $userProfile->user_id;
            $member->event_group_referent_id = $taskUser->userImportTask->event_group_referent_id;
            $member->exclude_from_query = 0;
            $member->save(false);
            $taskUser->token = null;
            $taskUser->user_id = $userProfile->user_id;
            $taskUser->save(false);
            $userProfile->user_import_task_id = $taskUser->user_import_task_id;
            $userProfile->dg_of_registration = $taskUser->userImportTask->event_group_referent_id;
            $userProfile->save(false);

        }
    }


    /**
     * Login-info choice at register step
     * @return string
     */
    public function actionInsertAuthData()
    {
        $this->setUpLayout('login');
        $password_reset_token = null;
        $user = null;
        $username = null;
        $community_id = null;
        $redirectUrl = \Yii::$app->getUser()->loginUrl;
        $precompileUsernameOnFirstAccess = $this->module->precompileUsernameOnFirstAccess;
        $isFirstAccess = false;
        if (NULL !== (Yii::$app->getRequest()->getQueryParam('token'))) {
            $password_reset_token = Yii::$app->getRequest()->getQueryParam('token');
            $user = User::findByPasswordResetToken($password_reset_token);
            if ($user) {
                $username = $user->username;
                $isFirstAccess = (empty($user->password_hash));
            }
        }

        $postLoginUrl = null;
        if (!is_null(Yii::$app->getRequest()->get('url_previous'))) {
            $postLoginUrl = Yii::$app->getRequest()->get('url_previous');
        }

        if ((Yii::$app->getRequest()->get('community_id')) !== NULL) {
            $community_id = Yii::$app->getRequest()->getQueryParam('community_id');
//            $postLoginUrl  = Yii::$app->getUrlManager()->createUrl(['/community/join', 'id' => $community_id]);
        }
        if ($user && !$username) {
            if (Yii::$app->request->isPost) {
                $model = new FirstAccessForm();
                if ($isFirstAccess && is_null($user->userProfile->privacy)) {
                    $model->setScenario(FirstAccessForm::SCENARIO_CHECK_PRIVACY);
                }
                if ($model->load(Yii::$app->request->post())) {
                    if ($model->verifyUsername($model->username)) {
                        Yii::$app->getSession()->addFlash('danger',
                            Yii::t('amosadmin',
                                'Attenzione! La username inserita &egrave; gi&agrave; in uso. Sceglierne un&#39;altra.'));
                        return $this->render('first_access',
                            [
                                'model' => $model,
                                'isFirstAccess' => $isFirstAccess && is_null($user->userProfile->privacy)
                            ]);
                    } else {
                        $user->setPassword($model->password);
                        $user->username = $model->username;
                        if ($user->validate() && $user->save()) {
                            Yii::$app->getSession()->addFlash('success',
                                Yii::t('amosadmin', 'Perfetto! Hai scelto correttamente le tue credenziali.'));
                            $user->removePasswordResetToken();
                            $user->save();
                            if ($isFirstAccess) {
                                $profile = $user->userProfile;
                                $profile->privacy = 1;
                                if (!empty($_POST['FirstAccessForm']['privacy_2'])) {
                                    $profile->privacy_2 = $_POST['FirstAccessForm']['privacy_2'];
                                }
                                $profile->save(false);
                                //spostato in evento al bootstrap AfterLogin
                                //EventsAdminEmailUtility::sendEmailFirstAccessPassword($user);
                            }

                            return $this->login($model->username, $model->password, $community_id, $postLoginUrl, $isFirstAccess);
                        } else {
                            //return $this->render('login_error', ['message' => Yii::t('amosadmin', " Errore! Il sito non ha risposto, probabilmente erano in corso operazioni di manutenzione. Riprova più tardi.")]);
                            return $this->render('security-message',
                                [
                                    'title_message' => AmosAdmin::t('amosadmin', 'Spiacenti'),
                                    'result_message' => AmosAdmin::t('amosadmin',
                                        " Errore! Il sito non ha risposto, probabilmente erano in corso operazioni di manutenzione. Riprova più tardi.")
                                ]);
                        }
                    }
                } else {
                    $model->token = $password_reset_token;
                    return $this->render('first_access',
                        [
                            'model' => $model,
                            'isFirstAccess' => $isFirstAccess
                        ]);
                }
            } else {
                $model = new FirstAccessForm();
                if ($precompileUsernameOnFirstAccess) {
                    $model->username = $user->email;
                }
                if ($isFirstAccess) {
                    $model->setScenario(FirstAccessForm::SCENARIO_CHECK_PRIVACY);
                }
                $model->token = $password_reset_token;
                return $this->render('first_access',
                    [
                        'model' => $model,
                        'isFirstAccess' => $isFirstAccess && is_null($user->userProfile->privacy)
                    ]);
            }
        } else if ($user && $username) {

            if (Yii::$app->request->isPost) {
                $model = new FirstAccessForm();
                if ($isFirstAccess && is_null($user->userProfile->privacy)) {
                    $model->setScenario(FirstAccessForm::SCENARIO_CHECK_PRIVACY);
                }

                if ($model->load(Yii::$app->request->post())) {
                    $user->setPassword($model->password);

                    if ($user->validate() && $user->save()) {
                        Yii::$app->getSession()->addFlash('success',
                            Yii::t('amosadmin', 'Perfetto! Hai scelto correttamente la tua password.'));
                        $user->removePasswordResetToken();
                        $user->save();
                        if ($isFirstAccess) {
                            $profile = $user->userProfile;
                            $profile->privacy = 1;
                            if (!empty($_POST['FirstAccessForm']['privacy_2'])) {
                                $profile->privacy_2 = $_POST['FirstAccessForm']['privacy_2'];
                            }
                            $profile->save(false);
                            EventsAdminEmailUtility::sendEmailFirstAccessPassword($user);
                        }
                        return $this->login($username, $model->password, $community_id, $postLoginUrl, $isFirstAccess);
                    } else {
                        //return $this->render('login_error', ['message' => Yii::t('amosadmin', " Errore! Il sito non ha risposto, probabilmente erano in corso operazioni di manutenzione. Riprova più tardi.")]);
                        return $this->render('security-message',
                            [
                                'title_message' => AmosAdmin::t('amosadmin', 'Spiacenti'),
                                'result_message' => AmosAdmin::t('amosadmin',
                                    " Errore! Il sito non ha risposto, probabilmente erano in corso operazioni di manutenzione. Riprova più tardi.")
                            ]);
                    }
                } else {
                    $model->token = $password_reset_token;
                    $model->username = $username;
                    return $this->render('reset_password',
                        [
                            'model' => $model,
                            'isFirstAccess' => $isFirstAccess && is_null($user->userProfile->privacy)
                        ]);
                }
            } else {
                $model = new FirstAccessForm();
                if ($isFirstAccess && is_null($user->userProfile->privacy)) {
                    $model->setScenario(FirstAccessForm::SCENARIO_CHECK_PRIVACY);
                }
                $model->token = $password_reset_token;
                $model->username = $username;
                return $this->render('reset_password',
                    [
                        'model' => $model,
                        'isFirstAccess' => $isFirstAccess && is_null($user->userProfile->privacy)
                    ]);
            }
        } else {
            //return $this->render('login_error', ['message' => Yii::t('amosadmin', ' Errore! Il tempo per poter accedere è scaduto. Contatti l\'amministratore e si faccia reinviare la mail di accesso.')]);
            $tokenErrorMessage = AmosAdmin::t('amosadmin', "#insert_auth_data_token_expired_message");

            // Pickup assistance params
            $assistance = isset(\Yii::$app->params['assistance']) ? \Yii::$app->params['assistance'] : [];

            // Check if is in email mode
            $isMail = ((isset($assistance['type']) && $assistance['type'] == 'email') || (!isset($assistance['type'])
                    && isset(\Yii::$app->params['email-assistenza']))) ? true : false;
            $mailAddress = isset($assistance['email']) ? $assistance['email'] : (isset(\Yii::$app->params['email-assistenza'])
                ? \Yii::$app->params['email-assistenza'] : '');
            $linkHref = $isMail ? 'mailto:' . $mailAddress : (isset($assistance['url']) ? $assistance['url'] : '');
            if ((isset($assistance['enabled']) && $assistance['enabled']) || (!isset($assistance['enabled']) && isset(\Yii::$app->params['email-assistenza']))) {
                $tokenErrorMessage .= '';
                $linkAssistance = AmosAdmin::t('amosadmin', "o contatta l'assitenza")
                    . " " . Html::a(
                        AmosAdmin::t('amosadmin', "cliccando qui"), $linkHref,
                        ['title' => Yii::t('amoscore', 'Verrà aperta una nuova finestra'), 'style' => 'color:white']
                    );

                $linkForgot = Html::tag('br') . AmosAdmin::t('amosadmin',
                        "Recupera la password") . " " . (Html::a(
                        AmosAdmin::t('amosadmin', "#insert_auth_data_token_expired_message_click_here"),
                        ['/admin/security/forgot-password'],
                        ['title' => AmosAdmin::t('amosadmin', '#forgot_password_title_link'), 'style' => 'color:white']
                    ));

                $tokenErrorMessage .= $linkForgot . "<br>";
                $tokenErrorMessage .= $linkAssistance;

            } else {
                $tokenErrorMessage .= Html::tag('br') .
                    AmosAdmin::t('amosadmin', "#forgot_password_title_link") . ' ' .
                    Html::a(
                        AmosAdmin::t('amosadmin', "#insert_auth_data_token_expired_message_click_here"),
                        ['/admin/security/forgot-password'],
                        ['title' => AmosAdmin::t('amosadmin', '#forgot_password_title_link')]
                    );
            }
            return $this->render('security-message',
                [
                    'title_message' => AmosAdmin::t('amosadmin', 'Spiacenti'),
                    'result_message' => $tokenErrorMessage,
                    'hideGoBackBtn' => true
                ]);
        }
    }


    /**
     * @param UserProfile $userProfile
     * @param \Hybridauth\User\Profile $socialProfile
     * @param $provider
     * @return bool|SocialAuthUsers
     */
    protected function createSocialUser($userProfile, $socialProfile, $provider)
    {
        try {
            /**
             * @var $socialUser \open20\amos\socialauth\models\SocialAuthUsers
             */
            $socialUser = new \open20\amos\socialauth\models\SocialAuthUsers();

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

                    Yii::$app->session->addFlash('success',
                        AmosAdmin::t('amosadmin', 'Social Account for {provider} Linked to your User',
                            [
                                'provider' => $provider
                            ]));

                    return $socialUser;
                } else {
                    Yii::$app->session->addFlash('danger',
                        Module::t('amossocialauth', 'Unable to Link The Social Profile'));
                    return false;
                }
            } else {
                Yii::$app->session->addFlash('danger', Module::t('amossocialauth', 'Invalid Social Profile, Try again'));
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAcceptPrivacy()
    {
        \Yii::$app->params['bsVersion'] = '4.x';
        \Yii::$app->view->params['customClassMainContent'] = 'box-container sidebar-setting';
        $this->setUpLayout('bootstrap-italia-layout-with-sidebar', []);

        $userProfile = UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
        $model = new AcceptPrivacyForm();

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $userProfile->privacy = true;
            $userProfile->privacy_2 = $model->privacy_2;
            $userProfile->save(false);
            $preferencesTags = \Yii::$app->request->post('preferencesTags');
            EventsAdminUtility::savePreferencesTags($userProfile, $preferencesTags);

            // redirect to complete course
            $moodleModule = \Yii::$app->getModule('moodle');
            /** @var  $moduleEvents AmosEvents */
            $moduleEvents = \Yii::$app->getModule('events');
            if (!empty($moodleModule) && !empty($moduleEvents)) {
                if ($moduleEvents->operatorCandidate && !empty($moduleEvents->operatorCandidate['enabled'])) {
                    if (\Yii::$app->user->can($moduleEvents->operatorCandidate['role'])) {
                        $count = EventGroupReferentMm::find()
                            ->andWhere(['user_id' => \Yii::$app->user->id, 'exclude_from_query' => true])
                            ->andWhere(['status' => null])->count();
                        if ($count > 0) {
                            return $this->redirect(\Yii::$app->params['platform']['backendUrl'] . '/events/event-group-referent-mm/complete-course');
                        }
                    }

                }
            }

            return $this->redirect('/');
        }

        return $this->render('@backend/modules/eventsadmin/views/security/fullsize/accept-privacy', ['model' => $model]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogin()
    {
        \Yii::$app->session->set('beOrfFe','backend');

        $tokenInvitation = \Yii::$app->request->get('token_invitation');
        if(!empty($tokenInvitation)){
            \Yii::$app->session->set('tokenInvitationEvent', $tokenInvitation);
        }else {
            \Yii::$app->session->remove('tokenInvitationEvent');
        }
        \Yii::$app->params['hideLoginStandard'] = false;
        return parent::actionLogin();
    }

    /**
     * @return mixed
     */
    public function actionLoginAdmin()
    {
        \Yii::$app->session->set('beOrfFe','backend');

        if(YII_ENV == YII_ENV_PROD) {
            $isPermitted = EventsAdminUtility::isAccessPermitted();
            if (!$isPermitted) {
                return $this->redirect('/');
            }
        }

        $model = \Yii::$app->getModule('admin')->createModel('LoginForm');
        if (\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post())) {
            if ($model->usernameOrEmail != 'admin') {
                \Yii::$app->session->addFlash('warning', "Non hai il permesso di accedere.");
                return $this->redirect('/admin/security/login-admin');
            }
        }

        \Yii::$app->params['hideLoginStandard'] = false;
        return parent::actionLogin();
    }

}