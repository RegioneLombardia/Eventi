<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    [NAMESPACE_HERE]
 * @category   CategoryName
 */

namespace open20\amos\mobile\bridge\modules\v1\controllers;

use backend\modules\campains\utility\CampainsQueryUtility;
use backend\modules\campains\models\PreferenceCampainContainer;
use backend\modules\campains\models\PreferenceCampainChannelMm;
use common\models\AppVersion;
use DateTime;
use open20\amos\admin\models\UserOtpCode;
use open20\amos\audit\components\Helper;
use open20\amos\core\utilities\CurrentUser;
use preference\userprofile\exceptions\NotificationEmailException;
use preference\userprofile\models\PreferenceLanguage;
use preference\userprofile\models\PreferenceLanguageUserMm;
use preference\userprofile\models\Tag;
use open20\amos\admin\AmosAdmin;
use open20\amos\admin\models\UserProfile;
use open20\amos\admin\utility\UserProfileUtility;
use preference\userprofile\utility\UserProfileUtility as UserProfileUtilityPc;
use open20\amos\comuni\models\IstatComuni;
use open20\amos\comuni\models\IstatProvince;
use open20\amos\core\user\User;
use open20\amos\moodle\models\Topic;
use Exception;
use InvalidArgumentException;
use preference\userprofile\models\PreferenceChannel;
use preference\userprofile\models\PreferenceUserTargetAttribute;
use preference\userprofile\utility\EmailUtility;
use preference\userprofile\utility\TargetTagUtility;
use preference\userprofile\utility\TopicTagUtility;
use preference\userprofile\utility\UserInterestTagUtility;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\log\Logger;
use yii\validators\EmailValidator;
use yii\validators\StringValidator;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use preference\userprofile\models\PersonalData;
use yii\web\ForbiddenHttpException;

class PreferenceController extends DefaultController
{
    /**
     *
     * @param Action $action
     * @return bool
     * @throws ForbiddenHttpException
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
//        throw new ForbiddenHttpException('API non più utilizzabile - usare la forma nuova');

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        $behaviours = parent::behaviors();

        return ArrayHelper::merge($behaviours,
                [
                    'verbFilter' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'create-profile' => ['get'],
                            'delete-account' => ['get'],
                            'get-channels' => ['get'],
                            'get-targets' => ['get'],
                            'get-topics' => ['get'],
                            'get-language' => ['get'],
                            'get-cross-topics' => ['get'],
                            'get-tag-by-id' => ['get'],
                            'get-user-by-id' => ['get'],
                            'update-user-by-id' => ['get', 'post'],
                            'update-password-by-user-by-id' => ['get', 'post'],
                            'preference-tag-toggle' => ['get', 'post'],
                            'update-language' => ['get', 'post'],
                            'get-province' => ['get'],
                            'get-municipality' => ['get'],
                            'get-comunications-by-user' => ['get'],
                            'get-comunication-by-id' => ['get'],
                            'topic' => ['get'],
                            'get-app-last-version' => ['get'],
                        ],
                    ],
        ]);
    }

    /**
     * Il permesso di accedere ai dati solo se non dell'utente loggato o se a richiedere è l'utente 2 appguest
     *
     * @param $userId
     * @return void
     * @throws ForbiddenHttpException
     */
    private function checkUserSecurity($userId) {
        if ((Yii::$app->user->id == 2) || (Yii::$app->user->id == $userId)) {
            return;
        }

        throw new ForbiddenHttpException('Not allowed call');
    }

    /**
     * @return Json
     * @throws Exception
     */
    public function actionGetLastAppVersion()
    {
        $ret = ['status' => 'ok', 'message' => ''];
        try {
            /** @var ActiveQuery $query */
            $query = AppVersion::find();
            $av = $query->orderBy(['version' => SORT_DESC])->one();
            if (!empty($av)){
                $ret['data'] = $av->attributes;
            } else {
                $ret = ['status' => 'error', 'message' => 'nessuna versione impostata'];
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
            $ret = ['status' => 'error', 'message' => $ex->getMessage()];
        }
        return $ret;
    }

    /**
     *
     * @param type $email
     * @return type
     * @throws Exception
     */
    public function actionCreateProfile($email = null)
    {
        // annullo l'azione, con DL semplificazioni non sarà più possibile fare questa azione
        throw new ForbiddenHttpException('Azione bloccata: DL semplificazioni');

        $code        = 0;
        $message     = '';
        $transaction = null;
        $ret         = ['code' => 0, 'message' => ''];

        try {
            // controlliamo che sia già registrato l'utente...
            // se sì ed è attivo allora lo loggo...
            $user = User::find()->where(['email' => $email])->andWhere(['status' => 10])->one();
            if (!empty($user)) {
                $ret = $this->getError(1);
            }

            if (!$ret['code']) {
                $transaction = Yii::$app->db->beginTransaction();
                // non esiste lo registro

                $module           = Yii::$app->getModule(AmosAdmin::getModulename());
                $userCreatedArray = $module->createNewAccount(
                    '-', '-', $email, 1, false, null, null,
                    AmosAdmin::getModulename()
                );


                if (isset($userCreatedArray['error']) && ($userCreatedArray['error']
                    >= 1)) {
                    $ret = $this->getError(2);
                } else {

                    // utente creato vado a settare i dati inseriti in registrazione.
                    // 1 inserico i dati sul profilo
                    $user                       = $userCreatedArray['user'];
                    $user->password_reset_token = null;
                    $password                   = uniqid('', true);
                    $user->password_hash        = \Yii::$app->security->generatePasswordHash($password);
                    $user->status               = 10;
                    $user->save(false);

                    $userProfile          = $user->userProfile;
                    $userProfile->nome    = '';
                    $userProfile->cognome = '';
                    $userProfile->save(false);

                    $interest_classname = 'simple-choice';
                    $tag                = TargetTagUtility::getTargetByKey('cittadino');
                    if (!empty($tag)) {
                        UserInterestTagUtility::saveRegisteredUserInterestTag($userProfile,
                            $interest_classname, $tag);
                    } else {
                        throw new Exception('Impossibile creare target cittadino...');
                    }

                    $uta                       = new PreferenceUserTargetAttribute();
                    $uta->email                = $email;
                    $uta->validated_email_flag = false;
                    $uta->target_code          = TargetTagUtility::getTargetByKey('cittadino')->codice;
                    $uta->user_id              = $user->id;
                    $uta->save(false);
                    EmailUtility::sendUserMailQuickRegistration($email,
                        'Lombardia Informa: registrazione rapida', $email,
                        $password);
                }
                $transaction->commit();
            }
        } catch (Exception $ex) {
            $ret = ['code' => $ex->getCode(), 'message' => $ex->getMessage()];
        }
        return Json::encode($ret);
    }


    /**
     * @return array
     * @throws Exception
     */
    public function actionDeleteAccount($user_id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ret = ['code' => 0, 'message' => 'Response OK'];
        // VarDumper::dump(Yii::$app->user->id,3,1); die;
        try {
            if (Yii::$app->user->id != $user_id) {
                throw new ForbiddenHttpException();
            }
            $loggedUserProfile = \preference\userprofile\models\UserProfile::findOne(['user_id' => $user_id]);
            if (!empty($loggedUserProfile)) {
                \preference\userprofile\utility\UserProfileUtility::deleteProfile($loggedUserProfile);
                Yii::$app->user->logout();
            } else {
                throw new ForbiddenHttpException();
            }
        } catch (ForbiddenHttpException $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
            $ret['code'] = $ex->statusCode;
            $ret['message'] = 'Accesso negato';
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
            $ret['code'] = 1;
            $ret['message'] = 'Error';
        }
        return $ret;
    }

    
    /**
     * @return Json
     * @throws Exception
     */
    public function actionGetChannels()
    {   
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ret = ['code' => 0, 'message' => 'Response OK'];
        try {
            $listOfChannels = UserInterestTagUtility::getAllChannels();
            /** @var PreferenceChannel $channel **/
            foreach ($listOfChannels as $channel) {
                $ret['data'][$channel->id]['id'] = $channel->id;
                $ret['data'][$channel->id]['name'] = $channel->title;
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
            $ret = ['code' => $ex->getCode(), 'message' => $ex->getMessage()];
        }
        return $ret;
    }

    /**
     * @return Json
     * @throws Exception
     */
    public function actionGetTargets($userId = null)
    {
        $this->checkUserSecurity($userId);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ret = ['code' => 0, 'message' => 'Response OK'];
        try {
            $list = TargetTagUtility::getAllTargetTag();
            // VarDumper::dump( ArrayHelper::map($list,'id','id') , 10, true); die;
            $ret['data'] = $this->listOfTagToArray($list, $userId);
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
            $ret = ['code' => $ex->getCode(), 'message' => $ex->getMessage()];
        }
        return $ret;
    }

    /**
     * @return Json
     * @throws Exception
     */
    public function actionGetLanguage($userId = null)
    {
        $this->checkUserSecurity($userId);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ret = ['code' => 0, 'message' => 'Response OK'];
        try {

            // recupero l'elenco delle lingue attive
            $languagesList = PreferenceLanguage::find()->andWhere(['enable' => 1])->all();
            // recuper l'elenco delle lingue settate sull'utente
            $userLanguages = ArrayHelper::map(PreferenceLanguageUserMm::find()->andWhere(['user_id' => $userId])->all(),'preference_language_id', 'preference_language_id');

            /** @var PreferenceLanguage $language */
            foreach ($languagesList as $language) {
                $ret['data'][$language->id]['code'] = $language->code;
                $ret['data'][$language->id]['label'] = $language->name;
                $ret['data'][$language->id]['selected'] = false;
                if (in_array($language->id, $userLanguages)) {
                    $ret['data'][$language->id]['selected'] = true;
                }
            }

        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
            $ret = ['code' => $ex->getCode(), 'message' => $ex->getMessage()];
        }
        return $ret;
    }

    /**
     * @return null[]
     */
    public function actionUpdateLanguage()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $toret = [
            'status' => null,
            'messages' => null,
            'data' => null,
        ];
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $bodyParams = \Yii::$app->getRequest()->getBodyParams();
            $userId = isset($bodyParams['user_id'])? $bodyParams['user_id']: null;
            $this->checkUserSecurity($userId);

            /** @var UserProfile $userProfile */
            $userProfile = UserProfile::findOne(['user_id' => $userId]);
            if (empty($userProfile)) {
                throw new InvalidArgumentException('Lo user_id inviato non corrispone a nesuna anagrafica a sistema');
            }
            unset($bodyParams['user_id']);

            // elimino tutte le preferenze per poterle inserire nuovamente
            $toDel = PreferenceLanguageUserMm::findAll(['user_id' => $userId]);
            foreach ($toDel as $el) {
                $el->delete();
            }

            foreach ($bodyParams as $code => $value) {
                $language = PreferenceLanguage::find()->andWhere(['code' => $code])->one();
                if (!empty($language) && ($value == 'true')) {
                    $userLanguageMm = new PreferenceLanguageUserMm();
                    $userLanguageMm->user_id = $userId;
                    $userLanguageMm->preference_language_id = $language->id;
                    $userLanguageMm->save(false);
                }
            }

            $toCheck = PreferenceLanguageUserMm::findAll(['user_id' => $userId]);
            if (count($toCheck) > 0)  {
                $transaction->commit();
                $toret['status'] = 'ok';
            } else {
                $transaction->rollBack();
                $toret['status'] = 'ko';
            }

            return $toret;

        } catch (Exception $ex) {
            $transaction->rollBack();

            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);

            $toret['status'] = 'error';
            $toret['messages'][] = $ex->getMessage();
            return $toret;
        }

        throw new InvalidArgumentException('I parametri passati hanno generato un errore inatteso...');
    }

    /**
     * @return Json
     * @throws Exception
     */
    public function actionGetTopics($targetCode, $userId = null)
    {
        // $this->checkUserSecurity($userId);
        if (empty(Yii::$app->user->id)) {
            throw new ForbiddenHttpException('Not allowed call');
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ret = ['code' => 0, 'message' => 'Response OK'];
        try {
            $list = TopicTagUtility::getAllTopicByTargetCode($targetCode);
            $ret['data'] = $this->listOfTagToArray($list, $userId);
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
            $ret = ['code' => $ex->getCode(), 'message' => $ex->getMessage()];
        }
        return $ret;
    }

    /**
     * @return Json
     * @throws Exception
     */
    public function actionGetCrossTopics()
    {   
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ret = ['code' => 0, 'message' => 'Response OK'];
        try {
            $list = TopicTagUtility::getCrossTopicArray();
            $ret['data'] = $this->listOfTopicToArray($list);
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
            $ret = ['code' => $ex->getCode(), 'message' => $ex->getMessage()];
        }
        return $ret;
    }


    /**
     * @return Json
     * @throws Exception
     */
    public function actionGetTagById($id)
    {   
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ret = ['code' => 0, 'message' => 'Response OK'];
        try {
            $list[] = Tag::findOne($id);
            $ret['data'] = $this->listOfTagToArray($list);
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
            $ret = ['code' => $ex->getCode(), 'message' => $ex->getMessage()];
        }
        return $ret;
    }

    /**
     * @param  Tag[] $list
     * @return array
     */
    private function listOfTagToArray($list, $userId = null)
    {
        $userTagsSelection = $this->getUserSelectionArray($userId);

        $ret = [];
        /** @var Tag $tag **/
        foreach ($list as $tag) {
            $ret[$tag->id]['id'] = $tag->id;        
            $ret[$tag->id]['root_id'] = $tag->root;        
            $ret[$tag->id]['name'] = $tag->nome;  
            $ret[$tag->id]['code'] = $tag->codice;  
            $ret[$tag->id]['target_order'] = $tag->pc_target_order;  
            $ret[$tag->id]['topic_order'] = $tag->pc_topic_order;  
            $ret[$tag->id]['active'] = $tag->pc_active;  
            $ret[$tag->id]['description'] = $tag->descrizione;  
            $ret[$tag->id]['icon'] = $tag->icon;  

            if (!is_null($userId)) {
                $ret[$tag->id]['selected'] = false;
                if (in_array($tag->id, $userTagsSelection)) {
                    $ret[$tag->id]['selected'] = true;
                }
            }
        }
        return $ret;
    }

    /**
     * @param  Tag[] $list
     * @return array
     */
    private function listOfTopicToArray($list)
    {
        $ret = [];
        /** @var Topic $topic **/
        foreach ($list as $topic) {
            $ret[$topic->id]['code-topic'] = $topic->id;        
            $ret[$topic->id]['label'] = $topic->label;        
            $ret[$topic->id]['description'] = $topic->description;        
            $ret[$topic->id]['icon'] = $topic->icon;  
        }
        return $ret;
    }

    private function getUserSelectionArray($userId)
    {
        $userModel = AmosAdmin::instance()->createModel('User');
        $tagSelected = [];
        if (!empty($userModel::findOne(['id' => $userId]))) {
            $tagArray = UserInterestTagUtility::getUserInterests($userId, PreferenceChannel::APP_ID);
            $tagSelected = ArrayHelper::map( $tagArray, 'id', 'id');
            // VarDumper::dump( $tagSelected, 3, false); die;
        }
        return $tagSelected;
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return array
     */
    public function actionGetComunicationsByUser($userId = null, string $filterTargets = null, string $filterDateFrom = null, string $filterDateTo = null, string $filterItems = null)
    {
        $this->checkUserSecurity($userId);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (is_null($userId)) {
            throw new InvalidArgumentException('Utente obbligatorio');
        }

        // Utente corrente loggato
        $userId = \Yii::$app->user->id;
        $userProfile = \backend\modules\pcadmin\models\UserProfile::findOne(['user_id' => $userId]);

        // Tag selezionati dall'utente corrente
        $userTags = $userProfile->getUserTags(PreferenceChannel::APP_ID);

        /** @var ActiveQuery $query */
        $query = CampainsQueryUtility::getQueryBaseForCampainsChannels();
        $query->andWhere(['user.id' => $userId]);
        // CANALE APP sulla scelta utente
        $query->andWhere(['canale.id' => PreferenceChannel::APP_ID]);
        $query->orderBy('preference_campain_channel_mm.date_app ASC');

        $targets = null;
        if ($filterTargets !== null) {
            $targetsStr = explode(',', $filterTargets);
            foreach ($targetsStr as $val) {
                $targets[] = intval($val);
            }
        }

        $items = null;
        if ($filterItems !== null) {
            $itemsStr = explode(',',$filterItems);
            foreach ($itemsStr as $val) {
                $items[] = intval($val);
            }
        }

        $dateFrom = null;
        if ($filterDateFrom !== null) {
            $dateFrom = DateTime::createFromFormat('Y-m-d H:i:s', $filterDateFrom . ' 00:00:00');
        }

        if ($filterDateTo !== null) {
            $dateTo = DateTime::createFromFormat('Y-m-d H:i:s', $filterDateTo  . ' 00:00:00');
        }

        $listOfComunications = $query->all();
        $toret = [];
        /** @var PreferenceCampainChannelMm $campainChannel */
        foreach ($listOfComunications as $campainChannel) {
            // VarDumper::dump( ArrayHelper::map($campainChannel->preferenceCampain->getPreferenceCampainTagMms(),'tag_id', 'tag_id'), 3, $highlight = true);
            // Lista tag contenuti con nuove logiche (MEV 68-2022, Segmentazione e invio campagne contenuti multitematici)
            $listOfCampainTargets = $this->getListOfTagByIds(array_intersect($userTags, ArrayHelper::map($campainChannel->preferenceCampainChannelMmTagMms,'tag_id', 'tag_id')), true);
            $listOfCampainItems = $this->getListOfTagByIds(array_intersect($userTags, ArrayHelper::map($campainChannel->preferenceCampainChannelMmTagMms,'tag_id', 'tag_id')));

            $listOfCampainTargetsIds = $this->listOfTagToArray($listOfCampainTargets);
            $listOfCampainItemsIds = $this->listOfTagToArray($listOfCampainItems);

            // Controllo sui filtri avanzati.
            // La lista delle comunicazioni è data dalle scelte dell'utente fatte per gestire il proprio profilo
            // Su questa lista i parametri dei filtri agiscono
            $isToSend = true;
            // se non vuoto allora filtro per targets
            if (!empty($targets)) {
                try {
                    $intersecTargets = array_intersect(ArrayHelper::map($listOfCampainTargetsIds,'id', 'id'), $targets);
                    if (count($intersecTargets) <= 0) {
                        $isToSend = false;
                    }
                } catch (Exception $e) {
                    // do nothing...
                }
            }

            // se non vuoto allora filtro per items
            if (!empty($items)) {
                try {
                    $intersecTargets = array_intersect(ArrayHelper::map($listOfCampainItemsIds,'id', 'id'), $items);
                    if (count($intersecTargets) <= 0) {
                        $isToSend = false;
                    }
                } catch (Exception $e) {
                    // do nothing...
                }
            }

            $campainDate = false;
            try {
                // Viene valutata solo la data!!! porto tutto alle ore 00:00:00 - tecnica non molto fine...
                $arrayDate = explode(' ', $campainChannel->date_app);
                $campainDate = new DateTime(array_shift($arrayDate) . ' 00:00:00');
            } catch (Exception $e) {
                // do nothing...
            }

            // se non vuoto allora filtro per data from
            if (!empty($dateFrom) && $campainDate) {
                if ($dateFrom > $campainDate) {
                    $isToSend = false;
                }
            }

            // se non vuoto allora filtro per data to
            if (!empty($dateTo) && $campainDate) {
                if ($dateTo < $campainDate) {
                    $isToSend = false;
                }
            }

            if ($isToSend) {
                $toret[$campainChannel->id]['id'] = $campainChannel->id;
                $toret[$campainChannel->id]['date'] = $campainChannel->date_app;
                $toret[$campainChannel->id]['title'] = $campainChannel->title;
                $toret[$campainChannel->id]['topics'] = $listOfCampainItemsIds;
                $toret[$campainChannel->id]['targets'] = $listOfCampainTargetsIds;
                $toret[$campainChannel->id]['contents_number'] = $this->getNumberOfContentByCampainChannel($campainChannel);
            }

        }

        return $toret;
    }

    /**
     *
     * @param integer $comunicationId
     * @return void 
     */
    public function actionGetComunicationById($comunicationId)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if(\Yii::$app->user->id === null) {
            throw new ForbiddenHttpException('Not allowed call');
        }
        // Utente corrente loggato
        $userId = \Yii::$app->user->id;
        $userProfile = \backend\modules\pcadmin\models\UserProfile::findOne(['user_id' => $userId]);

        // Tag selezionati dall'utente corrente
        $userTags = $userProfile->getUserTags(PreferenceChannel::APP_ID);

        $channelMm = PreferenceCampainChannelMm::findOne(['id' => $comunicationId]);
        $toret = [];

        if (!empty($channelMm)) {
            // Lista tag contenuti con nuove logiche (MEV 68-2022, Segmentazione e invio campagne contenuti multitematici)
            $listItems = $this->getListOfTagByIds(array_intersect($userTags, ArrayHelper::map($channelMm->preferenceCampainChannelMmTagMms,'tag_id', 'tag_id')));
            $listChannels = $this->getListOfTagByIds(array_intersect($userTags, ArrayHelper::map($channelMm->preferenceCampainChannelMmTagMms,'tag_id', 'tag_id')), true);

            $containers = PreferenceCampainContainer::find()->joinWith('preferenceCampainSection', true, 'INNER JOIN')->andWhere(['preference_campain_channel_mm_id' => $comunicationId])->all();
            $toret = [
                'title' => $channelMm->title,
                'topics' => $this->listOfTagToArray($listItems),
                'targets' => $this->listOfTagToArray($listChannels),
                'date' => $channelMm->date_app,
                'contents' => []
            ];
            if (!empty($containers)) {
                // VarDumper::dump( count($containers), $depth = 10, $highlight = false);
                /** @var PreferenceCampainContainer $container */
                foreach ($containers as $container) {
                    // Verifica che la lista dei tag selezionati dall'utente sia presente tra i tag del contenuto/container (con una intersezione di array)
                    // Se il risultato dell'intersezione contiene almeno 1 elemento, il contenuto e' da visualizzare
                    $displayContainer = (count(array_intersect($userTags, ArrayHelper::map($container->preferenceCampainContainerTagMms, 'tag_id', 'tag_id'))) > 0);

                    if($displayContainer) {
                        $toret['contents'][$container->id]['id'] = $container->id;
                        $toret['contents'][$container->id]['title'] = $container->content_title;
                        $toret['contents'][$container->id]['description'] = $container->content_text;
                        $toret['contents'][$container->id]['news_url'] = $container->getContentLink();
                        $toret['contents'][$container->id]['image_url'] = (!empty($container->contentImage))? $container->contentImage->getWebUrl(): null;

                        if (!empty($container->preferenceEvento)) {
                            $event = $container->preferenceEvento;
                            $toret['contents'][$container->id]['event_location'] = $event->location . (!empty($event->location_entrance)? (' - ' . $event->location_entrance): '');
                            $toret['contents'][$container->id]['event_start_datetime'] = $event->date_start;
                            $toret['contents'][$container->id]['event_end_datetime'] = $event->date_end;
                        }
                        if (!empty($container->newsInnovazione)) {
                            $news = $container->newsInnovazione;
                            $toret['contents'][$container->id]['oi_news_cat'] = $news->main_category;
                            $toret['contents'][$container->id]['oi_news_pubdate'] = $news->publication_date;
                        }
                    }
                }
            }
        }
        return $toret;
    }

    private function getListOfTagByIds($ids, $onlyTargets = false) 
    {
        $toret = [];

        foreach ($ids as $id) {
            $tag = Tag::findOne($id);
            if ($onlyTargets) {
                $toret[$tag->root] = Tag::findOne($tag->root);
            } else {
                $toret[$tag->id] = $tag;
            }
        }

        return  $toret;
    }

    /**
     *
     * @param integer $userId
     * @return void 
     */
    public function actionGetUserById($userId)
    {
        $this->checkUserSecurity($userId);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $toret = [
            'status' => null,
            'messages' => null,
            'data' => null,
        ];

        /** @var UserProfile $userProfile */
        $userProfile = UserProfile::findOne(['user_id' => $userId]);
        if (empty($userProfile)){
            $toret['status'] = 'error';
            $toret['messages'][] = 'Anagrafica non trovata';
            return $toret;
        }

        $data = [
            'name' => $userProfile->nome,
            'surname' => $userProfile->cognome,
            'gender' => $userProfile->sesso,
            'fiscal_code' => $userProfile->codice_fiscale,
            'birth_date' => $userProfile->nascita_data,
            'municipality_id' => $userProfile->comune_residenza_id,
            'province_id' => $userProfile->provincia_residenza_id,
        ];

        foreach (UserProfileUtilityPc::getIDMFields($userId) as $key => $value){
            $keyVal = Inflector::camel2id($key,'_');
            $data[$keyVal] = UserProfileUtilityPc::getIDMLabels($key);
            $data[$keyVal] = $value;
        }

        $toret['status'] = 'ok';
        $toret['data']= $data;
        return $toret;
    }

    /**
     *
     * @param integer $id
     * @return void 
     */
    public function actionGetProvince($id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $toret = [
            'status' => null,
            'messages' => null,
            'data' => null,
        ];

        // All data
        if (is_null($id)) {
            $all = IstatProvince::find()->orderBy('nome')->asArray()->all();

            $toret['status'] = 'ok';
            $toret['data'] = $all;
            return $toret;
        }

        $province = IstatProvince::findOne(['id' => $id]);

        if(empty($province)){
            $toret['status'] = 'error';
            $toret['messages'][] = 'Provincia non trovata';
            return $toret;
        } 

        $toret['status'] = 'ok';
        $toret['data'] = $province->toArray();
        return $toret;
    }

    /**
     *
     * @param integer $id
     * @return void 
     */
    public function actionGetMunicipality($provinceId = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $toret = [
            'status' => null,
            'messages' => null,
            'data' => null,
        ];

        // All data
        if (!is_null($provinceId)) {
            $all = IstatComuni::find()->andWhere(['istat_province_id' => $provinceId])->orderBy('nome')->asArray()->all();

            $toret['status'] = 'ok';
            $toret['data'] = $all;
            return $toret;
        }

        if (!is_null($id)) {
            $comune = IstatComuni::findOne(['id' => $id]);

            if(empty($comune)){
                $toret['status'] = 'error';
                $toret['messages'][] = 'Comune non trovato';
                return $toret;
            } 
    
            $toret['status'] = 'ok';
            $toret['data'] = $comune->toArray();
            return $toret;
        }

        throw new InvalidArgumentException('Nessun parametro inviato, inviare almeno un parametro...');
    }

    /**
     *
     * @param integer $id
     * @return void 
     */
    public function actionUpdateUserById($userId)
    {
        $this->checkUserSecurity($userId);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $toret = [
            'status' => null,
            'messages' => null,
            'data' => null,
        ];

        try {

            /** @var UserProfile $userProfile */
            $userProfile = UserProfile::findOne(['user_id' => $userId]);

            if (empty($userProfile)) {
                throw new InvalidArgumentException('Lo userId inviato non corrispone a nesuna anagrafica a sistema');
            }

            $bodyParams = \Yii::$app->getRequest()->getBodyParams();

            $personalData = new PersonalData();
            $personalData->name = isset($bodyParams['name'])? $bodyParams['name']: null;
            $personalData->surname = isset($bodyParams['surname'])? $bodyParams['surname']: null;
            $personalData->gender = isset($bodyParams['gender'])? $bodyParams['gender']: null;
            $personalData->birth_date = isset($bodyParams['birth_date'])? $bodyParams['birth_date']: null;
            $personalData->residence_province = isset($bodyParams['residence_province'])? $bodyParams['residence_province']: null;
            $personalData->residence_city = isset($bodyParams['residence_city'])? $bodyParams['residence_city']: null;

            if ($personalData->validate()) {
                // salvataggio dei dati sull user profile

                $userProfile->nome = $personalData->name;
                $userProfile->cognome = $personalData->surname;
                
                $userProfile->sesso = (empty($personalData->gender))? null: (($personalData->gender == 'm')? 'Maschio': 'Femmina');
                
                $date = DateTime::createFromFormat ('d/m/Y', $personalData->birth_date);
                $userProfile->nascita_data = ($date)? $date->format('Y-m-d'): null;

                $userProfile->comune_residenza_id = $personalData->residence_city;
                $userProfile->provincia_residenza_id = $personalData->residence_province;

                $ret = $userProfile->save(false);
                if ($ret) {
                    $toret['status'] = 'ok';
                    $toret['data'] = $personalData->toArray();
                    foreach (UserProfileUtilityPc::getIDMFields($userId) as $key => $value){
                        $keyVal = Inflector::camel2id($key,'_');
                        $toret['data'][$keyVal] = UserProfileUtilityPc::getIDMLabels($key);
                        $toret['data'][$keyVal] = $value;
                    }
                    return $toret;
                } else {
                    $toret['status'] = 'error';
                    $toret['messages'][] = 'Errore imprevisto in fase di salvataggio dati...';
                    return $toret;
                }
                
            } else {
                VarDumper::dump($personalData->toArray(), $depth = 10, $highlight = false);
                $toret['status'] = 'error';
                $toret['messages'] = $personalData->errors;
                return $toret;
            }

        } catch (Exception $ex) {
            
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);

            $toret['status'] = 'error';
            $toret['messages'][] = $ex->getMessage();
            return $toret;
        }

        throw new InvalidArgumentException('I parametri passati hanno generato un errore inatteso...');      
    }

     /**
     *
     * @param integer $id
     * @return void 
     */
    public function actionUpdatePasswordByUserId($userId)
    {
        // annullo l'azione, con DL semplificazioni non sarà più possibile fare questa azione
        throw new ForbiddenHttpException('Azione bloccata: DL semplificazioni');

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $toret = [
            'status' => null,
            'messages' => null,
            'data' => null,
        ];

        try {

            /** @var UserProfile $userProfile */
            $userProfile = UserProfile::findOne(['user_id' => $userId]);

            if (empty($userProfile)) {
                throw new InvalidArgumentException('Lo userId inviato non corrispone a nesuna anagrafica a sistema');
            }

            $userProfile->user->generatePasswordResetToken();
            $userProfile->user->save(false);
            $sent = UserProfileUtility::sendPasswordResetMail($userProfile, null, null);
            if ($sent) {
                $appLink = \Yii::$app->params['platform']['frontendUrl']; //Yii::$app->urlManager->createAbsoluteUrl(['/']);
                $link = $appLink . '/' . AmosAdmin::getModuleName() . '/security/insert-auth-data?token=' . $userProfile->user->password_reset_token;
                $toret['status'] = 'ok';
                $toret['messages'][] = 'Email inviata correttamente all\'utente';
                $toret['data'] = [
                    'token' => $userProfile->user->password_reset_token,
                    'url' => $link,
                ];
                return $toret;
            } else {
                $toret['status'] = 'error';
                $toret['messages'][] = 'Impossibile inviare la mail per resettare la pawwsord';
                return $toret;
            }

        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);

            $toret['status'] = 'error';
            $toret['messages'][] = $ex->getMessage();
            return $toret;
        }

        throw new InvalidArgumentException('I parametri passati hanno generato un errore inatteso...');   
    }

    
    /**
     * 
     * @return type
     */
    public function actionGetAllFavorite($className, $userId)
    {

        $this->checkUserSecurity($userId);

        $notify = Yii::$app->getModule('notify');
        $toret = [
            'status' => null,
            'messages' => null,
            'data' => null,
        ];
        
        try {

            if (!empty($className) && (class_exists($className))) { 
                $userModel = AmosAdmin::instance()->createModel('User');

                if (!empty($userModel::findOne(['id' => $userId]))) {

                    $ids = $notify->getAllFavourites($className, $userId);
                    $query = PreferenceCampainChannelMm::find()
                        ->andWhere(['id' => ArrayHelper::map($ids,'content_id', 'content_id')])
                        ->andWhere(['preference_channel_id' => PreferenceChannel::APP_ID])
                    ;
                    $listOfComunications = $query->all();

                    /** @var PreferenceCampainChannelMm $campainChannel */
                    foreach ($listOfComunications as $campainChannel) {
                        $listItems = $this->getListOfTagByIds(ArrayHelper::map($campainChannel->preferenceCampain->preferenceCampainTagMms,'tag_id', 'tag_id'));
                        $listChannels = $this->getListOfTagByIds(ArrayHelper::map($campainChannel->preferenceCampain->preferenceCampainTagMms,'tag_id', 'tag_id'), true);
            
                        $toret['data'][$campainChannel->id]['id'] = $campainChannel->id;
                        $toret['data'][$campainChannel->id]['date'] = $campainChannel->date_app;
                        $toret['data'][$campainChannel->id]['title'] = $campainChannel->title;
                        $toret['data'][$campainChannel->id]['topics'] = $this->listOfTagToArray($listItems);
                        $toret['data'][$campainChannel->id]['targets'] = $this->listOfTagToArray($listChannels);
                        $toret['data'][$campainChannel->id]['contents_number'] = $this->getNumberOfContentByCampainChannel($campainChannel);
                    }
                    $toret['status'] = 'ok';

                    return $toret;
                
                } else {
                    throw new InvalidArgumentException('Utente non trovato');
                }
            } else {
                throw new InvalidArgumentException('className inesistente');
            }


        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
            $toret['status'] = 'ko';
            $toret['messages'][] = $ex->getMessage();
            return $toret;
        }

    }

    /**
     *
     * Attenzione, questa funzione deve fare il toggle del tag in base al canale app
     *
     * se deve accendere il tag allora deve aggiungere il solo canale app
     * se il tag è già acceso allora abbiamo due casi!
     * 1) se attivo il solo canale app allo va spento
     * 2) se sono attivi altri canali come sms o email allora va tolto solo i canale APP!
     *
     * Se deve accendere/spegnere il target... allora pochi controlli
     */
    public function actionPreferenceTagToggle()
    {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $toret = [
        'status' => null,
        'messages' => null,
        'data' => null,
    ];

    try {

        $bodyParams = \Yii::$app->getRequest()->getBodyParams();

        $userId = isset($bodyParams['user_id'])? $bodyParams['user_id']: null;
        $this->checkUserSecurity($userId);

        /** @var UserProfile $userProfile */
        $userProfile = UserProfile::findOne(['user_id' => $userId]);
        if (empty($userProfile)) {
            throw new InvalidArgumentException('Lo user_id inviato non corrispone a nesuna anagrafica a sistema');
        }

        $tagId = isset($bodyParams['tag_id'])? $bodyParams['tag_id']: null;
        $tag = Tag::findOne(['id' => $tagId]);
        if(empty($tag)) {
            throw new InvalidArgumentException('Lo tag_id inviato non corrispone a nesuna anagrafica a sistema');
        }

        $preferenceSelected = UserInterestTagUtility::getRegisteredUserInterestTag($userProfile, $tag);

        // gestione di un tag che è un TARGET
        // Il toggle deve accenderlo e spegnerlo banalmente
        if (!TopicTagUtility::tagIsTopic($tag)){
            if (empty($preferenceSelected)) {
                UserInterestTagUtility::saveRegisteredUserInterestTag($userProfile, 'simple-choice', $tag);
            } else {
                UserInterestTagUtility::removeRegisteredUserInterestTag($userProfile, $tag);
            }
        } else {
            // altrimenti è un TOPIC

            // VarDumper::dump(UserInterestTagUtility::isSetUserChannel($tag, $userProfile->user_id, PreferenceChannel::APP_ID), $depth = 10, $highlight = false);
            // TOGGLE della preferenza sul canale dell'APP
            if (UserInterestTagUtility::isSetUserChannel($tag, $userProfile->user_id, PreferenceChannel::APP_ID)){
                UserInterestTagUtility::deletePreferenceTopicChannel($userProfile->user_id, $tag, PreferenceChannel::APP_ID);
            } else {
                UserInterestTagUtility::addSinglePreferenceTopicChannel($userProfile->user_id, $tag, PreferenceChannel::APP_ID);

                // se accendo la preferenza e non è attivo il tag sull'utente allora attivo il tag all'utente
                if (empty($preferenceSelected)) {
                    // la aggiungo
                    UserInterestTagUtility::saveRegisteredUserInterestTag($userProfile, 'simple-choice', $tag);

                }
            }
        }

        $toret['status'] = 'ok';
        return $toret;

        } catch (Exception $ex) {
                
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);

            $toret['status'] = 'error';
            $toret['messages'][] = $ex->getMessage();
            return $toret;
        }

        throw new InvalidArgumentException('I parametri passati hanno generato un errore inatteso...');      
    }



    private function getNumberOfContentByCampainChannel($campainChannel)
    {
        $count = 0;
        foreach ($campainChannel->preferenceCampainSections as $section) {
            $count = $count + intval(PreferenceCampainContainer::find()->andWhere(['preference_campain_section_id' => $section->id])->count());
        }
        return $count;
    }

    /**
     *
     * @param type $code
     */
    protected function getError($code)
    {
        $ret = [];

        switch ($code) {
            case 1:
                $ret ['message'] = 'Utente già registrato';
                $ret ['code'] = $code;
                break;
            case 2:
                $ret ['message'] = 'Impossibile creare l\'anagrafica';
                $ret ['code'] = $code;
                break;
        }
        return $ret;
    }

    public function actionSendEmailOtp(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $toret = [
            'status' => null,
            'messages' => null,
            'data' => null,
        ];

        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $email = isset($bodyParams['email'])? $bodyParams['email']: null;
        $userId = isset($bodyParams['userId'])? $bodyParams['userId']: null;
        $this->checkUserSecurity($userId);

        $userProfile = UserProfile::findOne(['user_id' => $userId]);
        if (empty($userProfile)) {
            throw new InvalidArgumentException('Lo userId inviato non corrispone a nesuna anagrafica a sistema');
        }

        $val = new EmailValidator();
        if (!$val->validate($email)){
            throw new InvalidArgumentException('Email malformata');
        }

        try {
            if (!is_null($email)) {
                /** @var UserOtpCode $otp */
                $otp = UserProfileUtilityPc::generateOPT($userProfile->user);
                EmailUtility::sendUserMailValidationEmailOtp($otp->otp_code, $email, 'Lombardia Informa, validazione email di servizio');
                $toret['status'] = 'ok';
            } else {
                $toret['status'] = 'error';
                $toret['messages'] = 'Email nulla...';
            }
        } catch (NotificationEmailException $e) {
            $toret['status'] = 'error';
            $toret['messages'] = 'Impossibile inviare codice di validazione alla mail indicata';
        }

        return $toret;
    }

    public function actionSetEmail(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $toret = [
            'status' => null,
            'messages' => null,
            'data' => null,
        ];

        $bodyParams = \Yii::$app->getRequest()->getBodyParams();
        $email = isset($bodyParams['email'])? $bodyParams['email']: null;
        $userId = isset($bodyParams['userId'])? $bodyParams['userId']: null;
        $this->checkUserSecurity($userId);

        $otp = isset($bodyParams['otp'])? $bodyParams['otp']: null;

        $userProfile = UserProfile::findOne(['user_id' => $userId]);
        if (empty($userProfile)) {
            throw new InvalidArgumentException('Lo userId inviato non corrispone a nesuna anagrafica a sistema');
        }

        $val = new EmailValidator();
        if (!$val->validate($email)){
            throw new InvalidArgumentException('Email malformata');
        }

        if (UserOtpCode::isValidCodice($otp, UserOtpCode::TYPE_AUTH_EMAIL, $userId)) {
            if (!UserOtpCode::isExpired($otp, UserOtpCode::TYPE_AUTH_EMAIL, $userId)) {
                try {
                    $user = $userProfile->user;
                    $user->email = $email;
                    $user->save(false);
                    $toret['status'] = 'ok';
                } catch (Exception $e) {
                    $toret['status'] = 'error';
                    $toret['messages'] = 'Impossibile salvare la nuova email';
                }
            } else {
                $toret['status'] = 'error';
                $toret['messages'] = 'Codice di validazione scaduto';
            }
        } else {
            $toret['status'] = 'error';
            $toret['messages'] = 'Codice di validazione non valido';
        }

        return $toret;
    }

//    /**
//     *
//     * @param string $cod_tematica
//     * @return array
//     */
//    public function actionTopic($cod_tematica)
//    {
//        return Json::encode(['code' => 1, 'decription' => 'test']);
//    }
}