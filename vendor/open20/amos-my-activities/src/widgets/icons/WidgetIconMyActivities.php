<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\myactivities\widgets\icons
 * @category   CategoryName
 */

namespace open20\amos\myactivities\widgets\icons;

use open20\amos\core\widget\WidgetIcon;

use open20\amos\myactivities\AmosMyActivities;
use open20\amos\myactivities\models\MyActivities;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconMyActivities
 * @package open20\amos\myactivities\widgets\icons
 */
class WidgetIconMyActivities extends WidgetIcon
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setLabel(AmosMyActivities::tHtml('amosmyactivities', 'My activities'));
        $this->setDescription(AmosMyActivities::t('amosmyactivities', 'My activities'));
        $this->setIcon('bullhorn');
        $this->setUrl(Yii::$app->urlManager->createUrl(['myactivities/my-activities/index']));
        $this->setCode('MYACTIVITIES');
        $this->setModuleName('myactivities');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                [
                    'bk-backgroundIcon',
                    'color-darkPrimary'
                ]
            )
        );

        $this->setBulletCount(
            $this->makeBulletCounter()
        );
    }

    /**
     * 
     * @param type $user_id
     * @return type
     */
    public function makeBulletCounter($userId = null, $className = null, $externalQuery = null)
    {
        return MyActivities::getCountActivities(true);
    }

}
