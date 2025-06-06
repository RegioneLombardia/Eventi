<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\widgets\icons
 * @category   CategoryName
 */

namespace open20\amos\admin\widgets\icons;

use open20\amos\core\widget\WidgetIcon;
use open20\amos\core\widget\WidgetAbstract;
use open20\amos\core\icons\AmosIcons;
use open20\amos\admin\AmosAdmin;
use open20\amos\admin\models\UserProfile;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconInactiveUserProfiles
 * @package open20\amos\admin\widgets\icons
 */
class WidgetIconInactiveUserProfiles extends WidgetIcon
{

    /** 
     * @inheritdoc
     */
    public function init() 
    {
        parent::init();

        $paramsClassSpan = [
            'bk-backgroundIcon',
            'color-darkGrey'
        ];

        $this->setLabel(AmosAdmin::tHtml('amosadmin', 'Inactive users'));
        $this->setDescription(AmosAdmin::t('amosadmin', 'List of inactive platform users'));

        if (!empty(Yii::$app->params['dashboardEngine']) && Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('user');
            $paramsClassSpan = [];
        } else {
            $this->setIcon('users');
        }

        $this->setUrl(['/'. AmosAdmin::getModuleName(). '/user-profile/inactive-users']);
        $this->setCode('INACTIVE_USERS');
        $this->setModuleName(AmosAdmin::getModuleName());
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(), $paramsClassSpan
            )
        );

        $query = new Query();
        $query
            ->select([UserProfile::tableName().'.id', UserProfile::tableName().'.attivo', UserProfile::tableName().'.deleted_at'])
            ->from(UserProfile::tableName())
            ->where([UserProfile::tableName().'.attivo' => UserProfile::STATUS_DEACTIVATED])
            ->andWhere([UserProfile::tableName().'.deleted_at' => null]);
       
    }
}