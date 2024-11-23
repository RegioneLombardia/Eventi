<?php

namespace open20\amos\events\models\base;

use open20\amos\core\validators\StringHtmlValidator;
use open20\amos\documenti\models\Documenti;
use open20\amos\events\AmosEvents;
use open20\amos\events\models\EventLandingDocuments;
use open20\amos\news\models\News;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "event_landing".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $advanced_options_landing
 * @property string $description
 * @property string $schedule
 * @property string $title_video
 * @property string $title_documents
 * @property string $title_request_info
 * @property string $contact_info_organizator
 * @property string $title_pics
 * @property string $title_info
 * @property string $title_protagonists
 * @property string $title_maps
 * @property string $title_schedule
 * @property string $title_presentation
 * @property string $title_related_events
 * @property string $description_protagonists
 * @property string $map_style
 * @property integer $image_slider_id
 * @property string $gallery_type
 * @property integer $video_slider_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property integer $ask_sex,
 * @property integer $ask_age,
 * @property integer $ask_county,
 * @property integer $ask_city,
 * @property integer $ask_telefon,
 * @property integer $ask_fiscal_code,
 * @property integer $ask_company,
 * @property integer $ask_tags,
 * @property integer $ask_sex_required,
 * @property integer $ask_age_required,
 * @property integer $ask_county_required,
 * @property integer $ask_city_required,
 * @property integer $ask_telefon_required,
 * @property integer $ask_fiscal_code_required,
 * @property integer $ask_company_required,
 * @property integer $ask_tags_required,
 * @property integer $social_reg,
 * @property integer $facebook_reg,
 * @property integer $twitter_reg,
 * @property integer $linkedin_reg,
 * @property integer $goolge_reg,
 * @property integer $apple_reg,
 * @property integer $spid_cns_reg,
 * @property integer $user_name_reg,
 * @property string $streaming_url
 * @property integer $enable_streaming
 * @property integer $streaming_type
 * @property string $date_begin_streaming
 * @property string $date_sending_memo
 * @property string $text_sending_memo
 * @property string $subject_sending_memo
 * @property integer $event_communication_memo_id
 * @property integer $luya_page_id
 * @property integer $nav_id_tks_page
 * @property integer $nav_id_wating_page
 * @property integer $nav_id_already_present_page
 * @property string thank_you_subscribe,
 * @property string thank_you_registered,
 * @property string thank_you_waiting_list
 *
 * @property \open20\amos\events\models\Event $event
 * @property \open20\amos\events\models\EventLandingNews[] $eventLandingNews
 * @property \open20\amos\events\models\EventLandingProtagonist[] $eventLandingProtagonists
 */


class  EventLanding extends \open20\amos\core\record\Record
{
    public $isSearch = false;


    const SCENARIO_MANAGE_CONTENT = 'scenario_manage_content';

    public function scenarios()
    {
        $parentScenarios = parent::scenarios();
        $scenarios = ArrayHelper::merge(
            $parentScenarios,
            [
                self::SCENARIO_MANAGE_CONTENT => $parentScenarios[self::SCENARIO_DEFAULT]
            ]
        );
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_landing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [[ 'luya_template_id','url'], 'required'],
            [['title_instagram_video','title_related_events','title_maps','title_schedule','title_protagonists','title_pics','title_video','title_info','title_documents','title_request_info'],'required', 'on' => self::SCENARIO_MANAGE_CONTENT],
            [['event_communication_memo_id', 'streaming_type','enable_streaming','event_id', 'image_slider_id', 'video_slider_id', 'created_by', 'updated_by', 'deleted_by', 'enable_rating'], 'integer'],
            [['share_title', 'title_presentation','title_protagonists','title_pics','title_video','title_info','title_documents','title_request_info',  'streaming_url','description', 'schedule','subject_sending_memo','text_sending_memo', 'rating_title', 'rating_description'], 'string'],
            [['is_social_share_enabled', 'gallery_type','date_begin_streaming','date_sending_memo','created_at', 'updated_at', 'deleted_at'], 'safe'],
            [
                [
                    'url',
                    'luya_page_id',
                    'luya_template_id',
                    'nav_id_tks_page',
                    'nav_id_wating_page',
                    'nav_id_already_present_page',
                    'userData',
                    'social_reg',
                    'facebook_reg',
                    'twitter_reg',
                    'linkedin_reg',
                    'goolge_reg',
                    'apple_reg',
                    'spid_cns_reg',
                    'user_name_reg',
                    'ask_sex',
                    'ask_age',
                    'ask_county',
                    'ask_city',
                    'ask_telefon',
                    'ask_fiscal_code',
                    'ask_company',
                    'ask_tags',

                    'ask_sex_required',
                    'ask_age_required',
                    'ask_county_required',
                    'ask_city_required',
                    'ask_telefon_required',
                    'ask_fiscal_code_required',
                    'ask_company_required',
                    'ask_tags_required',
                    'thank_you_subscribe',
                    'thank_you_registered',
                    'thank_you_waiting_list',
                    'advanced_options_landing',
                    'contact_info_organizator',
                    'description_protagonists',
                    'map_style'
                ]
                , 'safe'
            ],
            ['url', 'checkAvailabilityUrl'],
            [['url'], 'string', 'max' => 100],
            [['description'], StringHtmlValidator::className(), 'max' => 250],
            [['schedule'], StringHtmlValidator::className(), 'max' => 1500],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\events\models\Event::className(), 'targetAttribute' => ['event_id' => 'id']],
        ];

        if($this->event->eventType->event_type != \open20\amos\events\models\EventType::TYPE_INFORMATIVE){
            $rules = ArrayHelper::merge($rules, [
                ['date_sending_memo', 'compare', 'compareAttribute' => 'date_begin_streaming', 'operator' => '<='],
                ['date_begin_streaming', 'compare', 'compareAttribute' => 'date_sending_memo', 'operator' => '>='],
            ]);
        }
        return $rules;
    }

    public function checkAvailabilityUrl(){
        $utility = new \amos\cmsbridge\utility\CmsUtility();
        $ok = $utility->isUrlOk($this->url, $this->luya_page_id) ;
        if(!$ok){
            $this->addError('url', "L'url della landing esiste già");
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amosevents', 'ID'),
            'event_id' => Yii::t('amosevents', 'Event'),
            'luya_template_id' => AmosEvents::t('amosevents', 'Template'),
            'description' => Yii::t('amosevents', 'Presentation'),
            'schedule' => Yii::t('amosevents', 'Schedule'),
            'streaming_type' => Yii::t('amosevents', 'Tipologia streaming'),
            'streaming_url' => Yii::t('amosevents', 'Url streaming'),
            'text_sending_memo' => Yii::t('amosevents', 'Testo promemoria'),
            'subject_sending_memo' => Yii::t('amosevents', 'Oggetto promemoria'),
            'date_sending_memo' => Yii::t('amosevents', 'Data invio promemoria'),
            'date_begin_streaming' => Yii::t('amosevents', 'Data avvio stereaming'),
            'image_slider_id' => Yii::t('amosevents', 'Slider images'),
            'video_slider_id' => Yii::t('amosevents', 'Slider videos'),
            'instagram_video_slider_id' => Yii::t('amosevents', 'Slider Instagram videos'),
            'gallery_type' => Yii::t('amosevents', 'Tipo di galleria'),
            'title_protagonists' => AmosEvents::t('amosevents', 'Titolo protagonisti'),
            'title_pics' => AmosEvents::t('amosevents', 'Titolo pics'),
            'title_video' => AmosEvents::t('amosevents', 'Titolo video'),
            'title_instagram_video' => AmosEvents::t('amosevents', 'Titolo video Instagram'),
            'title_documents' => AmosEvents::t('amosevents', 'Titolo documenti'),
            'title_info' => AmosEvents::t('amosevents', 'Titolo info'),
            'title_request_info' => AmosEvents::t('amosevents', 'Titolo richiesta informazioni'),
            'description_protagonists' => AmosEvents::t('amosevents', 'Introduzione'),
            'created_at' => Yii::t('amosevents', 'Created at'),
            'updated_at' => Yii::t('amosevents', 'Updated at'),
            'deleted_at' => Yii::t('amosevents', 'Deleted at'),
            'created_by' => Yii::t('amosevents', 'Created by'),
            'updated_by' => Yii::t('amosevents', 'Updated at'),
            'deleted_by' => Yii::t('amosevents', 'Deleted at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(\open20\amos\events\models\Event::className(), ['id' => 'event_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventLandingNews()
    {
        return $this->hasMany(\open20\amos\events\models\EventLandingNews::className(), ['event_landing_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['id' => 'news_id'])->via('eventLandingNews');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventLandingDocuments()
    {
        return $this->hasMany(EventLandingDocuments::className(), ['event_landing_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(Documenti::className(), ['id' => 'documenti_id'])->via('eventLandingDocuments');
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventLandingProtagonists()
    {
        return $this->hasMany(\open20\amos\events\models\EventLandingProtagonist::className(), ['event_landing_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImageSlider()
    {
        return $this->hasOne(\amos\sitemanagement\models\SiteManagementSlider::className(), ['id' => 'image_slider_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVideoSlider()
    {
        return $this->hasOne(\amos\sitemanagement\models\SiteManagementSlider::className(), ['id' => 'video_slider_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstagramVideoSlider()
    {
        return $this->hasOne(\amos\sitemanagement\models\SiteManagementSlider::className(), ['id' => 'instagram_video_slider_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventLandingSections()
    {
        return $this->hasMany(\open20\amos\events\models\EventLandingSection::className(), ['event_landing_id' => 'id']);
    }

}
