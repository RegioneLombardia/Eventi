<?php

namespace backend\models\translations;

use Yii;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;

/**
* This is the base-model class for table "documenti_categorie__translation".
*
    * @property integer $documenti_categorie_id
    * @property string $language
    * @property string $titolo
    * @property string $sottotitolo
    * @property string $descrizione_breve
    * @property string $descrizione
    * @property string $status
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    *
            * @property \backend\models\translations\DocumentiCategorieTranslation $documentiCategorie
    */
class DocumentiCategorieTranslation extends \open20\amos\core\record\Record
{


    public $language_source;


/**
* @inheritdoc
*/
public static function tableName()
{
return 'documenti_categorie__translation';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['documenti_categorie_id', 'language'], 'required'],
            [['documenti_categorie_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['titolo', 'sottotitolo', 'descrizione_breve', 'descrizione'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['documenti_categorie_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\documenti\models\DocumentiCategorie::className(), 'targetAttribute' => ['documenti_categorie_id' => 'id']],
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
    'documenti_categorie_id' => Yii::t('amostranslation', 'Documenti Categorie ID'),
    'language' => Yii::t('amostranslation', 'Language'),
    'titolo' => Yii::t('amostranslation', 'Titolo'),
    'sottotitolo' => Yii::t('amostranslation', 'Sottotitolo'),
    'descrizione_breve' => Yii::t('amostranslation', 'Descrizione Breve'),
    'descrizione' => Yii::t('amostranslation', 'Descrizione'),
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
    public function getDocumentiCategorie()
    {
    return $this->hasOne(\open20\amos\documenti\models\DocumentiCategorie::className(), ['id' => 'documenti_categorie_id']);
    }
}
