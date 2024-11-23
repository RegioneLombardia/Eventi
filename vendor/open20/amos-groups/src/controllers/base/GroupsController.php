<?php

namespace open20\amos\groups\controllers\base;

use open20\amos\admin\models\search\UserProfileSearch;
use open20\amos\admin\models\UserProfile;
use open20\amos\core\user\User;
use open20\amos\core\utilities\Email;
use open20\amos\groups\models\Groups;
use open20\amos\groups\models\GroupsMailer;
use open20\amos\groups\models\GroupsMembers;
use open20\amos\groups\models\search\GroupsSearch;
use open20\amos\groups\Module;
use Yii;
use open20\amos\core\controllers\CrudController;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\helpers\Html;
use open20\amos\core\helpers\T;
use yii\helpers\Url;

/**
 * GroupsController implements the CRUD actions for Groups model.
 */
class GroupsController extends CrudController
{
    public function init()
    {
        $this->setModelObj(new \open20\amos\groups\models\Groups());
        $this->setModelSearch(new \open20\amos\groups\models\search\GroupsSearch());

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => Yii::t('amoscore', '{iconaTabella}' . Html::tag('p', Yii::t('amoscore', 'Table')), [
                    'iconaTabella' => AmosIcons::show('view-list-alt')
                ]),
                'url' => '?currentView=grid'
            ],
            /*'list' => [
                'name' => 'list',
                'label' => Yii::t('amoscore', '{iconaLista}'.Html::tag('p',Yii::t('amoscore', 'List')), [
                    'iconaLista' => AmosIcons::show('view-list')
                ]),           
                'url' => '?currentView=list'
            ],
            'icon' => [
                'name' => 'icon',
                'label' => Yii::t('amoscore', '{iconaElenco}'.Html::tag('p',Yii::t('amoscore', 'Icons')), [
                    'iconaElenco' => AmosIcons::show('grid')
                ]),           
                'url' => '?currentView=icon'
            ],
            'map' => [
                'name' => 'map',
                'label' => Yii::t('amoscore', '{iconaMappa}'.Html::tag('p',Yii::t('amoscore', 'Map')), [
                    'iconaMappa' => AmosIcons::show('map')
                ]),       
                'url' => '?currentView=map'
            ],
            'calendar' => [
                'name' => 'calendar',
                'intestazione' => '', //codice HTML per l'intestazione che verrà caricato prima del calendario,
                                      //per esempio si può inserire una funzione $model->getHtmlIntestazione() creata ad hoc
                'label' => Yii::t('amoscore', '{iconaCalendario}'.Html::tag('p',Yii::t('amoscore', 'Calendar')), [
                    'iconaMappa' => AmosIcons::show('calendar')
                ]),       
                'url' => '?currentView=calendar'
            ],*/
        ]);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {



        $labelCreate = Module::t('amosgroups', 'Nuovo');
        $titleCreate = Module::t('amosgroups', 'Crea un nuovo gruppo');
        $urlCreate = '/groups/groups/create';
        $titleSection = Module::t('amosgroups', "Gruppi");


            $this->view->params = [
            'titleSection' => $titleSection,
            'labelCreate' => $labelCreate,
            'titleCreate' => $titleCreate,
            'urlCreate' => $urlCreate,
        ];

        if (!parent::beforeAction($action)) {
            return false;
        }

        // other custom code here
        return true;
    }

    /**
     * Lists all Groups models.
     * @return mixed
     */
    public function actionIndex($layout = NULL)
    {
        Url::remember();
        $this->setDataProvider($this->getModelSearch()->search(Yii::$app->request->getQueryParams()));
        return parent::actionIndex();
    }

    /**
     * Displays a single Groups model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        /** @var  $model Groups */
        $model = $this->findModel($id);
        $modelSearch = new GroupsSearch();
        $dataProviderSelected = $modelSearch->searchUsersSelected($model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', [
                'model' => $model,
                'dataProviderSelected' => $dataProviderSelected
            ]);
        }
    }

    /**
     * Creates a new Groups model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($idParent = null)
    {
        $this->layout = "@vendor/open20/amos-core/views/layouts/form";
        $model = new Groups();
        $groupsConfig = \Yii::$app->getModule(Module::getModuleName());
        $classNameParent = $groupsConfig->classNameParent;
        $modelSearch = new GroupsSearch();
        $dataProvider = $modelSearch->searchUsers();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->parent_id = $this->getParentId($idParent);
            if ($model->save()) {
                foreach (\Yii::$app->request->post('attrSelected') as $user_profile_id) {
                    $profile = UserProfile::findOne($user_profile_id);
                    $user = $profile->user;
                    $member = new GroupsMembers();
                    $member->user_id = $user->id;
                    $member->groups_id = $model->id;
                    $member->save();
                }
                Yii::$app->getSession()->addFlash('success', Yii::t('amoscore', 'Item created'));
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->addFlash('danger', Yii::t('amoscore', 'Item not created, check data'));
                return $this->render('create', [
                    'model' => $model,
                    'dataProvider' => $dataProvider
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'dataProvider' => $dataProvider
            ]);
        }
    }


    /**
     * Updates an existing Groups model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->layout = "@vendor/open20/amos-core/views/layouts/form";
        /** @var  $model Groups */
        $model = $this->findModel($id);
        $mailer = new GroupsMailer();

        $modelSearch = new GroupsSearch();
        $dataProvider = $modelSearch->searchUsers();
        $dataProviderSelected = $modelSearch->searchUsersSelected($model);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                // assignment of users to groups
                GroupsMembers::deleteAll(['groups_id' => $model->id]);
                if (!empty(\Yii::$app->request->post('attrSelected'))) {
                    foreach (\Yii::$app->request->post('attrSelected') as $user_profile_id) {
                        $profile = UserProfile::findOne($user_profile_id);
                        $user = $profile->user;
                        $member = new GroupsMembers();
                        $member->user_id = $user->id;
                        $member->groups_id = $model->id;
                        $member->save();
                    }
                }
                Yii::$app->getSession()->addFlash('success', Yii::t('amoscore', 'Item updated'));
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                Yii::$app->getSession()->addFlash('danger', Yii::t('amoscore', 'Item not updated, check data'));
                return $this->render('update', [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                    'dataProviderSelected' => $dataProviderSelected,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'dataProviderSelected' => $dataProviderSelected,
            ]);
        }
    }

    /**
     * Deletes an existing Groups model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        /** @var Groups $model */
        $model = $this->findModel($id);
        if ($model) {
            GroupsMembers::deleteAll(['groups_id' => $model->id]);
            $model->delete();
            if (!empty($model->deleted_at)) {
                Yii::$app->getSession()->addFlash('success', Yii::t('amoscore', 'Item deleted'));
            } else {
                Yii::$app->getSession()->addFlash('danger', Yii::t('amoscore', 'Item not deleted because of dependency'));
            }
        } else {
            Yii::$app->getSession()->addFlash('danger', Yii::t('amoscore', 'Item not found'));
        }
        return $this->redirect(['index']);
    }

    /**
     * @param null $idParent
     * @return null
     */
    public function getParentId($idParent = null)
    {
        $parent_id = $idParent;
        $groupsModule = Yii::$app->getModule(Module::getModuleName());
        $classNameParent = $groupsModule->classNameParent;
        $cwh = Yii::$app->getModule("cwh");
        $community = Yii::$app->getModule("community");
        if (!empty($classNameParent) && (!empty($idParent))) {
            $parentModel = $classNameParent::findOne($idParent);
            if (!empty($parentModel)) {
                $parent_id = $parentModel->id;
            }
        }
        // if we are navigating users inside a sprecific entity (eg. a community)
        // see users filtered by entity-user association table
        elseif (isset($cwh) && isset($community)) {
            $cwh->setCwhScopeFromSession();
            if (!empty($cwh->userEntityRelationTable)) {
                $entityId = $cwh->userEntityRelationTable['entity_id'];
                $parent_id = $entityId;
            }
        }

        return $parent_id;
    }


}
