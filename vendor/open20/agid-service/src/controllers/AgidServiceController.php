<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\agid\service\controllers
 * @category   CategoryName
 */

namespace open20\agid\service\controllers;

use open20\amos\core\helpers\Html;
use open20\agid\person\Module;
use open20\agid\service\models\AgidServiceType;
use open20\agid\service\models\AgidServiceTypeRoles;
use yii\helpers\Json;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Class AgidServiceController
 * This is the class for controller "AgidServiceController".
 * @package open20\agid\service\controllers
 */
class AgidServiceController extends \open20\agid\service\controllers\base\AgidServiceController
{

    public function beforeAction($action)
    {
        if (\Yii::$app->user->isGuest) {
            $titleSection = Module::t('amosservice', 'grammar_model_plural');
            $urlLinkAll   = '/service/agid-service/index';
        } else {
            $titleSection = Module::t('amosservice', 'grammar_model_plural');
        }

        $labelCreate = 'Nuovo';
        $titleCreate = 'Crea un nuovo servizio';
        $urlCreate   = '/service/agid-service/create';

        $this->view->params = [
            'isGuest' => \Yii::$app->user->isGuest,
            'modelLabel' => 'servizi',
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

    public function actionGlobal($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out                         = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {

            $query = new Query;
            $query->select(new \yii\db\Expression("id, name AS text"))
                ->from('agid_service')
                ->andWhere(['status' => \open20\agid\service\models\AgidService::AGID_SERVICE_STATUS_VALIDATED])
                ->andWhere(['like', 'name', $q])
                ->andWhere(['deleted_at' => null]);

            if (!empty($id)) {
                $query->andWhere(['<>', 'id', $id]);
            }
            $command = $query->createCommand();

            $data = $command->queryAll();

            $out['results'] = array_values($data);
        }
        return $out;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $result = ArrayHelper::merge(
                parent::behaviors(),
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
                                    'global',
                                    'news-ajax',
                                    'documenti-ajax',
                                    'organizzazioni-ajax',
                                ],
                                'roles' => ['BASIC_USER', 'ADMIN']
                            ],
                            [
                                'allow' => true,
                                'actions' => [
                                    'get-agid-service-type-by-content-type',
                                ],
                                'roles' => ['@']
                            ]
                        ],
                    ],
                ]
        );

        return $result;
    }

    public function actionNewsAjax($q = null, $id = null, $myid = null)
    {
        $out = ['more' => false];
        if (!is_null($q)) {
            $query = new Query();
            $query->select('id, titolo AS text')
                ->from(\open20\amos\news\models\News::tableName())
                ->where('titolo LIKE :search', ['search' => "%".$q."%"])
                ->andWhere(['status' => News::NEWS_WORKFLOW_STATUS_VALIDATO])
                ->andWhere(['deleted_at' => null])
                ->limit(50);

            if (!empty($myid)) {
                $query->andWhere(new \yii\db\Expression("id not in ($myid)"));
            }
            $command        = $query->createCommand();
            $data           = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => \open20\amos\news\models\News::findOne($id)->titolo];
        } else {
            $out['results'] = ['id' => 0, 'text' => \Yii::t('amosnews', 'Nessun risultato trovato')];
        }
        return Json::encode($out);
    }

    public function actionDocumentiAjax($q = null, $id = null, $myid = null)
    {
        $out = ['more' => false];
        if (!is_null($q)) {
            $query = new Query();
            $query->select('id, titolo AS text')
                ->from(\open20\amos\documenti\models\Documenti::tableName())
                ->where('titolo LIKE :search', ['search' => "%".$q."%"])
                ->andWhere(['status' => 'DocumentiWorkflow/VALIDATO'])
                ->andWhere(['deleted_at' => null])
                ->limit(50);

            if (!empty($myid)) {
                $query->andWhere(new \yii\db\Expression("id not in ($myid)"));
            }
            $command        = $query->createCommand();
            $data           = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => \open20\amos\documenti\models\Documenti::findOne($id)->titolo];
        } else {
            $out['results'] = ['id' => 0, 'text' => \Yii::t('amosnews', 'Nessun risultato trovato')];
        }
        return Json::encode($out);
    }

    public function actionOrganizzazioniAjax($q = null, $id = null)
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

            $command        = $query->createCommand();
            $data           = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => \open20\agid\organizationalunit\models\AgidOrganizationalUnit::findOne($id)->name];
        } else {
            $out['results'] = ['id' => 0, 'text' => \Yii::t('amosnews', 'Nessun risultato trovato')];
        }
        return Json::encode($out);
    }

    /**
     * action per la restitselect option documenti_agid_type per documenti_agid_content_type
     *
     * @return string
     */
    public function actionGetAgidServiceTypeByContentType(){

        $post_request = \Yii::$app->request->post();


        if(!\Yii::$app->getUser()->can('AGID_SERVICE_ADMIN')){
            $ids = AgidServiceTypeRoles::find()->select('service_agid_type_id')->andWhere(['user_id' =>\Yii::$app->getUser()->id ])->distinct()->column();
            $agid_service_types = AgidServiceType::find()->orderBy([ 'name' => SORT_ASC ])
                ->andWhere(['id' => $ids,
                ])->andWhere([
                    'deleted_at' => null
                ])->all();

        }else{
            $agid_service_types = AgidServiceType::find()->orderBy([ 'name' => SORT_ASC ])
                ->andWhere([
                    'deleted_at' => null
                ])->all();
        }



        $select_option = '<option value="">Seleziona ...</option>';

        foreach ($agid_service_types as $key => $agid_service_type) {

            if( $post_request['service_agid_type_id'] != $agid_service_type->id ){

                $select_option .= "<option value=".$agid_service_type->id . ">" . $agid_service_type->name . "</option>";

            }else{

                $select_option .= "<option value=".$agid_service_type->id . " selected>" . $agid_service_type->name . "</option>";
            }
        }

        return json_encode($select_option);
    }
}