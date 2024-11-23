<?php

namespace open20\amos\groups\models;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "groups".
 */
class Groups extends \open20\amos\groups\models\base\Groups
{
    public $attrGroupsMembers;

    public function representingColumn()
    {
        return [
            //inserire il campo o i campi rappresentativi del modulo
        ];
    }

    public function attributeHints()
    {
        return [
        ];
    }

    /**
     * Returns the text hint for the specified attribute.
     * @param string $attribute the attribute name
     * @return string the attribute hint
     */
    public function getAttributeHint($attribute)
    {
        $hints = $this->attributeHints();
        return isset($hints[$attribute]) ? $hints[$attribute] : null;
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['attrGroupsMembers', 'safe']
        ]);
    }

    public function attributeLabels()
    {
        return
            ArrayHelper::merge(
                parent::attributeLabels(),
                [
                ]);
    }

    /**
     * Return Groups associeated to a parent  or if $idParent is null getAllGroup
     *
     * @param null $idParent
     * @return ActiveQuery
     */
    public static function getGroupsByParent($idParent = null){
        $query = Groups::find();
        $cwh = \Yii::$app->getModule('cwh');
        $community = \Yii::$app->getModule('community');

        // if you are inside a community filter for community groups
        if (isset($cwh) && isset($community)) {
            $cwh->setCwhScopeFromSession();
            if (!empty($cwh->userEntityRelationTable)) {
                $entityId = $cwh->userEntityRelationTable['entity_id'];
                $query->andWhere(['groups.parent_id'=> $entityId]);
            }
        } elseif(!empty($idParent)) {
            $query->andWhere(['groups.parent_id'=> $idParent]);
        }

        return $query;
    }

    /**
     * Return the number of user members inside the group
     * @return int
     */
    public function getNumberGroupMembers(){
        return $this->getGroupsMembers()->count();
    }

}
