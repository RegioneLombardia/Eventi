<?php

namespace open20\amos\events\models\search;

use open20\amos\admin\models\UserProfile;
use open20\amos\admin\utility\UserProfileUtility;
use open20\amos\core\user\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use open20\amos\events\models\EventGroupReferentMm;
use yii\helpers\ArrayHelper;

/**
 * EventGroupReferentMmSearch represents the model behind the search form about `open20\amos\events\models\EventGroupReferentMm`.
 */
class EventGroupReferentMmSearch extends EventGroupReferentMm
{

//private $container;

    public function __construct(array $config = [])
    {
        $this->isSearch = true;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['exclude_from_query', 'id', 'user_id', 'event_group_referent_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at', 'firstName', 'lastName', 'codiceFiscale', 'ruolo', 'dgAppartenenza', 'userStatus', 'email'], 'safe'],
            ['EventGroupReferent', 'safe'],
            [['nomeCognome', 'denominazione'], 'string'],
        ];
    }

    public function scenarios()
    {
// bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query        = EventGroupReferentMm::find();
       
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith('eventGroupReferent');
        $query->joinWith('user');
        $query->joinWith('userProfile');

        $dataProvider->setSort([
            'attributes' => [
                'user_id' => [
                    'asc' => ['event_group_referent_mm.user_id' => SORT_ASC],
                    'desc' => ['event_group_referent_mm.user_id' => SORT_DESC],
                ],
                'event_group_referent_id' => [
                    'asc' => ['event_group_referent_mm.event_group_referent_id' => SORT_ASC],
                    'desc' => ['event_group_referent_mm.event_group_referent_id' => SORT_DESC],
                ],
                'eventGroupReferent' => [
                    'asc' => ['event_group_referent.denominazione' => SORT_ASC],
                    'desc' => ['event_group_referent.denominazione' => SORT_DESC],
                ], 'user' => [
                    'asc' => ['user.email' => SORT_ASC],
                    'desc' => ['user.email' => SORT_DESC],
                ],]]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }



        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'event_group_referent_id' => $this->event_group_referent_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
        ]);

//        $query->andFilterWhere( ['exclude_from_query' => $this->exclude_from_query]);
        $query->andFilterWhere(['like', new \yii\db\Expression('event_group_referent.denominazione'), $this->denominazione]);
//        $query->andFilterWhere(['like', new \yii\db\Expression('user.email'), $this->email]);

        $query->andFilterWhere(['like', 'user_profile.nome', $this->firstName]);
        $query->andFilterWhere(['like', 'user_profile.cognome', $this->lastName]);
        $query->andFilterWhere(['like', 'user.email', $this->email]);
        $query->andFilterWhere(['like', 'user_profile.codice_fiscale', $this->codiceFiscale]);

        $this->filterRoles($query);

        $this->filterUserStatus($query);

        return $dataProvider;
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
                case self::ROLE_SUPER_USER:
                    $permissions = ['SUPER_USER', 'ADMIN'];
                    $notPermission = ['ASSISTENZA_EVENTI'];
                    break;

                case self::ROLE_ASSISTENZA:
                    $permissions = ['ASSISTENZA_EVENTI'];
                    break;

                case self::ROLE_OPERATORE:
                    $permissions = ['EVENT_DG_OPERATOR','SUPER_USER_EVENT'];
                    $notPermission = ['SUPER_USER','ASSISTENZA_EVENTI', 'EVENT_DG'];
                    break;

                case self::ROLE_DG:
                    $permissions = ['EVENT_DG'];
                    $notPermission = ['SUPER_USER'];
                    break;

                case self::ROLE_CANDIDATO_OPERATORE:
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

    /**
     * User status filter
     * @param $query
     * @return void
     */
    public function filterUserStatus($query)
    {
        if (!empty($this->userStatus)) {
            if ($this->userStatus == 1) {
                $query->andFilterWhere(['user_profile.attivo' => 1])
                    ->andFilterWhere(['NOT LIKE', 'user_profile.nome', UserProfileUtility::DELETED_ACCOUNT_NAME]);
            } else if ($this->userStatus == 2) {
                $query->andFilterWhere(['user_profile.attivo' => 0])
                    ->andFilterWhere(['NOT LIKE', 'user_profile.nome', UserProfileUtility::DELETED_ACCOUNT_NAME]);
            } else if ($this->userStatus == 3) {
                $query->andFilterWhere(['LIKE', 'user_profile.nome', UserProfileUtility::DELETED_ACCOUNT_NAME]);
            }
        }
    }
}