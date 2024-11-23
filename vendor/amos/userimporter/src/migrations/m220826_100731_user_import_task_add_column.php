<?php
use yii\db\Migration;
use yii\rbac\Permission;


/**
* Class m220804_163731_user_import_edit_list_permissions*/
class m220826_100731_user_import_task_add_column extends Migration{

   public function safeUp()
   {
       $this->addColumn('user_import_task', 'is_from_edit_list', $this->integer(1)->defaultValue(0)->after('event_group_referent_id'));

   }

   public function safeDown()
   {
       $this->dropColumn('user_import_task', 'is_from_edit_list');

   }
}
