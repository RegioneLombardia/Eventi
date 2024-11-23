<?php

/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 04/11/2021
 * Time: 16:44
 */

namespace open20\amos\events\models\search;

use open20\amos\admin\models\UserProfile;
use open20\amos\core\user\User;
use open20\amos\admin\utility\UserProfileUtility;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class EventWebexProfileSearch extends Model {

    public $name;
    public $char;

    public function init() {
        parent::init();
    }

    public function rules() {
        return [
            [['name', 'char'], 'safe']
        ];
    }

    public function search($params) {

        $UtentiWebexCreator = User::find()->select('id')
                ->innerJoin('auth_assignment', 'user.id = auth_assignment.user_id')
                ->andWhere(['LIKE ', 'item_name', 'EVENT_WEBEX_CREATOR'])
                ->asArray()
                ->all();

        $query = UserProfile::find()
                ->innerJoinWith('user')
                ->andWhere(['NOT IN', 'user_profile.user_id', ($UtentiWebexCreator[0]) ? $UtentiWebexCreator[0] : []])
                ->andWhere(['attivo' => 1])
                ->andWhere(['NOT LIKE', 'user_profile.nome', UserProfileUtility::DELETED_ACCOUNT_NAME]);

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

}
