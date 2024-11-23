<?php

namespace backend\models\translations;

use Yii;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;

/**
* This is the base-model class for table "discussioni_topic__translation".
*
    * @property integer $discussioni_topic_id
    * @property string $language
    * @property string $slug
    * @property string $titolo
    * @property string $testo
    * @property string $lat
    * @property string $lng
    * @property string $primo_piano
    * @property string $status
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    *
            * @property \backend\models\translations\DiscussioniTopicTranslation $discussioniTopic
    */
class DiscussioniTopicTranslation extends \open20\amos\core\record\Record
{


    public $language_source;


/**
* @inheritdoc
*/
public static function tableName()
{
return 'discussioni_topic__translation';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['discussioni_topic_id', 'language'], 'required'],
            [['discussioni_topic_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['slug', 'titolo', 'testo', 'lat', 'lng', 'primo_piano'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['discussioni_topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\discussioni\models\DiscussioniTopic::className(), 'targetAttribute' => ['discussioni_topic_id' => 'id']],
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
    'discussioni_topic_id' => Yii::t('amostranslation', 'Discussioni Topic ID'),
    'language' => Yii::t('amostranslation', 'Language'),
    'slug' => Yii::t('amostranslation', 'Slug'),
    'titolo' => Yii::t('amostranslation', 'Titolo'),
    'testo' => Yii::t('amostranslation', 'Testo'),
    'lat' => Yii::t('amostranslation', 'Lat'),
    'lng' => Yii::t('amostranslation', 'Lng'),
    'primo_piano' => Yii::t('amostranslation', 'Primo Piano'),
    'status' => Yii::t('amostranslation', 'Status'),
    'created_by' => Yii::t('amostranslation', 'Created By'),
    'updated_by' => Yii::t('amostranslation', 'Updated By'),
    'deleted_by' => Yii::t('amostranslation', 'Deleted By'),
    'created_at' => Yii::t('amostranslation', 'Created At'),
    'updated_at' => Yii::t('amostranslation', 'Updated At'),
    'deleted_at' => Yii::t('amostranslation', 'Deleted At'),
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getDiscussioniTopic()
    {
    return $this->hasOne(\open20\amos\discussioni\models\DiscussioniTopic::className(), ['id' => 'discussioni_topic_id']);
    }
}
