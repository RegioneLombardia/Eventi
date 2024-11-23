<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\agid\organizationalunit\controllers
 */

namespace open20\agid\organizationalunit\controllers;

use open20\amos\core\helpers\Html;
use open20\agid\person\Module;
use yii\helpers\Json;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Class AgidOrganizationalUnitController
 * This is the class for controller "AgidOrganizationalUnitController".
 * @package app\controllers
 */
class AgidOrganizationalUnitController extends \open20\agid\organizationalunit\controllers\base\AgidOrganizationalUnitController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),
                [
                    'access' => [
                        'class' => AccessControl::className(),
                        'ruleConfig' => [
                            'class' => AccessRule::className(),
                        ],
                        'rules' => [
                            [
                                'allow' => true,
                                'actions' => [
                                    'organizzazioni-ajax',
                                ],
                                'roles' => ['@']
                            ],
                        ],
                    ],
        ]);
    }

    public function beforeAction($action)
    {
        if (\Yii::$app->user->isGuest) {
            $titleSection = Module::t('amosorganizationalunit', '#menu_front_organizationalunit');
            $urlLinkAll   = '';
        } else {
            $titleSection = Module::t('amosorganizationalunit', '#menu_front_organizationalunit');
        }

        $labelCreate = 'Nuova';
        $titleCreate = 'Crea una nuova unità organizzativa';
        $urlCreate   = '/organizationalunit/agid-organizational-unit/create';

        $this->view->params = [
            'isGuest' => \Yii::$app->user->isGuest,
            'modelLabel' => 'persone',
            'titleSection' => $titleSection,
            'subTitleSection' => '',
            'urlLinkAll' => $urlLinkAll,
            'labelLinkAll' => '',
            'titleLinkAll' => '',
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

    public function actionOrganizzazioniAjax($q = null, $id = null, $myid = null)
    {
        $out = ['more' => false];
        if (!is_null($q)) {
            $query = new Query();
            $query->select('id, name AS text')
                ->from(\open20\agid\organizationalunit\models\AgidOrganizationalUnit::tableName())
                ->where('name LIKE :search', ['search' => "%".$q."%"])
                ->andWhere(['status' => 'AgidOrganizationalUnitWorkflow/VALIDATED'])
                ->andWhere(['deleted_at' => null])
                ->limit(50);

            if (!empty($myid)) {
                $query->andWhere(['<>', 'id', $myid]);
            }


            $command        = $query->createCommand();
            $data           = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => \open20\agid\organizationalunit\models\AgidOrganizationalUnit::findOne($id)->name];
        } else {
            $out['results'] = ['id' => 0, 'text' => \Yii::t('amosorganizationalunit', 'Nessun risultato trovato')];
        }
        return Json::encode($out);
    }
}