<?php

namespace app\modules\cmsapi\frontend\utility\cmspageblock;

use app\modules\cmsapi\frontend\models\PostCmsCreatePage;
use app\modules\cmsapi\frontend\utility\CmsBlocksBuilder;
use open20\amos\events\utility\EventsUtility;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use open20\amos\events\AmosEvents;

class CmsLandingFormPageBlock extends CmsPageBlock
{
    private $name_surname_email = [];
    private $extra_fields = [];

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->initialize();
    }

    protected function initialize()
    {
        $this->name_surname_email['name'] = new CmsLandingFormField(['required' => 1,
            'type' => 'string',
            'field' => 'name',
            'label' => "Nome"]);
        $this->name_surname_email['surname'] = new CmsLandingFormField(['required' => 1,
            'type' => 'string',
            'field' => 'surname',
            'label' => "Cognome"]);
        $this->name_surname_email['email'] = new CmsLandingFormField(['required' => 1,
            'type' => 'email',
            'field' => 'email',
            'label' => "Email"]);

        $this->extra_fields ['sex'] = new CmsLandingFormField(['required' => 0, 'type' => 'select',
            'field' => 'sex',
            'label' => "Sesso",
            'subvalue' => [
                ["value" => "None", "label" => AmosEvents::t('amosevents',"Non Dichiarato")],
                ["value" => "Maschio", "label" => AmosEvents::t('amosevents',"Maschio")],
                ["value" => "Femmina", "label" => AmosEvents::t('amosevents',"Femmina")],
            ]]);

        $this->extra_fields ['age'] = new CmsLandingFormField(['required' => 0,
            'type' => 'select',
            'field' => 'age',
            'label' => "Eta",
            'subvalue' => [
                ["value" => "1", "label" => "18-25"],
                ["value" => "2", "label" => "26-35"],
                ["value" => "3", "label" => "36-45"],
                ["value" => "4", "label" => "46-55"],
                ["value" => "5", "label" => "56-65"],
                ["value" => "6", "label" => ">65"],
            ]]);
        $this->extra_fields ['country'] = new CmsLandingFormField(['required' => 0,
            'type' => 'selectdb',
            'field' => 'country',
            'subvalue' => [
                ["value" => "app\controllers\FrontendUtility::getIstatProvince", "label" => ""],
            ],
            'label' => "Provincia"]);
        $this->extra_fields ['city'] = new CmsLandingFormField(['required' => 0,
            'type' => 'selectrel',
            'field' => 'city',
            'subvalue' => [
                [
                    "value" => "country-id",
                    "label" => "/comuni/default/comuni-by-provincia",
                ],
                'otheroptions' => ['data-class' => 'open20\amos\comuni\IstatProvince', 'data-relattribute' => 'country'],

            ],
            'label' => "Citta'"]);
        $this->extra_fields ['telefon'] = new CmsLandingFormField(['required' => 0,
            'type' => 'string',
            'field' => 'telefon',
            'label' => "Telefono"]);
        $this->extra_fields ['fiscal_code'] = new CmsLandingFormField(['required' => 0,
            'type' => 'hidden',
            'field' => 'fiscal_code',
            'label' => "Codice Fiscale"]);

        $this->extra_fields ['company'] = new CmsLandingFormField(['required' => 0,
            'type' => 'string',
            'field' => 'company',
            'label' => "Azienda"]);

        $this->extra_fields ['n_companions'] = new CmsLandingFormField(['required' => 0,
            'type' => 'companions',
            'field' => 'n_companions',
            'label' => "Aggiungi accompagnatori"
        ]);

        $this->extra_fields ['preference_tags'] = new CmsLandingFormField(['required' => 0,
            'type' => 'preferenceTags',
            'field' => 'preference_tags',
            'subvalue' => $this->getTags(),
            'label' => "Tag Interesse informativo"]);

        $this->extra_fields ['tags'] = new CmsLandingFormField(['required' => 0,
            'type' => 'checkboxList',
            'field' => 'tags',
            'subvalue' => $this->getTags(),
            'label' => "Interessi"]);

        $labelPresaVisione = "Presa visione dell'informativa al trattamento di dati personali di cui al presente";
        $labelEsprimo = "esprimo il consenso al trattamento dei miei dati personali per la finalità indicata alla lettera a) dell’informativa - registrazione alla piattaforma Eventi *";

        $this->extra_fields['privacy'] = new CmsLandingFormField(['required' => 1,
            'type' => 'radioList',
            'field' => 'privacy',
            'subvalue' => [
                ["value" => "0", "label" => "Non acconsento"],
                ["value" => "1", "label" => "Acconsento"],
            ],
            'label' => "<div><span style=\"color: #ffffff;\">".$labelPresaVisione." "."<a style=\"color: #ffffff;\" href=\"" . \Yii::$app->params['platform']['frontendUrl'] . "/it/privacy\" target=\"_blank\" rel=\"noopener\">link</a>:</span></div><div><span>".$labelEsprimo."</span></div>"]);

        $this->extra_fields['privacy_2'] = new CmsLandingFormField([
            'type' => 'radioList',
            'field' => 'privacy_2',
            'subvalue' => [
                ["value" => "0", "label" => "Non acconsento"],
                ["value" => "1", "label" => "Acconsento"],
            ],
            'label' => \backend\modules\eventsadmin\utility\EventsAdminUtility::getLabelPrivacy2()]);


    }

    /**
     * @return array
     */
    public static function mappingUserProfile()
    {
        return [
            'company' => 'azienda',
            'fiscal_code' => 'codice_fiscale',
            'telefon' => 'telefono',
            'city' => 'nascita_comuni_id',
            'country' => 'nascita_province_id',
            'age' => 'user_profile_age_group_id',
            'sex' => 'sesso',
            'name' => 'nome',
            'surname' => 'cognome',
        ];
    }

    /**
     * @param $luya_attr
     * @return mixed|null
     */
    public static function getUserProfileAttribute($luya_attr)
    {
        $map = self::mappingUserProfile();
        if (!empty($map[$luya_attr])) {
            return $map[$luya_attr];
        } else {
            return null;
        }
    }

    /**
     * @param $userProfile
     * @param $attribute
     * @return string
     */
    public static function parseValueForLuya($userProfile, $attribute)
    {
//        if ($attribute == 'sesso') {
//            switch ($userProfile->$attribute) {
//                case 'None':
//                    $value = 'N';
//                    break;
//                case 'Maschio':
//                    $value = 'M';
//                    break;
//                case 'Femmina':
//                    $value = 'F';
//                    break;
//            }
//        } else {
        $value = $userProfile->$attribute;
//        }
        return $value;
    }


    /**
     *
     * @param PostCmsCreatePage $postCmsPage
     */
    public function buildValues(PostCmsCreatePage $postCmsPage)
    {
        /* var $formLanding PostCmsFormLanding */
        $formLanding = $postCmsPage->form_landing;
        $values = Json::decode($this->json_config_values);
        $valuesCfg = Json::decode($this->json_config_cfg_values);
        $values["table"] = "event_form_" . $postCmsPage->event_data->event_id;

        $values['items'] = [];


        if ($formLanding->user_name_reg) {
            $this->buildFormNameSurname($values['items']);
            $this->buildFormNameSurnameCfg($valuesCfg);
        } else {
            $this->removeFormNameSurname($values['items']);
            $this->removeFormNameSurnameCfg($valuesCfg);
        }

        $valuesCfg['required_fields'] = $this->buildRequiredFields($formLanding);
        $valuesCfg['register_with_social'] = $formLanding->social_reg ? 1 : 0;

        if ($formLanding->social_reg || $formLanding->user_name_reg) {
            $this->is_hidden = 0;
        } else {
            $this->is_hidden = 1;
        }

        if ($formLanding->ask_sex) {
            $this->buildElement($values['items'], $this->extra_fields['sex']);
        } else {
            $this->removeElement($values['items'], $this->extra_fields['sex']);
        }
        if ($formLanding->ask_age) {
            $this->buildElement($values['items'], $this->extra_fields['age']);
        } else {
            $this->removeElement($values['items'], $this->extra_fields['age']);
        }
        if ($formLanding->ask_county) {
            $this->buildElement($values['items'], $this->extra_fields['country']);
        } else {
            $this->removeElement($values['items'], $this->extra_fields['country']);
        }
        if ($formLanding->ask_city) {
            $this->buildElement($values['items'], $this->extra_fields['city']);
        } else {
            $this->removeElement($values['items'], $this->extra_fields['city']);
        }
        if ($formLanding->ask_telefon) {
            $this->buildElement($values['items'], $this->extra_fields['telefon']);
        } else {
            $this->removeElement($values['items'],
                $this->extra_fields['telefon']);
        }
//        if ($formLanding->ask_fiscal_code) {
        $this->buildElement($values['items'], $this->extra_fields['fiscal_code']);
//        } else {
//            $this->removeElement($values['items'],
//                $this->extra_fields['fiscal_code']);
//        }

        if ($formLanding->ask_company) {
            $this->buildElement($values['items'], $this->extra_fields['company']);
        } else {
            $this->removeElement($values['items'],
                $this->extra_fields['company']);
        }

        $this->buildElement($values['items'], $this->extra_fields['n_companions']);

        $this->buildElement($values['items'], $this->extra_fields['preference_tags']);

        if ($formLanding->ask_tags) {
            $this->buildElement($values['items'], $this->extra_fields['tags']);
        } else {
            $this->removeElement($values['items'], $this->extra_fields['tags']);
        }

        $this->buildElement($values['items'], $this->extra_fields['privacy']);
        $this->buildElement($values['items'], $this->extra_fields['privacy_2']);
        $valuesCfg['privacy_form_field'] = $this->extra_fields['privacy']->field;

        $valuesCfg['send_mail'] = 1;
        $valuesCfg['register_on_platform'] = 1;
        $valuesCfg['send_credential'] = 0;
        $valuesCfg['from_email'] = Yii::$app->params['supportEmail'];

        if (!empty($postCmsPage->form_landing->confirm_mail_subject)) {
            $valuesCfg['email_subject'] = $postCmsPage->form_landing->confirm_mail_subject;
        }

        if (!empty($postCmsPage->form_landing->confirm_mail_text)) {
            $valuesCfg['email_text'] = $postCmsPage->form_landing->confirm_mail_text;
        }

        if (!empty($postCmsPage->form_landing->waiting_mail_text)) {
            $valuesCfg['email_waiting_list_text'] = $postCmsPage->form_landing->waiting_mail_text;
        }

        if (!empty($postCmsPage->form_landing->seats_available)) {
            $valuesCfg['seats_available'] = $postCmsPage->form_landing->seats_available;
        }
        if (!empty($postCmsPage->form_landing->community_id)) {
            $valuesCfg['community_id'] = $postCmsPage->form_landing->community_id;
        }

        $this->emailsElements($valuesCfg, $postCmsPage);
        $this->relatedPages($valuesCfg, $postCmsPage);

        $this->json_config_values = Json::encode($values);
        $this->json_config_cfg_values = Json::encode($valuesCfg);
    }

    /**
     * @param $formLanding
     */
    public function buildRequiredFields($formLanding)
    {
        $requiredFields = [];
        if ($formLanding) {
            if ($formLanding->ask_sex_required) {
                $requiredFields [] = 'sex';
            }
            if ($formLanding->ask_age_required) {
                $requiredFields [] = 'age';
            }
            if ($formLanding->ask_county_required) {
                $requiredFields [] = 'country';
            }
            if ($formLanding->ask_city_required) {
                $requiredFields [] = 'city';
            }
            if ($formLanding->ask_telefon_required) {
                $requiredFields [] = 'telefon';
            }
            if ($formLanding->ask_fiscal_code_required) {
                $requiredFields [] = 'fiscal_code';
            }
            if ($formLanding->ask_company_required) {
                $requiredFields [] = 'company';
            }
        }
        return $requiredFields;
    }

    /**
     *
     * @param type $nav_item_page_id
     * @return type
     */
    public static function findBlocks($nav_item_page_id)
    {
        $id_block = static::findBlock(CmsBlocksBuilder::LANDINGFORM);
        $blocks = static::find()->
        andWhere(['nav_item_page_id' => $nav_item_page_id])->
        andWhere(['block_id' => $id_block->id])
            ->all();
        return $blocks;
    }

    /**
     *
     * @param array $items
     */
    protected function buildFormNameSurname(array &$items)
    {
        foreach ($this->name_surname_email as $key) {
            $this->buildElement($items, $key);
        }
    }

    /**
     *
     * @param array $items
     */
    protected function removeFormNameSurname(array &$items)
    {
        foreach ($this->name_surname_email as $key) {
            $this->removeElement($items, $key);
        }
    }

    /**
     *
     * @param array $cfg
     */
    protected function buildFormNameSurnameCfg(array &$cfg)
    {
        $cfg['user_name_form_field'] = $this->name_surname_email['name']->field;
        $cfg['user_surname_form_field'] = $this->name_surname_email['surname']->field;
        $cfg['to_form_field'] = $this->name_surname_email['email']->field;
        $cfg['already_present_field'] = $this->name_surname_email['email']->field;
    }

    /**
     *
     * @param array $items
     */
    protected function removeFormNameSurnameCfg(array &$items)
    {
        $cfg['user_name_form_field'] = '';
        $cfg['user_surname_form_field'] = '';
        $cfg['to_form_field'] = '';
    }

    /**
     *
     * @param array $items
     * @param type $key
     */
    protected function buildElement(array &$items, $key)
    {
        if (!$this->keyPresente($items, $key)) {
            $items[] = $key->toArray();
        }
    }

    /**
     *
     * @param array $items
     * @param type $key
     */
    protected function removeElement(array &$items, $key)
    {
        $item = $this->keyPresente($items, $key);
        if (!is_null($item)) {
            ArrayHelper::removeValue($items, $item);
        }
    }

    /**
     *
     * @param array $items
     * @param type $key
     */
    protected function keyPresente(array $items, CmsLandingFormField $key)
    {

        foreach ($items as $item) {
            if (!strcmp($item['field'], $key->field)) {
                return $item;
            }
        }
        return null;
    }

    /**
     *
     * @return array
     */
    private function getTags()
    {
        $tags = EventsUtility::getPreferenceCenterTags();
        $tagList = [];
        foreach ($tags as $tag) {
            $tagList[] = ['value' => $tag->id, 'label' => $tag->nome];
        }
        return $tagList;
    }

    /**
     *
     * @param string $label
     * @return string
     */
    protected function buildHtmlLabel(string $label)
    {
        return "<!DOCTYPE html>\n<html>\n<head>\n</head>\n<body>\n$label\n</body>\n</html>";
    }

    protected function buildHtmlPrivacyLable()
    {
        $text = "<!DOCTYPE html>\n<html>\n<head>\n</head>\n<body>\n<div><span style=\"color: #ffffff;\">Presa visione dell'informativa al trattamento di dati personali di cui al presente <a style=\"color: #ffffff;\" href=\"" . \Yii::$app->params['platform']['frontendUrl'] . "/it/privacy\" target=\"_blank\" rel=\"noopener\">link</a>:</span></div>\n<div><span>esprimo il consenso al trattamento dei miei dati personali per la finalità indicata alla lettera a) dell’informativa - registrazione alla piattaforma Eventi. *</span></div>\n</body>\n</html>";
        return $text;
    }

    /**
     *
     * @param array $values
     * @param PostCmsCreatePage $postCmsPage
     */
    protected function relatedPages(array &$values,
                                    PostCmsCreatePage $postCmsPage)
    {
        if (!empty($postCmsPage->form_landing->nav_id_tks_page)) {
            $values["tks_page"] = ["type" => 1, "value" => $postCmsPage->form_landing->nav_id_tks_page];
        }
        if (!empty($postCmsPage->form_landing->nav_id_wating_page)) {
            $values["tks_waiting_page"] = ["type" => 1, "value" => $postCmsPage->form_landing->nav_id_wating_page];
        }
        if (!empty($postCmsPage->form_landing->nav_id_already_present_page)) {
            $values["already_present_page"] = ["type" => 1, "value" => $postCmsPage->form_landing->nav_id_already_present_page];
        }
    }

    /**
     *
     * @param array $values
     * @param PostCmsCreatePage $postCmsPage
     */
    protected function emailsElements(array &$values,
                                      PostCmsCreatePage $postCmsPage)
    {
        if (!empty($postCmsPage->form_landing->confirm_mail_subject)) {
            $values['email_subject'] = $postCmsPage->form_landing->confirm_mail_subject;
        }
        if (!empty($postCmsPage->form_landing->confirm_mail_text)) {
            $values['email_text'] = $postCmsPage->form_landing->confirm_mail_text;
        }
        if (!empty($postCmsPage->form_landing->waiting_mail_text)) {
            $values['email_waiting_list_text'] = $postCmsPage->form_landing->waiting_mail_text;
        }
    }
}