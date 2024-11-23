<?php

namespace open20\amos\events\widgets;


use open20\amos\events\utility\EventsUtility;
use yii\base\Widget;

class ChangeLanguage extends Widget
{
    public $model;
    public $classnameEnabled = [
        'open20\amos\news\models\News',
        'open20\amos\documenti\models\Documenti',
        'open20\amos\discussioni\models\DiscussioniTopic',
        'open20\amos\events\models\Event',
        'amos\sitemanagement\models\SiteManagementSliderElem',
    ];

    public function run()
    {
        if(\Yii::$app->controller->action->id == 'create'){
            return '';
        }

        if(empty($this->model)){
            $controller = \Yii::$app->controller;
            if(method_exists($controller, 'getModelObj')){
                $this->model = $controller->getModelObj();
            }
            if(!empty($this->model)){
                if(!in_array(get_class($this->model),$this->classnameEnabled)){
                    return '';
                }
            }
        }
        $okMultilanguage = EventsUtility::isMultilanguageEnabled();
        if (
             (!$this->model->isNewRecord && method_exists($this->model,'canShowTranslationInLine') && $this->model->canShowTranslationInLine())
            || (!$this->model->isNewRecord && !method_exists($this->model,'canShowTranslationInLine')&& $okMultilanguage )
        ) {
            return $this->render('change_language');
        }
        return '';
    }

}