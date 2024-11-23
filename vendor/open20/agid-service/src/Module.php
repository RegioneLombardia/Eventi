<?php

namespace open20\agid\service;

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\agid\service
 * @category   CategoryName
 */
use open20\amos\core\interfaces\CmsModuleInterface;
use open20\amos\core\interfaces\SearchModuleInterface;
use open20\amos\core\module\AmosModule;
use open20\amos\core\module\ModuleInterface;
use open20\agid\service\models\AgidServiceType;
use open20\agid\service\models\AgidServiceTypeRoles;
use Yii;
use yii\helpers\ArrayHelper;
use open20\amos\privileges\interfaces\ServiceCategoriesRolesInterface;

/**
 * Class Module
 * @package open20\amos\organizzazioni
 */
class Module extends AmosModule implements ModuleInterface, SearchModuleInterface, CmsModuleInterface, ServiceCategoriesRolesInterface {

    public static $CONFIG_FOLDER = 'config';

    /**
     * @inheritdoc
     */
    public static function getModuleName() {
        return 'service';
    }

    public function getWidgetIcons() {
        return [];
    }

    public function getWidgetGraphics() {
        return [];
    }

    /**
     * Get default model classes
     */
    protected function getDefaultModels() {
        return [
            'AgidService' => __NAMESPACE__ . '\\' . 'models\AgidService',
            'AgidServiceSearch' => __NAMESPACE__ . '\\' . 'models\search\AgidServiceSearch',
        ];
    }

    /**
     *
     * @return string
     */
    public function getFrontEndMenu($dept = 1) {
        $menu = parent::getFrontEndMenu();
        $app = Yii::$app;
        if (!$app->user->isGuest && (\Yii::$app->user->can('AGIDSERVICE_READ')||\Yii::$app->user->can('REDACTOR_SERVICE'))) {
            $menu .= $this->addFrontEndMenu(Module::t('amosservice', '#menu_front_service'), Module::toUrlModule('/agid-service/index'), $dept);
        }
        return $menu;
    }

    // /**
    //  * @inheritdoc
    //  */
    public function init() {
        parent::init();

        //Configuration: merge default module configurations loaded from config.php with module configurations set by the application
        $config = require(__DIR__ . DIRECTORY_SEPARATOR . self::$CONFIG_FOLDER . DIRECTORY_SEPARATOR . 'config.php');
        Yii::configure($this, ArrayHelper::merge($config, $this));
    }

    public static function getModelClassName() {
        return Module::instance()->model('AgidService');
    }

    public static function getModelSearchClassName() {
        return Module::instance()->model('AgidServiceSearch');
    }

    public static function getModuleIconName() {
        return null;
    }

    public static function getServiceCategoryArrayRole(){

        return  ArrayHelper::map(AgidServiceType::find()->orderBy('name')->all(), 'id', 'name');
    }
    public static function getServiceCategoryArrayRoleAssignedToUser($userId){
        $ids = AgidServiceTypeRoles::find()->select('service_agid_type_id')->andWhere(['user_id' =>$userId])->distinct()->column();
        return  ArrayHelper::map(AgidServiceType::find()->orderBy('name')->andWhere(['id' => $ids,])->all(), 'id', 'name');

    }

    public static function getServiceModelCategoryRole(){
        return AgidServiceTypeRoles::classname();
    }

}
