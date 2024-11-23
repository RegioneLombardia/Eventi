<?php

namespace open20\amos\groups\widgets\icons;

use open20\amos\core\widget\WidgetIcon;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconGroups
 * @package open20\amos\groups\widgets\icons
 */
class WidgetIconGroups extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setLabel(\Yii::t('amosgroups', 'Comunicazioni gruppi'));
        $this->setDescription(Yii::t('amosgroups', 'Groups'));
        $this->setIcon('group');
        $this->setIconFramework('am');
        $this->setUrl(Yii::$app->urlManager->createUrl(['/groups/groups']));
        $this->setModuleName('partecipanti');
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-primary'
        ]));
    }
}
