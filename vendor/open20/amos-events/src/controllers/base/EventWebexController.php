<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Proscriptions/proscription-default.txt to change this proscription
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace open20\amos\events\controllers\base;

use open20\amos\core\controllers\CrudController;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\events\AmosEvents;
use open20\amos\events\models\EventWebexAccounts;
use open20\amos\events\models\search\EventWebexAccountsSearch;
use open20\amos\events\models\search\EventWebexProfileSearch;
use Yii;
use yii\helpers\Url;

/**
 * Description of EventWebexController
 *
 */
class EventWebexController extends CrudController {

    /**
     * @var string $layout
     */
    public $layout = 'main';

    /**
     * @var AmosEvents $eventsModule
     */
    public $eventsModule = null;

    /**
     * @inheritdoc
     */
    public function init() {
        $this->eventsModule = AmosEvents::instance();

        $this->setModelObj(new EventWebexAccounts());
        $this->setModelSearch(new EventWebexAccountsSearch());

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosEvents::t('amosevents', '{tableIcon}' . Html::tag('p', AmosEvents::t('amosevents', 'Table')), [
                    'tableIcon' => AmosIcons::show('view-list-alt')
                ]),
                'url' => '?currentView=grid'
            ],
        ]);

        $language = \Yii::$app->request->get('language');
        if (!empty($language)) {
            \Yii::$app->language = $language;
        }

        parent::init();

        \Yii::$app->params['bsVersion'] = '4.x';
        \Yii::$app->view->params['customClassMainContent'] = 'box-container sidebar-setting';
        $this->setUpLayout('bootstrap-italia-layout-with-sidebar', []);
    }

    /**
     * Lists all EventWebexAccounts models.
     * @param string|null $layout
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex($layout = NULL) {
        Url::remember();
        $this->setDataProvider($this->modelSearch->search(Yii::$app->request->getQueryParams()));
        \Yii::$app->view->params['enableLayoutList'] = true;
        \Yii::$app->view->params['enableChangeView'] = true;
        return $this->render(
                        'index',
                        [
                            'dataProvider' => $this->getDataProvider(),
                            'model' => $this->getModelSearch(),
                            'currentView' => $this->getCurrentView(),
                        ]
        );
    }

    /**
     * Unlink the webex_mail from the user
     * @param integer|null $id
     * @return null
     */
    public function actionDelete($id = NULL) {
        if ($id) {
            $model = EventWebexAccounts::findOne($id);
            $role = \Yii::$app->authManager->getRole('EVENT_WEBEX_CREATOR');
            \Yii::$app->authManager->revoke($role, $model->user_id);
            $model->user_id = NULL;
            if ($model->save()) {
                \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', "#assegnazione_rimossa"));
            } else {
                \Yii::$app->session->addFlash('danger', AmosEvents::t('amosevents', "#rimozione_fallita"));
            }
        }
        return $this->redirect(['index']);
    }

    public function actionUpdate($id = NULL) {
        if ($id) {
            $model = EventWebexAccounts::findOne($id);
            $modelSearch = new EventWebexProfileSearch();
            $dataProvider = $modelSearch->search(\Yii::$app->request->get());

            return $this->render(
                            'update',
                            [
                                'modelWebexAccounts' => $model,
                                'modelSearch' => $modelSearch,
                                'dataProvider' => $dataProvider,
                            ]
            );
        }
        \Yii::$app->session->addFlash('danger', AmosEvents::t('amosevents', "#account_non_trovato"));
        return $this->redirect(['index']);
    }

    public function actionAssign($id = null, $user_id = null) {

        if ($id && $user_id) {
            $model = EventWebexAccounts::findOne($id);
            if ($model) {
                $role = \Yii::$app->authManager->getRole('EVENT_WEBEX_CREATOR');
                \Yii::$app->authManager->revoke($role, $model->user_id);
                \Yii::$app->authManager->assign($role, $user_id);

                $model->user_id = $user_id;
                if ($model->save()) {
                    \Yii::$app->authManager->deleteAllCache();
                    \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', "#assegnazione_aggiornata"));
                } else {
                    \Yii::$app->session->addFlash('danger', AmosEvents::t('amosevents', "#assegnazione_fallita"));
                }
            } else {
                Yii::$app->session->addFlash('danger', AmosEvents::t('amosevents', "#assegnazione_fallita"));
            }
        } else {
            \Yii::$app->session->addFlash('danger', AmosEvents::t('amosevents', "#assegnazione_fallita"));
        }

        return $this->redirect(['index']);
    }

}
