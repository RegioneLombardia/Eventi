<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 04/11/2021
 * Time: 16:44
 */

namespace open20\amos\events\models\search;


use open20\amos\admin\models\UserProfile;
use open20\amos\admin\utility\UserProfileUtility;
use open20\amos\chat\DataProvider;
use open20\amos\events\utility\EventsUtility;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;


class SpecificUserEventSearch extends Model
{
    const TYPE_BASE = 1;
    const TYPE_COMUNICATION = 2;

    public $name;
    public $char;

    public $invitationName;
    public $event;
    public $type;
    public $showAllUsers = true;

    const SCENARIO_NOMINAL_INVITATION = 'scenario_nominal_invitation';


    public function init()
    {
        parent::init();
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = ArrayHelper::merge(
            parent::scenarios(), [
                self::SCENARIO_NOMINAL_INVITATION => [
                    'name', 'char', 'invitationName'
                ],
            ]
        );
        return $scenarios;

    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['invitationName', 'required', 'on' => self::SCENARIO_NOMINAL_INVITATION],
            [['name', 'char', 'invitationName'], 'safe']
        ];
    }


    public function search($params)
    {
        $query = UserProfile::find()
            ->innerJoinWith('user')
            ->andWhere(['attivo' => 1])
            ->andWhere(['NOT LIKE', 'user_profile.nome', UserProfileUtility::DELETED_ACCOUNT_NAME]);

            if(!$this->showAllUsers){
                $currentDgId = EventsUtility::getCurrentDgId();
//                $query->innerJoin('event_group_referent_mm','event_group_referent_mm.user_id = user.id AND event_group_referent_mm.exclude_from_query = 0')
                $query->innerJoin('event_group_referent_mm','event_group_referent_mm.user_id = user.id')
                    ->andWhere(['event_group_referent_mm.event_group_referent_id' => $currentDgId]);
            }


        $query->groupBy('user_profile.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $dataProvider->setSort([
            'attributes' => ['nome', 'cognome'],
            'defaultOrder' => [
                'cognome' => SORT_ASC,
                'nome' => SORT_ASC
            ]
        ]);

        $this->load($params);

        if($this->type == self::TYPE_COMUNICATION && !empty($this->event)){
            $this->communicationQuery($query, $this->event);
        }

        $query->andFilterWhere(['LIKE', 'cognome', $this->char . '%', false]);
        $query->andFilterWhere(
            ['or',
                ['like', 'cognome', $this->name],
                ['like', 'nome', $this->name],
                ['like', 'codice_fiscale', $this->name],
                ['like', 'email', $this->name],
                ['like', "CONCAT( nome , ' ', cognome )", $this->name],
                ['like', "CONCAT( cognome , ' ', nome )", $this->name],
            ]
        );

        return $dataProvider;
    }


    /**
     * @param $query
     * @param $event
     * @return mixed
     */
    public function communicationQuery($query, $event){
        $query->innerJoin('community_user_mm', 'community_user_mm.user_id = user_profile.user_id')
            ->innerJoin('event_invitation', 'event_invitation.user_id = community_user_mm.user_id')
            ->andWhere(['community_user_mm.community_id' => $event->community_id])
            ->andWhere(['is', 'community_user_mm.deleted_at', null]);

        return $query;
    }

}