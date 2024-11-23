<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

namespace backend\modules\tickets\widgets\icons;

use open20\amos\core\icons\AmosIcons;
use open20\amos\core\widget\WidgetIcon;
use Yii;
use yii\helpers\ArrayHelper; 

class WidgetIconAssistenza extends WidgetIcon {

    public function init() {
        parent::init();

        $paramsClassSpan = [
            'bk-backgroundIcon',
            'color-greyColor'
        ];

        $this->setLabel(\open20\amos\core\module\BaseAmosModule::tHtml('amosapp','Assistenza'));
        $this->setDescription(Yii::t('amosapp', 'Email all\'assistenza'));


        if (!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('assistenza');
            $paramsClassSpan = [];
        } else {
            $this->setIconFramework(AmosIcons::AM);
            $this->setIcon('email');
        }
        
        $this->setUrl(Yii::$app->urlManager->createUrl(['/tickets/default/index']));
        $this->setCode('ASSISTENZA');
        $this->setModuleName('Assistenza');
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                $paramsClassSpan
            )
        );
    }

//    public function getOptions() {
//        $options = parent::getOptions();
//
//        //aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
//        return ArrayHelper::merge($options, ["children" => $this->getWidgetsIcon()]);
//    }

    /**
     * Recupera i widget figli da far visualizzare nella dashboard secondaria    
     * @return [open20\amos\core\widget\WidgetIcon] Array con i widget della dashboard
     */
//    public function getWidgetsIcon() {
//        $widgets = [];
//
//        $widget = \open20\amos\dashboard\models\AmosWidgets::find()->andWhere(['module' => 'registry'])->andWhere(['type' => 'ICON'])->andWhere(['!=', 'child_of', NULL])->all();
//
//        foreach ($widget as $Widget) {
//            $className = (strpos($Widget['classname'], '\\') === 0)? $Widget['classname'] : '\\' . $Widget['classname'];
//            $widgetChild = new $className;
//            if($widgetChild->isVisible()){
//                $widgets[] = $widgetChild->getOptions();
//            }
//        }
//        return $widgets;
//    }

}
