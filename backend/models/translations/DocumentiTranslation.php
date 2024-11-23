<?php

namespace backend\models\translations;

use Yii;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;

/**
* This is the base-model class for table "documenti__translation".
*
    * @property integer $documenti_id
    * @property string $language
    * @property string $titolo
    * @property string $sottotitolo
    * @property string $descrizione_breve
    * @property string $descrizione
    * @property string $metakey
    * @property string $metadesc
    * @property string $link_document
    * @property string $drive_file_id
    * @property string $status
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    * @property string $object
    * @property string $extended_description
    * @property string $distribution_license
    * @property string $dates_and_intermediate_stages
    * @property string $further_information
    * @property string $regulatory_requirements
    * @property string $protocol
    * @property string $help_box
    * @property string $author
    *
            * @property \backend\models\translations\DocumentiTranslation $documenti
    */
class DocumentiTranslation extends \open20\amos\core\record\Record
{


    public $language_source;


/**
* @inheritdoc
*/
public static function tableName()
{
return 'documenti__translation';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['documenti_id', 'language'], 'required'],
            [['documenti_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['titolo', 'sottotitolo', 'descrizione_breve', 'descrizione', 'metakey', 'metadesc', 'link_document', 'drive_file_id', 'object', 'extended_description', 'distribution_license', 'dates_and_intermediate_stages', 'further_information', 'regulatory_requirements', 'protocol', 'help_box', 'author'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['documenti_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\documenti\models\Documenti::className(), 'targetAttribute' => ['documenti_id' => 'id']],
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
    'documenti_id' => Yii::t('amostranslation', 'Documenti ID'),
    'language' => Yii::t('amostranslation', 'Language'),
    'titolo' => Yii::t('amostranslation', 'Titolo'),
    'sottotitolo' => Yii::t('amostranslation', 'Sottotitolo'),
    'descrizione_breve' => Yii::t('amostranslation', 'Descrizione Breve'),
    'descrizione' => Yii::t('amostranslation', 'Descrizione'),
    'metakey' => Yii::t('amostranslation', 'Metakey'),
    'metadesc' => Yii::t('amostranslation', 'Metadesc'),
    'link_document' => Yii::t('amostranslation', 'Link Document'),
    'drive_file_id' => Yii::t('amostranslation', 'Drive File ID'),
    'status' => Yii::t('amostranslation', 'Status'),
    'created_by' => Yii::t('amostranslation', 'Created By'),
    'updated_by' => Yii::t('amostranslation', 'Updated By'),
    'deleted_by' => Yii::t('amostranslation', 'Deleted By'),
    'created_at' => Yii::t('amostranslation', 'Created At'),
    'updated_at' => Yii::t('amostranslation', 'Updated At'),
    'deleted_at' => Yii::t('amostranslation', 'Deleted At'),
    'object' => Yii::t('amostranslation', 'Object'),
    'extended_description' => Yii::t('amostranslation', 'Extended Description'),
    'distribution_license' => Yii::t('amostranslation', 'Distribution License'),
    'dates_and_intermediate_stages' => Yii::t('amostranslation', 'Dates And Intermediate Stages'),
    'further_information' => Yii::t('amostranslation', 'Further Information'),
    'regulatory_requirements' => Yii::t('amostranslation', 'Regulatory Requirements'),
    'protocol' => Yii::t('amostranslation', 'Protocol'),
    'help_box' => Yii::t('amostranslation', 'Help Box'),
    'author' => Yii::t('amostranslation', 'Author'),
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getDocumenti()
    {
    return $this->hasOne(\open20\amos\documenti\models\Documenti::className(), ['id' => 'documenti_id']);
    }
}
