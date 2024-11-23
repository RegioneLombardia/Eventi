<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\models\search
 * @category   CategoryName
 */

namespace backend\modules\eventsadmin\models\search;

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\base\ConfigurationManager;
use open20\amos\admin\models\UserProfile;
use open20\amos\core\user\User;
use open20\amos\core\interfaces\SearchModelInterface;
use open20\amos\core\record\SearchResult;
use open20\amos\events\models\Event;
use open20\amos\events\utility\EventsUtility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;
use open20\amos\events\models\EventGroupReferentMm;

/**
 * Class UserProfileSearch
 *
 * UserProfileSearch represents the model behind the search form about `common\models\UserProfile`.
 *
 * @property string $email
 *
 * @package open20\amos\admin\models\search
 */
class UserProfileSearch extends \open20\amos\admin\models\search\UserProfileSearch
{
    /**
     * @var string $username
     */
    public $username = '';
    /**
     * @var
     */
    public $dg_id;
    public $flagOperatore;
    public $codiceFiscale;
    public $ruolo;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'privacy'], 'integer'],
            [[
                'nome',
                'cognome',
                'username',
                'email',
                'codiceFiscale',
                'sesso',
                'prevalent_partnership_id',
                'facilitatore_id',
                'status',
                'isFacilitator',
                'isOperatingReferent',
                'userProfileStatus',
                'validato_almeno_una_volta',
                'dg_id',
                'flagOperatore',
                'ruolo'
            ], 'safe'],
        ];
    }


    /**
     * @param ActiveQuery $query
     * @return mixed
     */
    public function baseFilter($query)
    {
        $query->andFilterWhere([
            UserProfile::tableName() . '.status' => $this->userProfileStatus,
            UserProfile::tableName() . '.validato_almeno_una_volta' => $this->validato_almeno_una_volta,
        ]);

        $query->andFilterWhere(['like', UserProfile::tableName() . '.nome', $this->nome])
            ->andFilterWhere(['like', UserProfile::tableName() . '.cognome', $this->cognome])
            ->andFilterWhere(['like', User::tableName() . '.username', $this->username])
            ->andFilterWhere(['like', 'codice_fiscale', $this->codiceFiscale])
            ->andFilterWhere(['like', User::tableName() . '.email', $this->email]);

        $group = EventsUtility::getCurrentDg();
        $canAdmin = Yii::$app->user->can('ADMIN');
        if (!$canAdmin && \Yii::$app->authManager->checkAccess(\Yii::$app->user->id, 'EVENT_DG_OPERATOR')) {
            $this->dg_id = $group->id;
        }


        if (!empty($this->dg_id) || isset($this->flagOperatore)) {
            $query->leftJoin('event_group_referent_mm', 'event_group_referent_mm.user_id = user.id');
            if (!empty($this->dg_id)) {
                $query->andFilterWhere(['event_group_referent_mm.event_group_referent_id' => $this->dg_id]);
            }
            $this->filterOperators($query);
        }

        $this->filterRoles($query);

        if (
            $this->adminModule->confManager->isVisibleBox('box_prevalent_partnership',
                ConfigurationManager::VIEW_TYPE_FORM) &&
            $this->adminModule->confManager->isVisibleField('prevalent_partnership_id',
                ConfigurationManager::VIEW_TYPE_FORM)
        ) {
            $this->userProfileSelectFieldsQuery($query, 'prevalent_partnership_id');
        }
        $this->userProfileSelectFieldsQuery($query, 'facilitatore_id');

        // If value is "-1" it mean the user is searching whether the sex value is not selected.
        if ($this->sesso == -1) {
            $query->andWhere(['or', [UserProfile::tableName() . '.sesso' => null], [UserProfile::tableName() . '.sesso' => '']]);
        } else {
            $query->andFilterWhere([
                UserProfile::tableName() . '.sesso' => $this->sesso
            ]);
        }

        $this->userProfileRolesQuery($query, 'isFacilitator', 'FACILITATOR');
        $organizationModuleName = $this->adminModule->getOrganizationModuleName();
        if (($organizationModuleName == 'organizations') && !is_null(Yii::$app->getModule($organizationModuleName))) {
            $this->userProfileRolesQuery($query, 'isOperatingReferent', 'OPERATING_REFERENT');
        }

        return $query;
    }

    /**
     * Roles search filter
     * @param $query
     * @return void
     */
    public function filterRoles($query)
    {
        if (!empty($this->ruolo)) {
            $permissions = [];
            $notPermission = [];
//            pr($this->ruolo);
            switch($this->ruolo){
                case EventGroupReferentMm::ROLE_SUPER_USER:
                    $permissions = ['SUPER_USER', 'ADMIN'];
                    $notPermission = ['ASSISTENZA_EVENTI'];
                    break;

                case EventGroupReferentMm::ROLE_ASSISTENZA:
                    $permissions = ['ASSISTENZA_EVENTI'];
                    break;

                case EventGroupReferentMm::ROLE_OPERATORE:
                    $permissions = ['EVENT_DG_OPERATOR','SUPER_USER_EVENT'];
                    $notPermission = ['SUPER_USER','ASSISTENZA_EVENTI', 'EVENT_DG'];
                    break;

                case EventGroupReferentMm::ROLE_DG:
                    $permissions = ['EVENT_DG'];
                    $notPermission = ['SUPER_USER'];
                    break;

                case EventGroupReferentMm::ROLE_CANDIDATO_OPERATORE:
                    $permissions = ['CANDIDATO_OPERATORE'];
                    $notPermission = ['EVENT_DG_OPERATOR', 'SUPER_USER_EVENT'];
                    break;
            }


            $auth = \Yii::$app->authManager;
            $in = [];
            $notIn = [];
            foreach ($permissions as $permission){
                $inTemp =  $auth->getUserIdsByRole($permission);
                $in = ArrayHelper::merge($in, $inTemp);
            }

            foreach ($notPermission as $Notpermission){
                $notInTemp =  $auth->getUserIdsByRole($Notpermission);
                $notIn = ArrayHelper::merge($notIn, $notInTemp);
            }

            $query->andFilterWhere(['in', 'user.id', $in])
                ->andFilterWhere(['not in', 'user.id', $notIn]);

        }
    }

    public function filterOperators($query)
    {
        if (isset($this->flagOperatore) && !is_null($this->flagOperatore)) {
            if ($this->flagOperatore == 2) {
                $operatorRoles = ['ADMIN', 'SUPER_USER', 'ASSISTENZA_EVENTI', 'EVENT_DG', 'CANDIDATO_OPERATORE', 'EVENT_DG_OPERATOR', 'SUPER_USER_EVENT'];
                $auth = \Yii::$app->authManager;
                $notIn = [];

                foreach ($operatorRoles as $Notpermission){
                    $notInTemp =  $auth->getUserIdsByRole($Notpermission);
                    $notIn = ArrayHelper::merge($notIn, $notInTemp);
                }

                $query->andFilterWhere(['not in', 'user.id', $notIn]);
            }else {
                $query->andFilterWhere(['event_group_referent_mm.exclude_from_query' => $this->flagOperatore]);
            }
        }
    }

}