<?php

namespace open20\amos\groups\models\base;

use Yii;

/**
 * This is the base-model class for table "groups_comunication".
 *
 * @property integer $id
 * @property integer $groups_id
 * @property string $subject
 * @property string $text
 * @property string $layout_path
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\amos\groups\models\Groups $groups
 */
class  GroupsComunication extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'groups_comunication';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['groups_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['subject', 'text'], 'string'],
            [['sent_at', 'status', 'email','created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['layout_path'], 'string', 'max' => 255],
            [['groups_id'], 'exist', 'skipOnError' => true, 'targetClass' => Groups::className(), 'targetAttribute' => ['groups_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amosgroups', 'ID'),
            'groups_id' => Yii::t('amosgroups', 'Group'),
            'subject' => Yii::t('amosgroups', 'Subject'),
            'text' => Yii::t('amosgroups', 'Text'),
            'sent_at' => Yii::t('amosgroups', 'Inviato il'),
            'status' => Yii::t('amosgroups', 'Stato'),
            'layout_path' => Yii::t('amosgroups', 'Layout'),
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
    public function getGroups()
    {
        return $this->hasOne(\open20\amos\groups\models\Groups::className(), ['id' => 'groups_id']);
    }
}
