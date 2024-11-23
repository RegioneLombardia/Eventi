<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 27/04/2022
 * Time: 16:46
 */

namespace open20\amos\events\widgets;


use open20\amos\admin\models\UserProfile;
use open20\amos\core\user\User;
use open20\amos\events\models\search\SpecificUserEventSearch;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

class EventNominalInvitationWidget extends Widget
{

    public $model;
    public $internalList;
    public $communication;
    public $showAllUsers = true;


    /**
     * @return string
     */
    public function run()
    {
        $modelSearch = new SpecificUserEventSearch();
        $modelSearch->setScenario(SpecificUserEventSearch::SCENARIO_NOMINAL_INVITATION);
        $modelSearch->showAllUsers = $this->showAllUsers;

        if ($this->communication) {
            $modelSearch->event = $this->model;
            $modelSearch->type = SpecificUserEventSearch::TYPE_COMUNICATION;
        }
        $dataProvider = $modelSearch->search(\Yii::$app->request->get());
//        $dataProviderInvitationUsers = new ActiveDataProvider([
//            'query' => $this->model->getEventSpecificUsers()
//        ]);
//
//        $arrayUsers = $this->model->getEventSpecificUsers()->asArray()->select('user_id')->all();
//        $participants_user_ids = [];
//        foreach ($arrayUsers as $user_id) {
//            $participants_user_ids  [] = $user_id['user_id'];
//        }
//        $countInvitationUsers = $this->model->getEventSpecificUsers()->count();

        $participants_user_ids = [];
        $countInvitationUsers = 0;
        $dataProviderInvitationUsers = new ActiveDataProvider([
            'query' => UserProfile::find()->andWhere(0)
        ]);

        if (!empty($this->internalList)) {
            $modelSearch->invitationName = $this->internalList->name;
            $activeQuery = unserialize(urldecode($this->internalList->active_query));
            $activeQuery2 = clone $activeQuery;
            $dataProviderInvitationUsers = new ActiveDataProvider([
                'query' => $activeQuery
            ]);

            $arrayUsers = $activeQuery2->asArray()->select('user_id')->all();
            foreach ($arrayUsers as $user_id) {
                $participants_user_ids  [] = $user_id['user_id'];
            }
            $countInvitationUsers = count($arrayUsers);
        }


        return $this->render('event_nominal_invitation/nominal_invitation', [
            'model' => $this->model,
            'internalList' => $this->internalList,
            'communication' => $this->communication,
            'modelSearch' => $modelSearch,
            'dataProvider' => $dataProvider,
            'dataProviderInvitationUsers' => $dataProviderInvitationUsers,
            'countInvitationUsers' => $countInvitationUsers,
            'participants_user_ids' => $participants_user_ids,
        ]);
    }

}