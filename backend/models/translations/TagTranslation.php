<?php

namespace backend\models\translations;

use Yii;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;

/**
* This is the base-model class for table "tag__translation".
*
    * @property integer $tag_id
    * @property string $language
    * @property string $nome
    * @property string $nome_en
    * @property string $codice
    * @property string $descrizione
    * @property string $descrizione_en
    * @property string $icon
    * @property string $status
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    *
            * @property \backend\models\translations\TagTranslation $tag
    */
class TagTranslation extends \open20\amos\core\record\Record
{


    public $language_source;


/**
* @inheritdoc
*/
public static function tableName()
{
return 'tag__translation';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['tag_id', 'language'], 'required'],
            [['tag_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['nome', 'nome_en', 'codice', 'descrizione', 'descrizione_en', 'icon'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\tag\models\Tag::className(), 'targetAttribute' => ['tag_id' => 'id']],
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
    'tag_id' => Yii::t('amostranslation', 'Tag ID'),
    'language' => Yii::t('amostranslation', 'Language'),
    'nome' => Yii::t('amostranslation', 'Nome'),
    'nome_en' => Yii::t('amostranslation', 'Nome En'),
    'codice' => Yii::t('amostranslation', 'Codice'),
    'descrizione' => Yii::t('amostranslation', 'Descrizione'),
    'descrizione_en' => Yii::t('amostranslation', 'Descrizione En'),
    'icon' => Yii::t('amostranslation', 'Icon'),
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
    public function getTag()
    {
    return $this->hasOne(\open20\amos\tag\models\Tag::className(), ['id' => 'tag_id']);
    }
}
