<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Widgets
 */

namespace open20\webmeeting\widgets\icons;

use open20\webmeeting\WebMeetingModule;

use open20\amos\core\widget\WidgetIcon;
use open20\amos\core\widget\WidgetAbstract;
use open20\amos\core\icons\AmosIcons;
use open20\amos\dashboard\models\AmosUserDashboards;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * 
 */
class WidgetIconWebMeetingMeeting extends WidgetIcon
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

        $this->setLabel(WebMeetingModule::_tHtml('#webmeeting_webmeeting'));
        $this->setDescription(WebMeetingModule::_t('#webmeeting_widget_meeting'));

        if (!empty(Yii::$app->params['dashboardEngine']) && Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $paramsClassSpan = [];
        }

        $this->setIcon('videoconferenza');
        $this->setUrl(WebMeetingModule::getMyIndexLink());
        $this->setCode(WebMeetingModule::WEBMEETING_CODE_FOR_WIDGETS);
        $this->setModuleName(WebMeetingModule::getModuleName());
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                $paramsClassSpan
            )
        );
    }

}
