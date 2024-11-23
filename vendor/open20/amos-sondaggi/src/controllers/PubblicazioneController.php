<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi\controllers
 * @category   CategoryName
 */

namespace open20\amos\sondaggi\controllers;

use open20\amos\admin\models\UserProfile;
use open20\amos\attachments\components\AttachmentsList;
use open20\amos\attachments\models\File;
use open20\amos\core\controllers\CrudController;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\user\User;
use open20\amos\dashboard\controllers\TabDashboardControllerTrait;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\models\GeneratoreSondaggio;
use open20\amos\sondaggi\models\search\SondaggiSearch;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\models\SondaggiDomande;
use open20\amos\sondaggi\models\SondaggiDomandePagine;
use open20\amos\sondaggi\models\SondaggiRisposte;
use open20\amos\sondaggi\models\SondaggiRisposteSessioni;
use open20\amos\sondaggi\utility\SondaggiUtility;
use open20\amos\sondaggi\widgets\icons\WidgetIconSondaggiGeneral;
use kartik\mpdf\Pdf;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;
use open20\amos\sondaggi\assets\ModuleRisultatiAsset;
use open20\amos\core\widget\WidgetAbstract;
use open20\amos\admin\AmosAdmin;

/**
 * Class PubblicazioneController
 * PubblicazioneController implements the CRUD actions for Sondaggi model.
 *
 * @property \open20\amos\sondaggi\models\Sondaggi $model
 * @property \open20\amos\sondaggi\models\search\SondaggiSearch $modelSearch
 *
 * @package open20\amos\sondaggi\controllers
 */
class PubblicazioneController extends CrudController
{

    use TabDashboardControllerTrait;
    public $base_dir;
    public $percorso_model;
    public $percorso_view;
    public $percorso_validator;
    public $alias_path;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config                   = []);
        if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
        if (!isset($this->alias_path)) $this->alias_path         = Yii::getAlias('@backend');
        if (!isset($this->base_dir)) $this->base_dir           = "sondaggi".DS."pubblicazione";
        if (!isset($this->percorso_model)) $this->percorso_model     = "backend\\sondaggi\\pubblicazione\\models\\q";
        if (!isset($this->percorso_view)) $this->percorso_view      = "backend\\sondaggi\\pubblicazione\\views\\q";
        if (!isset($this->percorso_validator)) $this->percorso_validator = "backend".DS."sondaggi".DS."validators".DS;
    }
    /**
     * @var string $layout
     */
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initDashboardTrait();

        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        $this->setModelObj(new Sondaggi());
        $this->setModelSearch(new SondaggiSearch());

        $this->setAvailableViews([
            'list' => [
                'name' => 'list',
                'label' => AmosIcons::show('view-list').Html::tag('p', AmosSondaggi::tHtml('amossondaggi', 'Lista')),
                'url' => '?currentView=list'
            ],
            'grid' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt').Html::tag('p',
                    AmosSondaggi::tHtml('amossondaggi', 'Tabella')),
                'url' => '?currentView=grid'
            ]
        ]);

        parent::init();
        if (!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->view->pluginIcon = 'ic ic-sondaggi';
        }
        $this->setUpLayout();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(),
                [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => [
                                'notifica',
                                'pubblica',
                                'pubblicazione',
                                'sondaggio-pubblico',
                                'sondaggi-pubblici',
                                'all-admin',
                                'genera-sondaggio',
                                'compila',
                                'ri-compila'
                            ],
                            'roles' => ['@']
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                'reinvia-mail-sondaggio'
                            ],
                            'roles' => ['ADMIN']
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                'own-interest',
                                'all',
                            ],
                            'roles' => ['COMPILATORE_SONDAGGI']
                        ],
                        [
                            'allow' => true,
                            'actions' => [
                                'own-interest',
                                'all',
                            ],
                            'roles' => ['COMPILATORE_AZIONI']
                        ],
                    ]
                ]
        ]);
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Base operations for list views
     * @param string $pageTitle
     * @param bool $setCurrentDashboard
     * @param bool $hideCreateNewBtn
     * @return string
     */
    protected function baseListsAction($pageTitle, $setCurrentDashboard = true, $hideCreateNewBtn = false)
    {
        Url::remember();
        $this->setTitleAndBreadcrumbs($pageTitle);
        $this->setListViewsParams($setCurrentDashboard, $hideCreateNewBtn);
        return $this->render('index',
                [
                'dataProvider' => $this->getDataProvider(),
                'model' => $this->getModelSearch(),
                'currentView' => $this->getCurrentView(),
                'availableViews' => $this->getAvailableViews(),
                'url' => ($this->url) ? $this->url : null,
                'parametro' => ($this->parametro) ? $this->parametro : null
        ]);
    }

    /**
     * This method is useful to set all common params for all list views.
     * @param bool $setCurrentDashboard
     * @param bool $hideCreateNewBtn
     */
    protected function setListViewsParams($setCurrentDashboard = true, $hideCreateNewBtn = false)
    {
        $this->setCreateNewBtnParams($hideCreateNewBtn);
        $this->setUpLayout('list');
        $this->view->params['currentDashboard'] = $this->getCurrentDashboard();
        $this->child_of                         = WidgetIconSondaggiGeneral::className();

        Yii::$app->session->set(AmosSondaggi::beginCreateNewSessionKey(), Url::previous());
        Yii::$app->session->set(AmosSondaggi::beginCreateNewSessionKeyDateTime(), date('Y-m-d H:i:s'));
    }

    /**
     * Set a view param used in \open20\amos\core\forms\CreateNewButtonWidget
     * @param bool $hideBtn
     */
    protected function setCreateNewBtnParams($hideBtn = false)
    {
//        Yii::$app->view->params['createNewBtnParams'] = [
//            'createNewBtnLabel' => AmosSondaggi::t('amossondaggi', 'Add new survey'),
//        ];
        if ($hideBtn) {
            Yii::$app->view->params['createNewBtnParams']['layout'] = '';
        }
    }

    /**
     * Used for set page title and breadcrumbs.
     * @param string $pageTitle News page title (ie. Created by news, ...)
     */
    public function setTitleAndBreadcrumbs($pageTitle)
    {
        Yii::$app->view->title                 = $pageTitle;
        Yii::$app->view->params['breadcrumbs'] = [
            ['label' => $pageTitle]
        ];
    }

    /**
     * Lists all models.
     * @param string|null $layout
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex($layout = null)
    {
        return $this->redirect(['/sondaggi/pubblicazione/all']);

        Url::remember();
        $this->setDataProvider($this->modelSearch->searchDominio(Yii::$app->request->getQueryParams()));
        return parent::actionIndex($layout);
    }

    /**
     * Lists all PartnershipProfiles models for ADMIN users.
     * @return string
     */
    public function actionAllAdmin()
    {
        $this->setDataProvider($this->modelSearch->searchAllAdmin(Yii::$app->request->getQueryParams()));
        return $this->baseListsAction(AmosSondaggi::t('amossondaggi', 'All admin'), false, true);
    }

    /**
     * @return string
     */
    public function actionOwnInterest()
    {
        $this->setDataProvider($this->modelSearch->searchOwnInterest(Yii::$app->request->getQueryParams()));

        $this->view->params['titleSection'] = AmosSondaggi::t('amossondaggi', 'Sondaggi di mio interesse ');
        $this->view->params['labelLinkAll'] = AmosSondaggi::t('amossondaggi', 'Tutti i sondaggi');
        $this->view->params['urlLinkAll']   = AmosSondaggi::t('amossondaggi', '/sondaggi/pubblicazione/all');
        $this->view->params['titleLinkAll'] = AmosSondaggi::t(
                'amossondaggi', 'Visualizza la lista di tutti i sondaggi'
        );
        return $this->baseListsAction(AmosSondaggi::t('amossondaggi', 'Sondaggi di mio interesse '), true, true);
    }

    /**
     * Lists all PartnershipProfiles models for ADMIN users.
     * @param string|null $currentView
     * @return string
     */
    public function actionAll()
    {
        $this->view->params['titleSection'] = AmosSondaggi::t('amossondaggi', 'Tutti i sondaggi');
        $this->view->params['labelLinkAll'] = AmosSondaggi::t('amossondaggi', 'Sondaggi di mio interesse ');
        $this->view->params['urlLinkAll']   = AmosSondaggi::t('amossondaggi', '/sondaggi/pubblicazione/own-interest');
        $this->view->params['titleLinkAll'] = AmosSondaggi::t(
                'amossondaggi', 'Visualizza la lista dei sondaggi di mio interesse'
        );
        $this->setDataProvider($this->modelSearch->searchAll(Yii::$app->request->getQueryParams()));
        return $this->baseListsAction(AmosSondaggi::t('amossondaggi', 'Tutti i sondaggi'), true, true);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionCreate()
    {
        return $this->redirect('/sondaggi/sondaggi/create');
    }

    /**
     * Displays a single PeiClassiUtenti model.
     * @param integer $id
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        $this->model = $this->findModel($id);
        if ($this->model->load(Yii::$app->request->post()) && $this->model->save()) {
            return $this->redirect(['view', 'id' => $this->model->id]);
        } else {
            return $this->render('view', ['model' => $this->model]);
        }
    }

    /**
     * Lists all PeiClassiUtenti models.
     * @return string
     */
    public function actionPubblicazione()
    {
        Url::remember();
        $dataprovider = $this->modelSearch->search(Yii::$app->request->getQueryParams(), 'admin-scope', null);
        $dataprovider->query->andWhere(['!=', 'status', Sondaggi::WORKFLOW_STATUS_BOZZA]);

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt').Html::tag('p',
                    AmosSondaggi::tHtml('amossondaggi', 'Tabella')),
                'url' => '?currentView=grid'
            ]
        ]);
        $this->setCurrentView($this->getAvailableView('grid'));

        $this->setDataProvider($dataprovider);
        $this->setListViewsParams(true, true);

        $this->view->params['titleSection'] = AmosSondaggi::t('amossondaggi', 'Pubblica i sondaggi');
        $this->view->params['labelLinkAll'] = AmosSondaggi::t('amossondaggi', 'Tutti i sondaggi');
        $this->view->params['urlLinkAll']   = AmosSondaggi::t('amossondaggi', '/sondaggi/pubblicazione/all');
        $this->view->params['titleLinkAll'] = AmosSondaggi::t('amossondaggi', 'Visualizza la lista di tutti i sondaggi');

        return $this->render('pubblicazione',
                [
                'dataProvider' => $this->getDataProvider(),
                'currentView' => $this->getCurrentView()
        ]);
    }

    /**
     * Genera i models e le view del sondaggio che si sta pubblicando
     * @param integer $idSondaggio L'id del sondaggi da pubblicare
     * @param string|null $url
     * @return \yii\web\Response
     */
    public function actionPubblica($idSondaggio, $url = null)
    {
        $dir_models = $this->alias_path.DS.$this->base_dir.DS."models".DS."q".$idSondaggio;
        $dir_views  = $this->alias_path.DS.$this->base_dir.DS."views".DS."q".$idSondaggio;

        if (!is_dir($dir_models)) {
            mkdir($dir_models, 0777, true);
        }
        if (!is_dir($dir_views)) {
            mkdir($dir_views, 0777, true);
        }
        $sondaggio  = Sondaggi::findOne(['id' => $idSondaggio]);
        $pagine     = $sondaggio->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
        $num_pagine = $pagine->count();
        $np         = 1;
        $generatore = new GeneratoreSondaggio();
        foreach ($pagine->all() as $pagina) {
            $generatore->creaValidator($this->percorso_validator, $pagina['id']);
            $generatore->creaView("backend".DS.$this->base_dir.DS."views".DS."q".$idSondaggio, $pagina['id'],
                $this->percorso_view.$idSondaggio);
            $generatore->creaModel("backend".DS.$this->base_dir.DS."models".DS."q".$idSondaggio, $pagina['id'],
                $this->percorso_validator, $this->percorso_model.$idSondaggio);
        }
        $sondaggio->status = Sondaggi::WORKFLOW_STATUS_VALIDATO;
        $sondaggio->save();
        if ($url) {
            Yii::$app->getSession()->addFlash('success',
                AmosSondaggi::tHtml('amossondaggi', 'Sondaggio pubblicato correttamente.'));
            return $this->redirect($url);
        } else {
            Yii::$app->getSession()->addFlash('success',
                AmosSondaggi::tHtml('amossondaggi', 'Sondaggio pubblicato correttamente.'));
            return $this->redirect('index');
        }
    }

    /**
     * Send notification e-mail to user by role
     * @param int $idSondaggio
     * @param string|null $url
     * @return \yii\web\Response
     */
    public function actionNotifica($idSondaggio, $url = null)
    {
        $sondaggio     = Sondaggi::findOne(['id' => $idSondaggio]);
        $pubblicazione = $sondaggio->getSondaggiPubblicaziones();
        $subject       = $pubblicazione->one()->mail_subject;
        $message       = $pubblicazione->one()->mail_message;
        $module        = \Yii::$app->controller->module;
        $email         = \Yii::$app->getModule('email');
        if (!empty($module) && isset($module->enableNotificationEmailByRoles) && $module->enableNotificationEmailByRoles
            == true && !empty($email) && !empty($module->defaultEmailSender)) {
            foreach ($pubblicazione->all() as $key => $value) {
                $role  = $value->ruolo;
                $users = User::find();
                foreach ($users->all() as $user) {
                    if (!empty(trim($user->email)) && \Yii::$app->authManager->checkAccess($user->id, $role)) {
                        $email->queue($module->defaultEmailSender, $user->email, $subject, $message);
                    }
                }
            }
        }
        if ($url) {
            Yii::$app->getSession()->addFlash('success',
                AmosSondaggi::tHtml('amossondaggi', 'Notifiche aggiunte alla coda di invio correttamente.'));
            return $this->redirect($url);
        } else {
            Yii::$app->getSession()->addFlash('success',
                AmosSondaggi::tHtml('amossondaggi', 'Notifiche aggiunte alla coda di invio correttamente.'));
            return $this->redirect('index');
        }
    }

    /**
     * Action che permette di compilare il sondaggio
     * @param int $id Id del sondaggio da compilare
     * @param int|null $idPagina
     * @param int|null $utente User ID
     * @param null $idSessione
     * @param null $accesso
     * @param null $url
     * @return string
     */
    public function actionCompila($id, $idPagina = null, $utente = null, $idSessione = null, $accesso = null,
                                  $url = null)
    {
        $draft              = false;
        $pageNonCompilabile = '/pubblicazione/non_compilabile';
        $thankYouPage       = '/pubblicazione/compilato';
        $this->setUpLayout('main');
        if (!$utente) {
            $utente = Yii::$app->getUser()->getId();
        }
        $this->model = Sondaggi::findOne(['id' => $id]);

        if ($this->model->status !== Sondaggi::WORKFLOW_STATUS_VALIDATO && !\Yii::$app->user->can('AMMINISTRAZIONE_SONDAGGI')) {
            \Yii::$app->getSession()->addFlash('danger',
                AmosSondaggi::tHtml('amossondaggi', 'Sondaggio non compilabile.'));
            return $this->goBack();
        }

        ModuleRisultatiAsset::register(\Yii::$app->getView());

        $pagineQuery         = $this->model->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
        $pagine              = $pagineQuery->all();
        $primaPagina         = $pagine[0]['id'];
        $ultimaPagina        = $pagine[$pagineQuery->count() - 1]['id'];
        $prossimaPagina      = null;
        $arrayPag            = [];
        $completato          = false;
        $domandeWithFilesIds = [];
        foreach ($pagine as $Pag) {
            $arrayPag[] = $Pag['id'];
        }
        if ($idPagina) {
            if ($idPagina != $ultimaPagina) {
                $idPag          = array_search($idPagina, $arrayPag);
                $prossimaPagina = $arrayPag[$idPag + 1];
            }
        } else {
            $idPagina       = $primaPagina;
            $idPag          = array_search($primaPagina, $arrayPag);
            $prossimaPagina = (isset($arrayPag[$idPag + 1])) ? $arrayPag[$idPag + 1] : 0;
        }

        $risposteWithFiles = [];
        if ($primaPagina) {
            $paginaSondaggio        = SondaggiDomandePagine::findOne($primaPagina);
            $query                  = $paginaSondaggio->getSondaggiDomandesWithFiles();
            $risposteWithFiles      = [];
            $domandeWithFilesModels = $query->all();
            foreach ((Array) $domandeWithFilesModels as $domandaSondaggio) {
                $domandeWithFilesIds []        = $domandaSondaggio->id;
                $risposta                      = new SondaggiRisposte();
                $risposta->sondaggi_domande_id = $domandaSondaggio->id;
                $risposteWithFiles []          = $risposta;
            }
        }

        $valutatori = 0;
        if ($this->model->abilita_criteri_valutazione == 1) {
            $valutatori = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_id' => $id])->count();
        }

        if (Yii::$app->request->isPost) {
            $data     = Yii::$app->request->post();
            $idPagina = $data['idPagina'];
            if ($idPagina != $ultimaPagina) {
                $idPag          = array_search($idPagina, $arrayPag);
                $prossimaPagina = $arrayPag[$idPag + 1];
            } else {
                $completato = true;
            }
            if (isset($_POST['truesubmitdraft'])) {
                $draft = $_POST['truesubmitdraft'];
                if ($draft) {
                    $completato = false;
                }
            }
            $utente      = $data['utente'];
            $idSessione  = $data['idSessione'];
            $percorso    = $this->percorso_model.$id."\\Pagina_".$idPagina;
            $percorsoNew = $this->percorso_model.$id."\\Pagina_".$prossimaPagina;
            $newModel    = new $percorso;
            if ($newModel->load($data) && $newModel->validate()) {
                $newModel->save($idSessione, $accesso, $completato);

//                foreach ($domandeWithFilesIds as $idDomanda) {
//                    $files = UploadedFile::getInstanceByName("domanda_$idDomanda");
//                    \Yii::$app->getModule('attachments')->attachFile($files->tempName, new SondaggiRisposte(), $attribute = "domanda_$idDomanda", $dropOriginFile = true, $saveWithoutModel = true);
//                }
//                foreach ($domandeWithFilesModels as $doma
                if ($completato) {
                    if ($this->model->send_pdf_via_email) {
                        $path = "uploads/Sondaggio_compilato".$idSessione.'_'.time().".pdf";
                        $this->generateSondaggiPdf($idSessione, $this->model->id, $path);
                            SondaggiUtility::sendEmailSondaggioCompilato($this->model, $idSessione, $path);
                    }

                    return $this->render($thankYouPage,
                            ['url' => $url, 'pubblicazioni' => $this->model->getSondaggiPubblicaziones()]);
                } else if ($draft) {
                    //	pr($idSessione); die;
                    $newModel->save($idSessione, $accesso, $completato);

                    $this->addFlash('success', AmosSondaggi::tHtml('amossondaggi', 'Bozza salvata con successo'));
                    $this->redirect(['/azioni/azioni/index']);
                    //return $this->redirect(['/sondaggi/pubblicazione/ri-compila?id='.$idSessione]);
                } else {
                    $prossimoModel = new $percorsoNew;
                    return $this->render('/pubblicazione/compila',
                            ['model' => $prossimoModel, 'idSessione' => $idSessione, 'idPagina' => $prossimaPagina, 'utente' => $utente,
                            'id' => $id, 'risposteWithFiles' => $risposteWithFiles, 'ultimaPagina' => $ultimaPagina]);
                }
            } else {
                return $this->render('/pubblicazione/compila',
                        ['model' => $newModel, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'utente' => $utente,
                        'id' => $id, 'risposteWithFiles' => $risposteWithFiles, 'ultimaPagina' => $ultimaPagina]);
            }
        } else {
            $inCorso = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_id' => $id])->andWhere(['user_id' => $utente]);
            if ($inCorso->count() == 0) {
                if ($primaPagina) {
                    $paginaSondaggio = SondaggiDomandePagine::findOne($primaPagina);
                    $query           = $paginaSondaggio->getSondaggiDomandesWithFiles();

                    foreach ($query->all() as $domandaSondaggio) {
                        $risposta                      = new SondaggiRisposte();
                        $risposta->sondaggi_domande_id = $domandaSondaggio->id;
                        $risposteWithFiles []          = $risposta;
                    }
                }

                $idSondaggio           = $id;
                $sessione              = new SondaggiRisposteSessioni();
                $sessione->begin_date  = date('Y-m-d H:i:s');
                $sessione->end_date    = null;
                $sessione->sondaggi_id = $id;
                $sessione->user_id     = $utente;
                $sessione->save();
                $idSessione            = $sessione->id;
                $modelloPagina         = $this->percorso_model.$id."\\Pagina_".$primaPagina;
                $pagina                = new $modelloPagina;
                return $this->render('/pubblicazione/compila',
                        ['model' => $pagina, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'utente' => $utente, 'id' => $id,
                        'risposteWithFiles' => $risposteWithFiles, 'ultimaPagina' => $ultimaPagina]);
            } else {
                $nonCompletato = 0;
                foreach ($inCorso->all() as $InCorso) {
                    if ($InCorso['completato'] == 0) {
                        $nonCompletato = $InCorso['id'];
                    }
                }
                if ($nonCompletato) {
                    if ($primaPagina) {
                        $paginaSondaggio     = SondaggiDomandePagine::findOne($primaPagina);
                        $query               = $paginaSondaggio->getSondaggiDomandesWithFiles();
                        $domandeWithFilesIds = [];
                        foreach ($query->all() as $domandaSondaggio) {
                            $domandeWithFilesIds [] = $domandaSondaggio->id;
                        }
                    }
                    $sessione = SondaggiRisposteSessioni::findOne(['id' => $nonCompletato]);
                    $risposte = $sessione->getSondaggiRispostes();
                    if ($risposte->count() > 0) {
                        //se esistono risposte date al sondaggio
                        $arrDomande = [];
                        foreach ($risposte->all() as $risposta) {
                            $arrDomande[] = $risposta['sondaggi_domande_id'];
                        }
                        $domande  = SondaggiDomande::find()->andWhere(['IN', 'id', $arrDomande])->orderBy('ordinamento ASC');
                        $idPagina = $domande->all()[$domande->count() - 1]['sondaggi_domande_pagine_id'];
                        if ($idPagina != $ultimaPagina) {
                            $idPag          = array_search($idPagina, $arrayPag);
                            $prossimaPagina = $arrayPag[$idPag + 1];
                        }
                        $percorso          = $this->percorso_model.$id."\\Pagina_".$idPagina;
                        $newModel          = new $percorso;
                        $tutteDomande      = SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $idPagina]);
                        $risposteWithFiles = [];
                        foreach ($tutteDomande->all() as $precompilaRisposte) {
                            $rispostaDomandaQuery          = SondaggiRisposte::find()->andWhere(['sondaggi_domande_id' => $precompilaRisposte['id']])->andWhere([
                                'sondaggi_risposte_sessioni_id' => $nonCompletato]);
                            $rispostaDomandaWithFilesQuery = clone $rispostaDomandaQuery;
                            $risposteWithFiles             = ArrayHelper::merge($risposteWithFiles,
                                    $rispostaDomandaWithFilesQuery->andWhere(['sondaggi_risposte.sondaggi_domande_id' => $domandeWithFilesIds])->all());
                            $rispostaDomandaCount          = $rispostaDomandaQuery->count();
                            if ($rispostaDomandaCount == 1) {
                                $rispostaDomanda = $rispostaDomandaQuery->one();
                                if ($rispostaDomanda['risposta_libera'] != null) {
                                    $idDom            = "domanda_".$precompilaRisposte['id'];
                                    $newModel->$idDom = $rispostaDomanda['risposta_libera'];
                                } else {
                                    $idDom            = "domanda_".$precompilaRisposte['id'];
                                    $newModel->$idDom = $rispostaDomanda['sondaggi_risposte_predefinite_id'];
                                }
                            } else if ($rispostaDomandaCount > 1) {
                                $arrRisposte = [];
                                foreach ($rispostaDomandaQuery->all() as $risposteSingole) {
                                    $arrRisposte[] = $risposteSingole['sondaggi_risposte_predefinite_id'];
                                }
                                $idDom            = "domanda_".$precompilaRisposte['id'];
                                $newModel->$idDom = $arrRisposte;
                            }
                        }

                        return $this->render('/pubblicazione/compila',
                                ['model' => $newModel, 'idPagina' => $idPagina, 'idSessione' => $nonCompletato, 'id' => $id,
                                'utente' => $utente, 'risposteWithFiles' => $risposteWithFiles, 'ultimaPagina' => $ultimaPagina]);
                    } else {//se non esistono risposte date al sondaggio
                        $newModel = null;
                        $percorso = ($this->percorso_model.$id."\\Pagina_".$primaPagina);
                        if (class_exists($percorso)) {
                            $newModel = new $percorso;
                            return $this->render('/pubblicazione/compila',
                                    ['model' => $newModel, 'idPagina' => $primaPagina, 'idSessione' => $nonCompletato, 'id' => $id,
                                    'utente' => $utente, 'risposteWithFiles' => $risposteWithFiles, 'ultimaPagina' => $ultimaPagina]);
                        } else {
                            return $this->redirect(['/sondaggi/sondaggi/index']);
                        }
                    }
                } else {//Se non esiste un sondaggio incompleto da completare                     
                    if (($inCorso->count() < $this->model->compilazioni_disponibili || $this->model->compilazioni_disponibili
                        == 0) && (($this->model->abilita_criteri_valutazione == 1 && $valutatori < $this->model->n_max_valutatori)
                        || $this->model->n_max_valutatori == 0 || $this->model->abilita_criteri_valutazione == 0)) {
                        $idSondaggio           = $id;
                        $sessione              = new SondaggiRisposteSessioni();
                        $sessione->begin_date  = date('Y-m-d H:i:s');
                        $sessione->end_date    = null;
                        $sessione->sondaggi_id = $id;
                        $sessione->user_id     = $utente;
                        $sessione->save();
                        $idSessione            = $sessione->id;
                        $modelloPagina         = $this->percorso_model.$id."\\Pagina_".$primaPagina;
                        $pagina                = new $modelloPagina;
                        return $this->render('/pubblicazione/compila',
                                ['model' => $pagina, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'utente' => $utente,
                                'id' => $id, 'risposteWithFiles' => $risposteWithFiles, 'ultimaPagina' => $ultimaPagina]);
                    } else {
                        return $this->render($pageNonCompilabile,
                                ['url' => $url, 'pubblicazioni' => $this->model->getSondaggiPubblicaziones()]);
                    }
                }
            }
        }
    }

    public function actionRiCompila($id, $url = null)
    {
        $draft              = false;
        $pageNonCompilabile = '/pubblicazione/non_compilabile';
        $thankYouPage       = '/pubblicazione/ricompilato';
        $this->setUpLayout('main');
        if (!$utente) {
            $utente = Yii::$app->getUser()->getId();
        }
        $sessione = SondaggiRisposteSessioni::findOne($id);
        if (empty($sessione) || empty($sessione->user_id)) {
            \Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amossondaggi', 'Sondaggio inesistente.'));
            return $this->goBack();
        }
        $this->model = Sondaggi::findOne(['id' => $sessione->sondaggi_id]);
        $idSondaggio = $this->model->id;

        if ($this->model->status !== Sondaggi::WORKFLOW_STATUS_VALIDATO && !\Yii::$app->user->can('AMMINISTRAZIONE_SONDAGGI')) {
            \Yii::$app->getSession()->addFlash('danger',
                AmosSondaggi::tHtml('amossondaggi', 'Sondaggio non compilabile.'));
            return $this->goBack();
        }

        ModuleRisultatiAsset::register(\Yii::$app->getView());

        $pagine         = $this->model->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
        $primaPagina    = $pagine->all()[0]['id'];
        $ultimaPagina   = $pagine->all()[$pagine->count() - 1]['id'];
        $prossimaPagina = null;
        $arrayPag       = [];
        $completato     = false;

        foreach ($pagine->all() as $Pag) {
            $arrayPag[] = $Pag['id'];
        }


        $domandeWithFilesIds = [];

        if ($idPagina) {
            if ($idPagina != $ultimaPagina) {
                $idPag          = array_search($idPagina, $arrayPag);
                $prossimaPagina = $arrayPag[$idPag + 1];
            }
        } else {
            $idPagina       = $primaPagina;
            $idPag          = array_search($primaPagina, $arrayPag);
            $prossimaPagina = (isset($arrayPag[$idPag + 1])) ? $arrayPag[$idPag + 1] : 0;
        }

        $risposteWithFiles = [];
        if ($primaPagina) {
            $paginaSondaggio        = SondaggiDomandePagine::findOne($primaPagina);
            $query                  = $paginaSondaggio->getSondaggiDomandesWithFiles();
            $risposteWithFiles      = [];
            $domandeWithFilesModels = $query->all();
            foreach ((Array) $domandeWithFilesModels as $domandaSondaggio) {
                $domandeWithFilesIds []        = $domandaSondaggio->id;
                $risposta                      = new SondaggiRisposte();
                $risposta->sondaggi_domande_id = $domandaSondaggio->id;
                $risposteWithFiles []          = $risposta;
            }
        }

        $valutatori = 0;
        if ($this->model->abilita_criteri_valutazione == 1) {
            $valutatori = SondaggiRisposteSessioni::find()->andWhere(['id' => $id])->count();
        }

        if (Yii::$app->request->isPost) {
            $data     = Yii::$app->request->post();
            $idPagina = $data['idPagina'];
            if ($idPagina != $ultimaPagina) {
                $idPag          = array_search($idPagina, $arrayPag);
                $prossimaPagina = $arrayPag[$idPag + 1];
            } else {
                $completato = true;
            }

            if (isset($_POST['truesubmitdraft'])) {
                $draft = $_POST['truesubmitdraft'];
                if ($draft) {
                    $completato = false;
                }
            }
            $utente      = $sessione->user_id;
            $idSessione  = $id;
            $percorso    = $this->percorso_model.$idSondaggio."\\Pagina_".$idPagina;
            $percorsoNew = $this->percorso_model.$idSondaggio."\\Pagina_".$prossimaPagina;
            $newModel    = new $percorso;
            if ($newModel->load($data) && $newModel->validate()) {
                $newModel->save($id, null, $completato);

//                foreach ($domandeWithFilesIds as $idDomanda) {
//                    $files = UploadedFile::getInstanceByName("domanda_$idDomanda");
//                    \Yii::$app->getModule('attachments')->attachFile($files->tempName, new SondaggiRisposte(), $attribute = "domanda_$idDomanda", $dropOriginFile = true, $saveWithoutModel = true);
//                }
//                foreach ($domandeWithFilesModels as $doma
                if ($completato) {
                    if ($this->model->send_pdf_via_email) {
                        $path = "uploads/Sondaggio_compilato".$idSessione.'_'.time().".pdf";
                        $this->generateSondaggiPdf($idSessione, $this->model->id, $path);
                        SondaggiUtility::sendEmailSondaggioCompilato($this->model, $idSessione, $path);
                    }

                    return $this->render($thankYouPage,
                            ['url' => $url, 'pubblicazioni' => $this->model->getSondaggiPubblicaziones()]);
                } else if ($draft) {
                    $newModel->save($id, null, $completato);
                    $sessione->completato = 0;
                    $sessione->end_date   = null;
                    $sessione->save();

                    $this->addFlash('success', AmosSondaggi::tHtml('amossondaggi', 'Bozza salvata con successo'));
                    // $this->redirect(['/azioni/azioni/index']);
                    return $this->goBack();
                } else {
                    $prossimoModel = new $percorsoNew;
                    return $this->render('/pubblicazione/compila',
                            ['model' => $prossimoModel, 'idSessione' => $idSessione, 'idPagina' => $prossimaPagina, 'utente' => $utente,
                            'id' => $idSondaggio, 'risposteWithFiles' => $risposteWithFiles, 'ultimaPagina' => $ultimaPagina]);
                }
            } else {
                return $this->render('/pubblicazione/compila',
                        ['model' => $newModel, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'utente' => $utente,
                        'id' => $idSondaggio, 'risposteWithFiles' => $risposteWithFiles, 'ultimaPagina' => $ultimaPagina]);
            }
        } else {


            $risposte = $sessione->getSondaggiRispostes();
            if ($risposte->count() > 0) {
                //se esistono risposte date al sondaggio
                $arrDomande = [];
                foreach ($risposte->all() as $risposta) {
                    $arrDomande[] = $risposta['sondaggi_domande_id'];
                }
                $domande  = SondaggiDomande::find()->andWhere(['IN', 'id', $arrDomande])->orderBy('ordinamento ASC');
                $idPagina = $domande->all()[$domande->count() - 1]['sondaggi_domande_pagine_id'];
                if ($idPagina != $ultimaPagina) {
                    $idPag          = array_search($idPagina, $arrayPag);
                    $prossimaPagina = $arrayPag[$idPag + 1];
                }
                $percorso          = $this->percorso_model.$idSondaggio."\\Pagina_".$idPagina;
                $newModel          = new $percorso;
                $tutteDomande      = SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $idPagina]);
                $risposteWithFiles = [];
                foreach ($tutteDomande->all() as $precompilaRisposte) {
                    $rispostaDomandaQuery          = SondaggiRisposte::find()->andWhere(['sondaggi_domande_id' => $precompilaRisposte['id']])->andWhere([
                        'sondaggi_risposte_sessioni_id' => $id]);
                    $rispostaDomandaWithFilesQuery = clone $rispostaDomandaQuery;
                    $risposteWithFiles             = ArrayHelper::merge($risposteWithFiles,
                            $rispostaDomandaWithFilesQuery->andWhere(['sondaggi_risposte.sondaggi_domande_id' => $domandeWithFilesIds])->all());
                    $rispostaDomandaCount          = $rispostaDomandaQuery->count();
                    if ($rispostaDomandaCount == 1) {
                        $rispostaDomanda = $rispostaDomandaQuery->one();
                        if ($rispostaDomanda['risposta_libera'] != null) {
                            $idDom            = "domanda_".$precompilaRisposte['id'];
                            $newModel->$idDom = $rispostaDomanda['risposta_libera'];
                        } else {
                            $idDom            = "domanda_".$precompilaRisposte['id'];
                            $newModel->$idDom = $rispostaDomanda['sondaggi_risposte_predefinite_id'];
                        }
                    } else if ($rispostaDomandaCount > 1) {
                        $arrRisposte = [];
                        foreach ($rispostaDomandaQuery->all() as $risposteSingole) {
                            $arrRisposte[] = $risposteSingole['sondaggi_risposte_predefinite_id'];
                        }
                        $idDom            = "domanda_".$precompilaRisposte['id'];
                        $newModel->$idDom = $arrRisposte;
                    }
                }

                return $this->render('/pubblicazione/compila',
                        ['model' => $newModel, 'idPagina' => $idPagina, 'idSessione' => $id, 'id' => $idSondaggio,
                        'utente' => $utente, 'risposteWithFiles' => $risposteWithFiles, 'ultimaPagina' => $ultimaPagina]);
            } else {//se non esistono risposte date al sondaggio
                $newModel = null;
                $percorso = ($this->percorso_model.$idSondaggio."\\Pagina_".$primaPagina);
                if (class_exists($percorso)) {
                    $newModel = new $percorso;
                    return $this->render('/pubblicazione/compila',
                            ['model' => $newModel, 'idPagina' => $primaPagina, 'idSessione' => $nonCompletato, 'id' => $idSondaggio,
                            'utente' => $utente, 'risposteWithFiles' => $risposteWithFiles, 'ultimaPagina' => $ultimaPagina]);
                } else {
                    return $this->redirect(['/sondaggi/sondaggi-domande-pagine/index', 'idSondaggio' => $idSondaggio]);
                }
            }
        }
    }

    /**
     * sondaggi/pubblicazione/reinvia-mail-sondaggio?id=24&idSessione=204
     * @param type $id
     * @param type $sessione
     */
    public function actionReinviaMailSondaggio($id, $idSessione)
    {
        try {
            $path        = "uploads/Sondaggio_compilato".$idSessione.'_'.time().".pdf";
            $this->model = Sondaggi::findOne(['id' => $id]);
            $this->generateSondaggiPdf($idSessione, $this->model->id, $path);
            if ($this->model->send_pdf_via_email) {
                SondaggiUtility::sendEmailSondaggioCompilato($this->model, $idSessione, $path);
            }
            pr('OK');
        } catch (\Exception $e) {
            pr($e->getMessage(), 'Errore');
        }
    }
    /**
     * Action che permette di compilare il sondaggio di accesso
     * @param integer $id Id dell'accesso
     *//* TODO TODO TODO TODO TODO TODO TODO TODO TODO
      public function actionCompilaSondaggioAccesso($id, $url = null) {
      $sessioni = SondaggiRisposteSessioni::find()->andWhere(['pei_accessi_servizi_facilitazione_id' => $id])->andWhere(['completato' => 0]);
      $idSessione = $sessioni->one()['id'];
      if ($sessioni->count() > 0) {
      $idSondaggio = $sessioni->one()['sondaggi_id'];
      $this->model = Sondaggi::findOne(['id' => $idSondaggio]);
      $pagine = $this->model->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
      $primaPagina = $pagine->all()[0]['id'];
      $ultimaPagina = $pagine->all()[$pagine->count() - 1]['id'];
      $prossimaPagina = null;
      $arrayPag = [];
      $completato = false;
      $precompilato = PeiAccessiServiziFacilitazioneConfigurazioneSondaggi::findOne(['sondaggi_id' => $idSondaggio])->precompilato;
      $vecchiQuest = SondaggiRisposteSessioni::find()->andWhere(['user_id' => $sessioni->one()['user_id']])->andWhere(['sondaggi_id' => $idSondaggio])->orderBy('updated_at, id DESC');
      $verifica = ($vecchiQuest->count() > 0) ? 1 : 0;

      foreach ($pagine->all() as $Pag) {
      $arrayPag[] = $Pag['id'];
      }
      if (Yii::$app->request->isPost) {
      $data = Yii::$app->request->post();
      $idPagina = $data['idPagina'];
      if ($idPagina != $ultimaPagina) {
      $IdPag = array_search($idPagina, $arrayPag);
      $prossimaPagina = $arrayPag[$IdPag + 1];
      } else {
      $completato = true;
      }
      $utente = $data['utente'];
      $idSessione = $data['idSessione'];
      $percorso = $this->percorso_model . $idSondaggio . "\\Pagina_" . $idPagina;
      $percorsoNew = $this->percorso_model . $idSondaggio . "\\Pagina_" . $prossimaPagina;
      $newModel = new $percorso;
      if ($newModel->load($data) && $newModel->validate()) {
      $newModel->save($idSessione, $id, $completato);
      if ($completato) {
      $url = Yii::$app->urlManager->createUrl([
      '/puntopei/pei-accessi-servizi-facilitazione/update',
      'id' => $id,
      'verifica' => true
      ]);
      return $this->redirect($url);
      } else {
      $ProssimoModel = new $percorsoNew;
      if ($verifica && $precompilato) {
      $vecchioSondaggio = $vecchiQuest->one()['id'];
      $TutteDomande = SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $prossimaPagina]);
      foreach ($TutteDomande->all() as $precompilaRisposte) {
      $RispostaDomanda = SondaggiRisposte::find()->andWhere(['sondaggi_domande_id' => $precompilaRisposte['id']])->andWhere(['sondaggi_risposte_sessioni_id' => $vecchioSondaggio]);
      if ($RispostaDomanda->count() == 1) {
      if ($RispostaDomanda->one()['risposta_libera'] != null) {
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $ProssimoModel->$idDom = $RispostaDomanda->one()['risposta_libera'];
      } else {
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $ProssimoModel->$idDom = $RispostaDomanda->one()['sondaggi_risposte_predefinite_id'];
      }
      } else if ($RispostaDomanda->count() > 1) {
      $arrRisposte = [];
      foreach ($RispostaDomanda->all() as $RisposteSingole) {
      $arrRisposte[] = $RisposteSingole['sondaggi_risposte_predefinite_id'];
      }
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $ProssimoModel->$idDom = $arrRisposte;
      }
      }
      }
      return $this->render('/pubblicazione/compila_sondaggio_accesso', ['model' => $ProssimoModel, 'idSessione' => $idSessione, 'idPagina' => $prossimaPagina, 'utente' => $utente, 'id' => $idSondaggio, 'idAccesso' => $id]);
      }
      } else {
      return $this->render('/pubblicazione/compila_sondaggio_accesso', ['model' => $newModel, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'utente' => $utente, 'id' => $idSondaggio, 'idAccesso' => $id]);
      }
      } else {
      $inCorso = SondaggiRisposteSessioni::findOne(['id' => $idSessione]);

      $risposte = $inCorso->getSondaggiRispostes();
      if ($risposte->count() > 0) {
      //se esistono risposte date al sondaggio
      $arrDomande = [];
      foreach ($risposte->all() as $Risposta) {
      $arrDomande[] = $Risposta['sondaggi_domande_id'];
      }
      $domande = SondaggiDomande::find()->andWhere(['IN', 'id', $arrDomande])->orderBy('ordinamento ASC');
      $idPagina = $domande->all()[$domande->count() - 1]['sondaggi_domande_pagine_id'];
      if ($idPagina != $ultimaPagina) {
      $IdPag = array_search($idPagina, $arrayPag);
      $prossimaPagina = $arrayPag[$IdPag + 1];
      }
      $percorso = $this->percorso_model . $idSondaggio . "\\Pagina_" . $idPagina;
      $newModel = new $percorso;
      $TutteDomande = SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $idPagina]);
      foreach ($TutteDomande->all() as $precompilaRisposte) {
      $RispostaDomanda = SondaggiRisposte::find()->andWhere(['sondaggi_domande_id' => $precompilaRisposte['id']])->andWhere(['sondaggi_risposte_sessioni_id' => $idSessione]);
      if ($RispostaDomanda->count() == 1) {
      if ($RispostaDomanda->one()['risposta_libera'] != null) {
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $newModel->$idDom = $RispostaDomanda->one()['risposta_libera'];
      } else {
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $newModel->$idDom = $RispostaDomanda->one()['sondaggi_risposte_predefinite_id'];
      }
      } else if ($RispostaDomanda->count() > 1) {
      $arrRisposte = [];
      foreach ($RispostaDomanda->all() as $RisposteSingole) {
      $arrRisposte[] = $RisposteSingole['sondaggi_risposte_predefinite_id'];
      }
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $newModel->$idDom = $arrRisposte;
      }
      }
      return $this->render('/pubblicazione/compila_sondaggio_accesso', ['model' => $newModel, 'idPagina' => $idPagina, 'idSessione' => $idSessione, 'id' => $idSondaggio, 'utente' => $inCorso->user_id, 'idAccesso' => $id]);
      } else {//se non esistono risposte date al sondaggio
      $percorso = $this->percorso_model . $idSondaggio . "\\Pagina_" . $primaPagina;
      $newModel = new $percorso;
      if ($verifica && $precompilato) {
      $vecchioSondaggio = $vecchiQuest->one()['id'];
      $TutteDomande = SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $primaPagina]);
      foreach ($TutteDomande->all() as $precompilaRisposte) {
      $RispostaDomanda = SondaggiRisposte::find()->andWhere(['sondaggi_domande_id' => $precompilaRisposte['id']])->andWhere(['sondaggi_risposte_sessioni_id' => $vecchioSondaggio]);
      if ($RispostaDomanda->count() == 1) {
      if ($RispostaDomanda->one()['risposta_libera'] != null) {
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $newModel->$idDom = $RispostaDomanda->one()['risposta_libera'];
      } else {
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $newModel->$idDom = $RispostaDomanda->one()['sondaggi_risposte_predefinite_id'];
      }
      } else if ($RispostaDomanda->count() > 1) {
      $arrRisposte = [];
      foreach ($RispostaDomanda->all() as $RisposteSingole) {
      $arrRisposte[] = $RisposteSingole['sondaggi_risposte_predefinite_id'];
      }
      $idDom = "domanda_" . $precompilaRisposte['id'];
      $newModel->$idDom = $arrRisposte;
      }
      }
      }
      return $this->render('/pubblicazione/compila_sondaggio_accesso', ['model' => $newModel, 'idPagina' => $primaPagina, 'idSessione' => $idSessione, 'id' => $idSondaggio, 'utente' => $inCorso->user_id, 'idAccesso' => $id]);
      }
      }
      } else {
      $url = Yii::$app->urlManager->createUrl([
      '/puntopei/pei-accessi-servizi-facilitazione/update',
      'id' => $id,
      'verifica' => true
      ]);
      return $this->redirect($url);
      }
      }

      public function actionSondaggioPubblicoAttivita() {
      $this->layout = '@vendor/open20/amos/core/views/layouts/sondaggio';
      $this->model = new \open20\amos\sondaggi\models\SondaggiPubblicazione();

      if (\Yii::$app->request->isPost) {
      if (isset(\Yii::$app->request->post()['SondaggiPubblicazione']['attivita'])) {
      $attivita = \Yii::$app->request->post()['SondaggiPubblicazione']['attivita'];
      $verificaAtt = \backend\modules\attivitaformative\models\PeiAttivitaFormative::findOne(['codice_attivita' => $attivita]);
      if (count($verificaAtt) == 1) {
      $idTipologia = $verificaAtt->getAreaFormativa()->one()['id'];
      $verificaSondaggio = \open20\amos\sondaggi\models\SondaggiPubblicazione::find()->leftJoin(Sondaggi::tableName(), 'id = sondaggi_id')->andWhere(['tipologie_attivita' => $idTipologia])->andWhere(['sondaggi_stato_id' => 3])->andWhere(['ruolo' => 'PUBBLICO'])->orderBy('sondaggi_id DESC');
      if ($verificaSondaggio->count() > 0) {
      $sondaggio = $verificaSondaggio->one()['sondaggi_id'];
      return $this->render('sondaggio_pubblico_attivita', [
      'model' => $this->model,
      'id' => $sondaggio,
      'attivita' => $attivita,
      ]);
      }
      }
      Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amossondaggi', 'E\' necessario inserire un codice attività valido per poter compilare il sondaggio.'));
      } else {
      Yii::$app->getSession()->addFlash('danger', AmosSondaggi::tHtml('amossondaggi', 'E\' necessario inserire il codice dell\'attività svolta per poter compilare il sondaggio.'));
      }
      }
      return $this->render('sondaggio_pubblico_attivita', [
      'model' => $this->model
      ]);
      } */

    /**
     * Action che permette di compilare il sondaggio pubblico
     * @param integer $id Id del sondaggio da compilare
     * @param null $idPagina
     * @param null $idSessione
     * @param null $accesso
     * @param null $url
     * @param null $attivita
     * @param bool $inizio
     * @param bool $libero
     * @return string|\yii\web\Response
     */
    public function actionSondaggioPubblico($id, $idPagina = null, $idSessione = null, $accesso = null, $url = null,
                                            $attivita = null, $inizio = false, $libero = false)
    {
        $this->layout = '@vendor/open20/amos/core/views/layouts/sondaggio';

        if ($libero && $id) {
            $verificaSondaggio = \open20\amos\sondaggi\models\SondaggiPubblicazione::find()->innerJoin(Sondaggi::tableName(),
                    'id = sondaggi_id')->andWhere(['id' => $id])->andWhere(['tipologie_attivita' => 0])->andWhere(['status' => Sondaggi::WORKFLOW_STATUS_VALIDATO])->andWhere([
                'ruolo' => 'PUBBLICO']);
            if ($verificaSondaggio->count() == 1) {
                $idAttivita     = null;
                $this->model    = Sondaggi::findOne(['id' => $id]);
                $pagine         = $this->model->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
                $primaPagina    = $pagine->all()[0]['id'];
                $ultimaPagina   = $pagine->all()[$pagine->count() - 1]['id'];
                $prossimaPagina = null;
                $arrayPag       = [];
                $completato     = false;
                foreach ($pagine->all() as $Pag) {
                    $arrayPag[] = $Pag['id'];
                }
                if ($idPagina) {
                    if ($idPagina != $ultimaPagina) {
                        $idPag          = array_search($idPagina, $arrayPag);
                        $prossimaPagina = $arrayPag[$idPag + 1];
                    }
                } else {
                    $idPagina       = $primaPagina;
                    $idPag          = array_search($primaPagina, $arrayPag);
                    $prossimaPagina = (isset($arrayPag[$idPag + 1])) ? $arrayPag[$idPag + 1] : 0;
                }
                if (Yii::$app->request->isPost && !$inizio) {
                    $data     = Yii::$app->request->post();
                    $idPagina = $data['idPagina'];
                    if ($idPagina != $ultimaPagina) {
                        $idPag          = array_search($idPagina, $arrayPag);
                        $prossimaPagina = $arrayPag[$idPag + 1];
                    } else {
                        $completato = true;
                    }

                    $idSessione  = $data['idSessione'];
                    $percorso    = $this->percorso_model.$id."\\Pagina_".$idPagina;
                    $percorsoNew = $this->percorso_model.$id."\\Pagina_".$prossimaPagina;
                    $newModel    = new $percorso;
                    if ($newModel->load($data) && $newModel->validate()) {
                        $newModel->save($idSessione, $accesso, $completato);
                        if ($completato) {
                            return $this->render('/pubblicazione/sondaggio_pubblico_completato', ['url' => $url]);
                        } else {
                            $prossimoModel = new $percorsoNew;
                            return $this->render('/pubblicazione/sondaggio_pubblico',
                                    ['model' => $prossimoModel, 'idSessione' => $idSessione, 'idPagina' => $prossimaPagina,
                                    'id' => $id, 'attivita' => $attivita, 'inizio' => false, 'libero' => true]);
                        }
                    } else {
                        return $this->render('/pubblicazione/sondaggio_pubblico',
                                ['model' => $newModel, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'id' => $id,
                                'attivita' => $attivita, 'inizio' => false, 'libero' => true]);
                    }
                } else {
                    // $inCorso = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_id' => $id]);
                    // if ($inCorso->count() == 0) {
                    $idSondaggio                         = $id;
                    $sessione                            = new SondaggiRisposteSessioni();
                    $sessione->begin_date                = date('Y-m-d H:i:s');
                    $sessione->end_date                  = null;
                    $sessione->sondaggi_id               = $id;
                    $sessione->pei_attivita_formative_id = $idAttivita;
                    $sessione->save();
                    $idSessione                          = $sessione->id;
                    $modelloPagina                       = $this->percorso_model.$id."\\Pagina_".$primaPagina;
                    $pagina                              = new $modelloPagina;
                    return $this->render('/pubblicazione/sondaggio_pubblico',
                            ['model' => $pagina, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'id' => $id, 'attivita' => $attivita,
                            'inizio' => false, 'libero' => true]);
                }
            } else {
                return $this->redirect('sondaggi-pubblici');
            }
        } else if (!$attivita || !$id) {
            return $this->redirect('sondaggio-pubblico-attivita');
        } else {
            $modelAttivita     = \backend\modules\attivitaformative\models\PeiAttivitaFormative::findOne(['codice_attivita' => $attivita]);
            $idAttivita        = $modelAttivita->id;
            $tipologieAttivita = $modelAttivita->getTags()->andWhere(['lvl' => 1])->andWhere(['root' => 1])->one()['id'];
            $verificaSondaggio = \open20\amos\sondaggi\models\SondaggiPubblicazione::find()->innerJoin(Sondaggi::tableName(),
                    'id = sondaggi_id')->andWhere(['id' => $id])->andWhere(['tipologie_attivita' => $tipologieAttivita])->andWhere([
                    'status' => Sondaggi::WORKFLOW_STATUS_VALIDATO])->andWhere(['ruolo' => 'PUBBLICO']);
            if ($verificaSondaggio->count() == 1) {
                $this->model    = Sondaggi::findOne(['id' => $id]);
                $pagine         = $this->model->getSondaggiDomandePagines()->orderBy('ordinamento, id ASC');
                $primaPagina    = $pagine->all()[0]['id'];
                $ultimaPagina   = $pagine->all()[$pagine->count() - 1]['id'];
                $prossimaPagina = null;
                $arrayPag       = [];
                $completato     = false;
                foreach ($pagine->all() as $Pag) {
                    $arrayPag[] = $Pag['id'];
                }
                if ($idPagina) {
                    if ($idPagina != $ultimaPagina) {
                        $idPag          = array_search($idPagina, $arrayPag);
                        $prossimaPagina = $arrayPag[$idPag + 1];
                    }
                } else {
                    $idPagina       = $primaPagina;
                    $idPag          = array_search($primaPagina, $arrayPag);
                    $prossimaPagina = (isset($arrayPag[$idPag + 1])) ? $arrayPag[$idPag + 1] : 0;
                }
                if (Yii::$app->request->isPost && !$inizio) {
                    $data     = Yii::$app->request->post();
                    $idPagina = $data['idPagina'];
                    if ($idPagina != $ultimaPagina) {
                        $idPag          = array_search($idPagina, $arrayPag);
                        $prossimaPagina = $arrayPag[$idPag + 1];
                    } else {
                        $completato = true;
                    }

                    $idSessione  = $data['idSessione'];
                    $percorso    = $this->percorso_model.$id."\\Pagina_".$idPagina;
                    $percorsoNew = $this->percorso_model.$id."\\Pagina_".$prossimaPagina;
                    $newModel    = new $percorso;
                    if ($newModel->load($data) && $newModel->validate()) {
                        $newModel->save($idSessione, $accesso, $completato);
                        if ($completato) {
                            return $this->render('/pubblicazione/sondaggio_pubblico_compilato',
                                    ['url' => $url, 'pubblicazioni' => $this->model->getSondaggiPubblicaziones()]);
                        } else {
                            $prossimoModel = new $percorsoNew;
                            return $this->render('/pubblicazione/sondaggio_pubblico',
                                    ['model' => $prossimoModel, 'idSessione' => $idSessione, 'idPagina' => $prossimaPagina,
                                    'id' => $id, 'attivita' => $attivita, 'inizio' => false, 'libero' => false]);
                        }
                    } else {
                        return $this->render('/pubblicazione/sondaggio_pubblico',
                                ['model' => $newModel, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'id' => $id,
                                'attivita' => $attivita, 'inizio' => false, 'libero' => false]);
                    }
                } else {
                    // $inCorso = SondaggiRisposteSessioni::find()->andWhere(['sondaggi_id' => $id]);
                    // if ($inCorso->count() == 0) {
                    $idSondaggio                         = $id;
                    $sessione                            = new SondaggiRisposteSessioni();
                    $sessione->begin_date                = date('Y-m-d H:i:s');
                    $sessione->end_date                  = null;
                    $sessione->sondaggi_id               = $id;
                    $sessione->pei_attivita_formative_id = $idAttivita;
                    $sessione->save();
                    $idSessione                          = $sessione->id;
                    $modelloPagina                       = $this->percorso_model.$id."\\Pagina_".$primaPagina;
                    $pagina                              = new $modelloPagina;
                    return $this->render('/pubblicazione/sondaggio_pubblico',
                            ['model' => $pagina, 'idSessione' => $idSessione, 'idPagina' => $idPagina, 'id' => $id, 'attivita' => $attivita,
                            'inizio' => false, 'libero' => false]);
                }
            } else {
                return $this->redirect('sondaggio-pubblico-attivita');
            }
        }
    }

    /**
     * @param int $id
     * @return string
     */
    public function actionSondaggiPubblici($id = 0)
    {
        $this->layout = '@vendor/open20/amos/core/views/layouts/sondaggio';
        $models       = \open20\amos\sondaggi\models\SondaggiPubblicazione::find()->innerJoin(Sondaggi::tableName(),
                'id = sondaggi_id')->andWhere(['tipologie_attivita' => 0])->andWhere(['status' => Sondaggi::WORKFLOW_STATUS_VALIDATO])->andWhere([
                'ruolo' => 'PUBBLICO'])->select('sondaggi_id as id');
        $this->model  = Sondaggi::find()->andWhere(['IN', 'id', $models])->orderBy('titolo ASC');

        if ($id) {
            $verifica = \open20\amos\sondaggi\models\SondaggiPubblicazione::find()->innerJoin(Sondaggi::tableName(),
                    'id = sondaggi_id')->andWhere(['id' => $id])->andWhere(['tipologie_attivita' => 0])->andWhere(['status' => Sondaggi::WORKFLOW_STATUS_VALIDATO])->andWhere([
                'ruolo' => 'PUBBLICO']);
            if ($verifica->count() == 1) {
                return $this->render('sondaggio_pubblico',
                        [
                        'id' => $id,
                        'libero' => true,
                ]);
            }
        }
        return $this->render('sondaggi_pubblici', [
                'model' => $this->model
        ]);
    }

    /**
     * @param $id
     * @param $sondaggioId
     * @return mixed
     */
    public function actionGeneraSondaggio($id, $sondaggioId)
    {
        return $this->generateSondaggiPdf($id, $sondaggioId);
    }
    /**
     * @param $id
     * @throws \yii\web\NotFoundHttpException
     */

    /**
     * @param $id
     * @throws \yii\web\NotFoundHttpException
     */
    public function generateSondaggiPdf($id, $sondaggioId, $path = null)
    {
        $this->model = $this->findModel($sondaggioId);
        $xlsData     = [];

        // INTESTAZIONE EXCEL
        $xlsData[0] = ["Nome", "Cognome", "Email"];
        $domande    = $this->model->getSondaggiDomandes()->orderBy('ordinamento ASC')->all();
        $count      = 1;


        // CORPO FILE EXCEL
        $sondRisposta = SondaggiRisposteSessioni::find()
            ->distinct()
            ->leftJoin('user_profile', 'user_profile.user_id = sondaggi_risposte_sessioni.user_id')
            ->innerJoinWith('user', 'user.id = user_profile.user_id')
            ->leftJoin('sondaggi_risposte',
                'sondaggi_risposte_sessioni.id = sondaggi_risposte.sondaggi_risposte_sessioni_id')
            ->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $sondaggioId])
            ->andWhere(['sondaggi_risposte_sessioni.id' => $id])
//            ->andWhere(['user_profile.user_id' => \Yii::$app->user->id])
            ->one();

        $allRisposte = SondaggiRisposte::find()
                ->andWhere(['sondaggi_risposte_sessioni_id' => $sondRisposta->id])
                ->andWhere(['is not', 'sondaggi_risposte_predefinite_id', null])
                ->select('sondaggi_risposte_predefinite_id as risposta')
                ->asArray()->all();

        $allRisposteLib = SondaggiRisposte::find()
                ->andWhere(['sondaggi_risposte_sessioni_id' => $sondRisposta->id])
                ->andWhere(['is not', 'risposta_libera', null])
                ->select('sondaggi_domande_id as risposta')
                ->asArray()->all();



        $allRisposte    = ArrayHelper::map($allRisposte, 'risposta', 'risposta');
        $allRisposteLib = ArrayHelper::map($allRisposteLib, 'risposta', 'risposta');



        $row               = 1;
        $profile           = UserProfile::find()->andWhere(['user_id' => $sondRisposta->user_id])->one();
        $xlsData [$row][0] = $profile->nome;
        $xlsData [$row][1] = $profile->cognome;
        $xlsData [$row][2] = $profile->user->email;
        $session_id        = $sondRisposta->id;



        /** @var  $domanda SondaggiDomande */
        $colum       = 3;
        $rispCond    = null;
        $rispCondLib = null;
        foreach ($domande as $domanda) {
            if ($domanda->domanda_condizionata == 1) {
                $rispCond = ArrayHelper::map($domanda->getSondaggiRispostePredefinitesCondizionate()->asArray()->all(),
                        'id', 'id');
            } else if (!empty($domanda->domanda_condizionata_testo_libero)) {
                $rispCondLib[$domanda->domanda_condizionata_testo_libero] = $domanda->domanda_condizionata_testo_libero;
            }


            if ((empty($rispCondLib) && empty($rispCond)) || ((!empty($rispCond) || !empty($rispCondLib)) && (!empty(array_intersect_key($allRisposte,
                        $rispCond)) || !empty(array_intersect_key($allRisposteLib, $rispCondLib))))) {

                $xlsData[0][] = "D.".$count." ".$domanda->domanda;
                $count ++;

                $query = $domanda->getRispostePerUtente($profile->user_id, $session_id);


                // RISPOSTE LIBERE
                if ($domanda->sondaggi_domande_tipologie_id == 6 || $domanda->sondaggi_domande_tipologie_id == 5) {
//                    pr($query->one()->risposta_libera, 'D. ' . $domanda->id);
                    $risposta = $query->one();
                    if ($risposta) {
                        $xlsData [$row][$colum] = str_replace(['<', '>'], ' ', $risposta->risposta_libera);
                    } else {
                        $xlsData [$row][$colum] = '';
                    }
                    //ALLEGATI
                } else if ($domanda->sondaggi_domande_tipologie_id == 13) {
                    $risposta = $query->one();
                    if ($risposta) {
                        $xlsData [$row][$colum] = \Yii::$app->formatter->asDate($risposta->risposta_libera);
                    } else {
                        $xlsData [$row][$colum] = '';
                    }
                } else if ($domanda->sondaggi_domande_tipologie_id == 12) {
                    $xlsData [$row][$colum] = '';
                } else if ($domanda->sondaggi_domande_tipologie_id == 10 || $domanda->sondaggi_domande_tipologie_id == 11) {
                    $risposta = $query->one();
                    if ($risposta) {
                        $attribute = 'domanda_'.$domanda->id;
                        if (!empty($risposta->$attribute)) {
                            $attachments    = $risposta->$attribute;
                            $listAttachUrls = [];
                            $risposteString = "<ul>";
                            /** @var  $attach File */
                            foreach ($attachments as $attach) {
                                $risposteString .= "<li><a href='".\Yii::$app->params['platform']['backendUrl'].$attach->getUrl()."'>".$attach->name."</a></li>";
                            }
                            $risposteString         .= '</ul>';
                            $xlsData [$row][$colum] = $risposteString;
//                        $xlsData [$row][$colum] = "<ul><li>".implode("</li><li>", $listAttachUrls)."</ul>";
//                            $xlsData [$row][$colum] = implode("\n", $listAttachUrls);
                        }
                    } else {
                        $xlsData [$row][$colum] = '';
                    }
                } else {

                    $risposteArray = [];
                    foreach ($query->all() as $risposta) {
                        if ($risposta->sondaggiRispostePredefinite) {

                            $risposteArray [] = $risposta->sondaggiRispostePredefinite->risposta;
                        }
                    }
//                    $xlsData [$row][$colum] = implode("\n", $risposteArray);

                    $xlsData [$row][$colum] = "<ul><li>".implode("</li><li>", $risposteArray)."</li></ul>";

//                    pr(implode(',', $risposteArray), 'D. ' . $domanda->id);
                }
                $colum++;
            }
        }


        return $this->savePdf($xlsData, $this->model, $sondRisposta, $path);
    }

    /**
     * @param $data
     * @param $modelSondaggio
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function savePdf($data, $modelSondaggio, $modelSondaggioRisposta, $path = null)
    {
        $content = $this->renderPartial('@vendor/open20/amos-sondaggi/src/views/sondaggi/_view_pdf',
            [
            'data' => $data,
            'sondaggio' => $modelSondaggio,
            'rispostaModel' => $modelSondaggioRisposta
        ]);
//        $footer = $this->renderPartial('@vendor/open20/amos-proposte-collaborazione-een/src/views/een-expr-of-interest/_pdf_footer', ['model' => $eenExpr]);


        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'content' => $content,
            'cssInline' => '',
            'options' => ['title' => ''],
            'methods' => [
                'SetFooter' => ['{PAGENO}']
            ],
        ]);

//        $pdf->getApi()->SetHTMLFooter($footer);
        $pdf->getApi()->SetMargins(0, 0, 20);
//        $pdf->getApi()->SetAutoPageBreak(TRUE, 25);
        $pdf->getApi()->margin_header = '6px';
        $pdf->getApi()->margin_footer = '10px';

        if (!empty($path)) {
            return $pdf->output($content, $path, Pdf::DEST_FILE);
        } else {
            return $pdf->output($content, "Sondaggio_compilato.pdf", Pdf::DEST_DOWNLOAD);
        }
    }

    public function beforeAction($action)
    {
        if (\Yii::$app->user->isGuest) {
            $titleSection = AmosSondaggi::t('amossondaggi', 'Sondaggi');
            $urlLinkAll   = '';

            $ctaLoginRegister = Html::a(
                    AmosSondaggi::t('amossondaggi', 'accedi o registrati alla piattaforma'),
                    isset(\Yii::$app->params['linkConfigurations']['loginLinkCommon']) ? \Yii::$app->params['linkConfigurations']['loginLinkCommon']
                        : \Yii::$app->params['platform']['backendUrl'].'/'.AmosAdmin::getModuleName().'/security/login',
                    [
                    'title' => AmosSondaggi::t(
                        'amossondaggi', 'Clicca per accedere o registrarti alla piattaforma {platformName}',
                        ['platformName' => \Yii::$app->name]
                    )
                    ]
            );
            $subTitleSection  = Html::tag(
                    'p',
                    AmosSondaggi::t(
                        'amossondaggi', 'Per partecipare alla creazione di nuove notizie, {ctaLoginRegister}',
                        ['ctaLoginRegister' => $ctaLoginRegister]
                    )
            );
        } else {
            $titleSection = AmosSondaggi::t('amossondaggi', 'Sondaggi di mio interesse');
            $labelLinkAll = AmosSondaggi::t('amossondaggi', 'Tutti i sondaggi');
            $urlLinkAll   = AmosSondaggi::t('amossondaggi', '/sondaggi/pubblicazione/all');
            $titleLinkAll = AmosSondaggi::t('amossondaggi', 'Visualizza la lista di tutti i sondaggi');

            $subTitleSection = Html::tag('p', AmosSondaggi::t('amossondaggi', ''));
        }
        $hideCreate = false;
        if (!\Yii::$app->user->can('SONDAGGI_CREATE')) {
            $hideCreate = true;
        }
        $labelCreate = AmosSondaggi::t('amossondaggi', 'Nuovo');
        $titleCreate = AmosSondaggi::t('amossondaggi', 'Crea un nuovo sondaggio');
        $labelManage = AmosSondaggi::t('amossondaggi', 'Gestisci');
        $titleManage = AmosSondaggi::t('amossondaggi', 'Gestisci i sondaggi');
        $urlCreate   = AmosSondaggi::t('amossondaggi', '/sondaggi/sondaggi/create');
        $urlManage   = AmosSondaggi::t('amossondaggi', '#');

        $this->view->params = [
            'isGuest' => \Yii::$app->user->isGuest,
            'modelLabel' => 'sondaggi',
            'titleSection' => $titleSection,
            'subTitleSection' => $subTitleSection,
            'urlLinkAll' => $urlLinkAll,
            'labelLinkAll' => $labelLinkAll,
            'titleLinkAll' => $titleLinkAll,
            'labelCreate' => $labelCreate,
            'titleCreate' => $titleCreate,
            'labelManage' => $labelManage,
            'titleManage' => $titleManage,
            'urlCreate' => $urlCreate,
            'urlManage' => $urlManage,
            'hideCreate' => $hideCreate
        ];

        if (!parent::beforeAction($action)) {
            return false;
        }

        // other custom code here

        return true;
    }

    /**
     *
     * @return array
     */
    public static function getManageLinks()
    {
        $links = [];

        $isCommunityManager = false;
        $moduleCommunity = \Yii::$app->getModule('community');
        if (!empty($moduleCommunity)) {
            $isCommunityManager = \open20\amos\community\utilities\CommunityUtil::isLoggedCommunityManager();
        }

        $links[] = [
            'title' => AmosSondaggi::t('amossondaggi', 'Visualizza la lista dei sondaggi di tuo interesse'),
            'label' => AmosSondaggi::t('amossondaggi', 'Sondaggi di mio interesse '),
            'url' => '/sondaggi/pubblicazione/own-interest',
        ];

        $links[] = [
            'title' => AmosSondaggi::t('amossondaggi', 'Visualizza la lista di tutti i sondaggi'),
            'label' => AmosSondaggi::t('amossondaggi', 'Tutti i sondaggi'),
            'url' => '/sondaggi/pubblicazione/all',
        ];

        if (\Yii::$app->user->can(\open20\amos\sondaggi\widgets\icons\WidgetIconSondaggiAdministration::class) || $isCommunityManager) {
            $links[] = [
                'title' => AmosSondaggi::t('amossondaggi', 'Amministra i sondaggi'),
                'label' => AmosSondaggi::t('amossondaggi', 'Amministra'),
                'url' => '/sondaggi/sondaggi/index',
            ];
        }

        if (\Yii::$app->user->can(\open20\amos\sondaggi\widgets\icons\WidgetIconPubblicaSondaggi::class)) {
            $links[] = [
                'title' => AmosSondaggi::t('amossondaggi', 'Amministra la pubblicazione dei sondaggi'),
                'label' => AmosSondaggi::t('amossondaggi', 'Pubblica'),
                'url' => '/sondaggi/pubblicazione/pubblicazione'
            ];
        }

        return $links;
    }
}