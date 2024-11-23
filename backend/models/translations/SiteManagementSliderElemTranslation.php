<?php

namespace backend\models\translations;

use Yii;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;

/**
* This is the base-model class for table "site_management_slider_elem__translation".
*
    * @property integer $site_management_slider_elem_id
    * @property string $language
    * @property string $title
    * @property string $description
    * @property string $link
    * @property string $url_video
    * @property string $path_video
    * @property string $status
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    * @property string $instagram_url_video
    *
            * @property \backend\models\translations\SiteManagementSliderElemTranslation $siteManagementSliderElem
    */
class SiteManagementSliderElemTranslation extends \open20\amos\core\record\Record
{


    public $language_source;


/**
* @inheritdoc
*/
public static function tableName()
{
return 'site_management_slider_elem__translation';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['site_management_slider_elem_id', 'language'], 'required'],
            [['site_management_slider_elem_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['title', 'description', 'link', 'url_video', 'path_video', 'instagram_url_video'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['site_management_slider_elem_id'], 'exist', 'skipOnError' => true, 'targetClass' => \amos\sitemanagement\models\SiteManagementSliderElem::className(), 'targetAttribute' => ['site_management_slider_elem_id' => 'id']],
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
    'site_management_slider_elem_id' => Yii::t('amostranslation', 'Site Management Slider Elem ID'),
    'language' => Yii::t('amostranslation', 'Language'),
    'title' => Yii::t('amostranslation', 'Title'),
    'description' => Yii::t('amostranslation', 'Description'),
    'link' => Yii::t('amostranslation', 'Link'),
    'url_video' => Yii::t('amostranslation', 'Url Video'),
    'path_video' => Yii::t('amostranslation', 'Path Video'),
    'status' => Yii::t('amostranslation', 'Status'),
    'created_by' => Yii::t('amostranslation', 'Created By'),
    'updated_by' => Yii::t('amostranslation', 'Updated By'),
    'deleted_by' => Yii::t('amostranslation', 'Deleted By'),
    'created_at' => Yii::t('amostranslation', 'Created At'),
    'updated_at' => Yii::t('amostranslation', 'Updated At'),
    'deleted_at' => Yii::t('amostranslation', 'Deleted At'),
    'instagram_url_video' => Yii::t('amostranslation', 'Instagram Url Video'),
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getSiteManagementSliderElem()
    {
    return $this->hasOne(\amos\sitemanagement\models\SiteManagementSliderElem::className(), ['id' => 'site_management_slider_elem_id']);
    }
}
