<?php

namespace open20\amos\groups\models\base;

use open20\amos\core\user\User;
use Yii;

/**
 * This is the base-model class for table "groups_members".
 *
 * @property integer $id
 * @property integer $groups_id
 * @property integer $user_id
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property  \open20\amos\groups\models\Groups $groups
 * @property  \open20\amos\core\user\User $user
 */
class GroupsMembers extends \open20\amos\core\record\Record
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'groups_members';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['groups_id', 'user_id'], 'required'],
            [['groups_id', 'user_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['description'], 'string', 'max' => 255],
            [['groups_id'], 'exist', 'skipOnError' => true, 'targetClass' => Groups::className(), 'targetAttribute' => ['groups_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'groups_id' => Yii::t('app', 'Group'),
            'user_id' => Yii::t('app', 'User'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted_at' => Yii::t('app', 'Deleted at'),
            'created_by' => Yii::t('app', 'Created at'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'deleted_by' => Yii::t('app', 'Deleted by'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasOne(\open20\amos\groups\models\Groups::className(), ['id' => 'groups_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\open20\amos\core\user\User::className(), ['id' => 'user_id']);
    }
}
