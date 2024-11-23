<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\news
 * @category   CategoryName
 */

namespace open20\amos\events\widgets\icons;

use open20\amos\core\widget\WidgetIcon;
use open20\amos\core\widget\WidgetAbstract;
use open20\amos\core\icons\AmosIcons;

use open20\amos\events\AmosEvents;
use open20\amos\events\models\search\EventSearch;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconAllNews
 * @package open20\amos\news\widgets\icons
 */
class WidgetWebexManagment extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $paramsClassSpan = [
            'bk-backgroundIcon',
            'color-primary'
        ];

        $this->setLabel(AmosEvents::tHtml('amosevents', 'Gestione Webex'));
        $this->setDescription(AmosEvents::t('amosevents', 'Visualizza le configurazioni Webex'));

        if (!empty(Yii::$app->params['dashboardEngine']) && Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('videoconferenza');
            $paramsClassSpan = [];
        } else {
            $this->setIcon('videocam');
        }
        $this->setUrl(['/events/event-webex/index']);
        $this->setCode('WEBEX-MANAGMENT');
        $this->setModuleName('events');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                $paramsClassSpan
            )
        );
  

    }

    /**
     * Aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
     * 
     * @inheritdoc
     */
    public function getOptions()
    {
        return ArrayHelper::merge(
            parent::getOptions(),
            ['children' => []]
        );
    }
    /**
     * @return bool
     */
    public function isVisible()
    {
        $module = \Yii::$app->getModule('events');
        if($module){
            if($module->enableNewWizard == true){
                return true;
            }
        }
        return false;
    }

}
