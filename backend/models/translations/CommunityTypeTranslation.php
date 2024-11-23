<?php

namespace backend\models\translations;

use Yii;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;

/**
* This is the base-model class for table "community_types__translation".
*
    * @property integer $community_types_id
    * @property string $language
    * @property string $name
    * @property string $description
    * @property string $status
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
*/
class CommunityTypeTranslation extends \open20\amos\core\record\Record
{


    public $language_source;


/**
* @inheritdoc
*/
public static function tableName()
{
return 'community_types__translation';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['community_types_id', 'language'], 'required'],
            [['community_types_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['name', 'description'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['community_types_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\community\models\CommunityType::className(), 'targetAttribute' => ['community_types_id' => 'id']],
 ['language_source', 'safe'],
];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
'language_source' => \Yii::t('amostranslation', 'Select another source language'),
    'community_types_id' => Yii::t('amostranslation', 'Community Types ID'),
    'language' => Yii::t('amostranslation', 'Language'),
    'name' => Yii::t('amostranslation', 'Name'),
    'description' => Yii::t('amostranslation', 'Description'),
    'status' => Yii::t('amostranslation', 'Status'),
    'created_by' => Yii::t('amostranslation', 'Created By'),
    'updated_by' => Yii::t('amostranslation', 'Updated By'),
    'deleted_by' => Yii::t('amostranslation', 'Deleted By'),
    'created_at' => Yii::t('amostranslation', 'Created At'),
    'updated_at' => Yii::t('amostranslation', 'Updated At'),
    'deleted_at' => Yii::t('amostranslation', 'Deleted At'),
];
}
}
