<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 26/10/2020
 * Time: 14:52
 */

namespace app\modules\cms\controllers;


use luya\cms\models\NavItem;
use luya\cms\models\NavItemPage;

class NavItemController extends \luya\cms\admin\apis\NavItemController
{

    /**
     * Returns an array of 10 items for last updated pages.
     *
     * @return array
     */
    public function actionLastUpdates()
    {

//        $myfile = fopen("sovrascitto.txt", "w") or die("Unable to open file!");
//        $txt = "John Doe\n";
//        fwrite($myfile, $txt);
//        $txt = "Jane Doe\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);

        $nav_permissions = NavItem::find()
            ->innerJoin('cms_nav_permission','cms_nav_permission.nav_id = cms_nav_item.id')
            ->innerJoin('admin_user_group','cms_nav_permission.group_id = admin_user_group.group_id')
            ->andWhere(['admin_user_group.user_id' => \Yii::$app->adminuser->id])
            ->asArray(true);
        $sql = $nav_permissions->all();
        $navItemIds =[];
        if(!empty($sql)){
            foreach ($sql as $page){
                $navItemIds []= $page['id'];
            }
        }

        return NavItem::find()
            ->select(['cms_nav_item.title', 'timestamp_update', 'update_user_id', 'nav_id'])
            ->limit(10)
            ->orderBy(['timestamp_update' => SORT_DESC])
            ->joinWith(['updateUser' => function ($q) {
                $q->select(['firstname', 'lastname', 'id'])->where([]);
            }, 'nav'])
            ->where(['cms_nav.is_deleted' => false])
            ->andFilterWhere(['cms_nav_item.id' => $navItemIds])
            ->asArray(true)
            ->all();
    }

    /**
     * The data api for a nav id and correspoding language.
     *
     * http://example.com/admin/api-cms-navitem/nav-lang-item?access-token=XXX&navId=A&langId=B.
     *
     * @param integer $navId
     * @param integer $langId
     * @return array
     */
    public function actionNavLangItem($navId, $langId)
    {
        $nav_permissions = NavItem::find()
            ->innerJoin('cms_nav_permission','cms_nav_permission.nav_id = cms_nav_item.id')
            ->innerJoin('admin_user_group','cms_nav_permission.group_id = admin_user_group.group_id')
            ->andWhere(['admin_user_group.user_id' => \Yii::$app->adminuser->id])
            ->asArray(true);
        $sql = $nav_permissions->all();
        $navItemIds =[];
        if(!empty($sql)){
            foreach ($sql as $page){
                $navItemIds []= $page['id'];
            }
        }

        $item = NavItem::find()->with('nav')->andWhere(['nav_id' => $navId, 'lang_id' => $langId])
            ->andFilterWhere(['cms_nav_item.nav_id' => $navItemIds])->one();

        if ($item) {
            return [
                'error' => false,
                'item' => $item->toArray(),
                'nav' => $item->nav->toArray(),
                'typeData' => ($item->nav_item_type == 1) ? NavItemPage::getVersionList($item->id) : $item->getType()->toArray(),
            ];
        }

        return ['error' => true];
    }
}