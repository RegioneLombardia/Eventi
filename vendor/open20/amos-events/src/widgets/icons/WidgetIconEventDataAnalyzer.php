<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events
 * @category   CategoryName
 */

namespace open20\amos\events\widgets\icons;

use open20\amos\core\widget\WidgetIcon;
use open20\amos\events\AmosEvents;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconEventsManagement
 * @package open20\amos\events\widgets\icons
 */
class WidgetIconEventDataAnalyzer extends WidgetIcon
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setLabel(AmosEvents::t('amosevents', 'Analisi dati'));
        $this->setDescription(AmosEvents::t('amosevents', 'Analisi dati'));
        $this->setIcon('account-box-mail');
        $this->setIconFramework('am');
        $this->setUrl(['/events/data-analyzer/analyze-event-types']);
        $this->setCode('EVENT_MANAGEMENT');
        $this->setModuleName('events');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                [
                    'bk-backgroundIcon',
                    'color-lightPrimary'
                ]
            )
        );
    }

}
