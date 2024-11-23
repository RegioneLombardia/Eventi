<?php

namespace open20\amos\groups\models\base;

use open20\amos\admin\models\UserProfile;
use open20\amos\core\user\User;
use Yii;

/**
 * This is the base-model class for table "groups".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\amos\groups\models\GroupsMembers[] $groupsMembers
 */
class Groups extends \open20\amos\core\record\Record
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'groups';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            ['name', 'required'],
            [['name', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amosgroups', 'Id'),
            'parent_id' => Yii::t('amosgroups', 'Parent'),
            'name' => Yii::t('amosgroups', 'Name'),
            'description' => Yii::t('amosgroups', 'Description'),
            'created_at' => Yii::t('amosgroups', 'Created at'),
            'updated_at' => Yii::t('amosgroups', 'Updated at'),
            'deleted_at' => Yii::t('amosgroups', 'Deleted at'),
            'created_by' => Yii::t('amosgroups', 'Created at'),
            'updated_by' => Yii::t('amosgroups', 'Updated by'),
            'deleted_by' => Yii::t('amosgroups', 'Deleted by'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupsMembers()
    {
        return $this->hasMany(\open20\amos\groups\models\GroupsMembers::className(), ['groups_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->via('groupsMembers');
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfiles()
    {
        return $this->hasMany(UserProfile::className(), ['user_id' => 'id'])->via('users');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupsComunications()
    {
        return $this->hasMany(\open20\amos\groups\models\GroupsComunication::className(), ['groups_id' => 'id']);
    }

}
