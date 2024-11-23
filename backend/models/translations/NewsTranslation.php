<?php

namespace backend\models\translations;

use Yii;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;

/**
* This is the base-model class for table "news__translation".
*
    * @property integer $news_id
    * @property string $language
    * @property string $slug
    * @property string $titolo
    * @property string $sottotitolo
    * @property string $descrizione_breve
    * @property string $descrizione
    * @property string $metakey
    * @property string $metadesc
    * @property string $status
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    * @property string $body_news
    *
            * @property \backend\models\translations\NewsTranslation $news
    */
class NewsTranslation extends \open20\amos\core\record\Record
{


    public $language_source;


/**
* @inheritdoc
*/
public static function tableName()
{
return 'news__translation';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['news_id', 'language'], 'required'],
            [['news_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['slug', 'titolo', 'sottotitolo', 'descrizione_breve', 'descrizione', 'metakey', 'metadesc', 'body_news'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\news\models\News::className(), 'targetAttribute' => ['news_id' => 'id']],
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
    'news_id' => Yii::t('amostranslation', 'News ID'),
    'language' => Yii::t('amostranslation', 'Language'),
    'slug' => Yii::t('amostranslation', 'Slug'),
    'titolo' => Yii::t('amostranslation', 'Titolo'),
    'sottotitolo' => Yii::t('amostranslation', 'Sottotitolo'),
    'descrizione_breve' => Yii::t('amostranslation', 'Descrizione Breve'),
    'descrizione' => Yii::t('amostranslation', 'Descrizione'),
    'metakey' => Yii::t('amostranslation', 'Metakey'),
    'metadesc' => Yii::t('amostranslation', 'Metadesc'),
    'status' => Yii::t('amostranslation', 'Status'),
    'created_by' => Yii::t('amostranslation', 'Created By'),
    'updated_by' => Yii::t('amostranslation', 'Updated By'),
    'deleted_by' => Yii::t('amostranslation', 'Deleted By'),
    'created_at' => Yii::t('amostranslation', 'Created At'),
    'updated_at' => Yii::t('amostranslation', 'Updated At'),
    'deleted_at' => Yii::t('amostranslation', 'Deleted At'),
    'body_news' => Yii::t('amostranslation', 'Body News'),
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getNews()
    {
    return $this->hasOne(\open20\amos\news\models\News::className(), ['id' => 'news_id']);
    }
}
