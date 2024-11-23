<?php

namespace app\modules\cms\models;

use app\modules\cms\admin\Module;
use luya\cms\models\NavItemModule;
use luya\cms\models\NavItemRedirect;
use luya\Exception;

class NavItem extends \luya\cms\models\NavItem
{

    public $__type;
    /**
     * Before create event.
     */
    public function beforeCreate()
    {
        $this->timestamp_create = time();
        $this->timestamp_update = 0;
        $this->create_user_id   = Module::getAuthorUserId();
        $this->update_user_id   = Module::getAuthorUserId();
        $this->slugifyAlias();
    }

    /**
     * Before update event.
     */
    public function eventBeforeUpdate()
    {
        $this->timestamp_update = time();
        $this->update_user_id   = Module::getAuthorUserId();
        $this->slugifyAlias();
    }

    /**
     * GET the type object based on the nav_item_type defintion and the nav_item_type_id which is the
     * primary key for the corresponding type table (page, module, redirect). This approach has been choosen
     * do dynamically extend type of pages whithout any limitation.
     *
     * @return \luya\cms\models\NavItemPage|\luya\cms\models\NavItemModule|\luya\cms\models\NavItemRedirect Returns the object based on the type
     * @throws Exception
     */
    public function getType()
    {
        if ($this->__type === null) {

            // what kind of item type are we looking for
            if ($this->nav_item_type == self::TYPE_PAGE) {
                $this->__type = NavItemPage::findOne($this->nav_item_type_id);
            } elseif ($this->nav_item_type == self::TYPE_MODULE) {
                $this->__type = NavItemModule::findOne($this->nav_item_type_id);
            } elseif ($this->nav_item_type == self::TYPE_REDIRECT) {
                $this->__type = NavItemRedirect::findOne($this->nav_item_type_id);
            }

            if ($this->__type === null) {
                $this->__type = false;
            }

            // set context for the object
            /// 5.4.2016: Discontinue, as the type model does have getNavItem relation
            //$this->_type->setNavItem($this);
        }

        return $this->__type;
    }
}