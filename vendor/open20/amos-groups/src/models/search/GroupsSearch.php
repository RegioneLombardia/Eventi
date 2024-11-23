<?php

namespace  open20\amos\groups\models\search;

use open20\amos\admin\models\base\UserProfile;
use open20\amos\admin\models\search\UserProfileSearch;
use open20\amos\core\user\User;
use open20\amos\cwh\AmosCwh;
use open20\amos\groups\models\Groups;
use open20\amos\groups\Module;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
* GroupsSearch represents the model behind the search form about `backend\modules\pateradmin\models\Groups`.
*/
class GroupsSearch extends \open20\amos\groups\models\Groups
{
    public function rules()
    {
        return [
            [['id', 'parent_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['name', 'description', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function getScope($params)
    {
        $scope = $this->formName();
        if( !isset( $params[$scope]) ){
            $scope = '';
        }
        return $scope;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $groupsModel = \Yii::$app->getModule(Module::getModuleName());
        $idParent = \Yii::$app->request->get('idParent');
        $query = Groups::find();

        /** @var AmosCwh $cwh */
        $cwh = Yii::$app->getModule("cwh");
        $community = Yii::$app->getModule("community");
        // if we are navigating users inside a sprecific entity (eg. a community)
        // see users filtered by entity-user association table
        if (isset($cwh) && isset($community)) {
            $cwh->setCwhScopeFromSession();
            if (!empty($cwh->userEntityRelationTable)) {
                $entityId = $cwh->userEntityRelationTable['entity_id'];
                $query->andWhere(['groups.parent_id'=> $entityId]);
            }
        }
        else {
            if(!empty($groupsModel->classNameParent)
                && !empty($groupsModel->classNameParentUserMm)
                && !empty($groupsModel->parentAttributeMm)
                && !empty($groupsModel->userAttributeMm)
                && !empty($idParent)) {
                $classNameParent = $groupsModel->classNameParent;
                $tableParent= $classNameParent::tableName();
                $query->innerJoin($tableParent, $tableParent. '.id = groups.parent_id' )->andWhere(['groups.parent_id'=> $idParent]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $scope = $this->getScope($params);

        if (!($this->load($params, $scope) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }


    public function searchProva(){
        $query  = UserProfile::find()->andWhere(['LIKE', 'nome', 'laura']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }



    /**
     * @return ActiveDataProvider
     */
    public function searchUsers(){
        $groupsModule = \Yii::$app->getModule(Module::getModuleName());
        $idParent = \Yii::$app->request->get('idParent');
        if(!empty($groupsModule->className) && !empty($groupsModule->method)) {
            $className = $groupsModule->className;
            $model = new $className();
            $method = $groupsModule->method;
            $dataProvider = $model->$method();
        }
        else {
            $query = UserProfile::find();
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }

        //if you want to filter user using an userMm
        if(!empty($groupsModule->classNameParent)
            && !empty($groupsModule->classNameParentUserMm)
            && !empty($groupsModule->parentAttributeMm)
            && !empty($groupsModule->userAttributeMm)
            && !empty($idParent)) {
            $classNameParentUserMm = $groupsModule->classNameParentUserMm;
            $tableParentUserMm = $classNameParentUserMm::tableName();
            $dataProvider->query->innerJoin($tableParentUserMm, $tableParentUserMm. '.'. $groupsModule->userAttributeMm . ' = user_profile.user_id' )
                ->andWhere([$tableParentUserMm .'.'. $groupsModule->parentAttributeMm => $idParent]);
        }

        // use the user profile model for form search
        $dataProvider->pagination = false;
        $userProfilesId = $dataProvider->getKeys();
        $modelSearch = new UserProfileSearch();
        $dataProvider = null;
        $dataProvider = $modelSearch->search(\Yii::$app->request->get());

        // filter user for codice_fiscale
        $codice_fiscale = null;
        if(!empty(\Yii::$app->request->get('UserProfileSearch')['codice_fiscale'])) {
            $codice_fiscale = \Yii::$app->request->get('UserProfileSearch')['codice_fiscale'];
        }
        $dataProvider->query->andWhere(['user_profile.id' => $userProfilesId])
            ->andFilterWhere(['LIKE','user_profile.codice_fiscale', $codice_fiscale]);

        return $dataProvider;
    }


    /**
     * @param $model Groups
     * @return ActiveDataProvider
     */
    public function searchUsersSelected($model){
        if($model->isNewRecord){
            $dataProviderSelected = new ActiveDataProvider([
                'query' => UserProfile::find()->andWhere(0)
            ]);
        }
        else {
            $dataProviderSelected = new ActiveDataProvider([
                'query' => $model->getUserProfiles()
            ]);
        }
        return $dataProviderSelected;
    }

}