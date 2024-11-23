<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 22/12/2021
 * Time: 16:01
 */

namespace backend\modules\tickets\controllers;


use open20\amos\core\controllers\BackendController;
use open20\amos\core\controllers\BaseController;
use luya\cms\models\Nav;
use luya\cms\models\NavItem;
use luya\cms\models\NavItemModule;
use luya\cms\models\NavItemPage;
use luya\cms\models\NavItemPageBlockItem;
use luya\cms\models\NavItemRedirect;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ExportLuyaController extends BackendController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['export-page', 'import'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
    }

    public function actionExportPage($nav_id)
    {
        //in cms_nav   nav_item_container_id  deve essere settato all 'importazione
        // si dovrebbero ciclare le altre nav usando parent_nav_id  in cms_nav
        $export = [];
        $nav = Nav::findOne($nav_id);
        $export[get_class($nav)] = $nav->attributes;
//        pr($nav->attributes, 'nav');

        $navItems = $nav->navItems;

        /** @var  $navItem NavItem */
        foreach ($navItems as $navItem) {
            if (!empty($navItem)) {
                $export[get_class($navItem)][] = $navItem->attributes;
//            pr($navItem->attributes, 'navitem');

                $navItemPage = NavItemPage::find()->andWhere(['nav_item_id' => $navItem->nav_item_type_id])->one();
//            pr($navItemPage->attributes, 'navitempage');
                $export[get_class($navItemPage)] = $navItemPage->attributes;

                if ($navItemPage) {
                    $blockItems = NavItemPageBlockItem::find()->andWhere(['nav_item_page_id' => $navItemPage->id])->all();
                    foreach ($blockItems as $blockItem) {
//                    pr($blockItem->attributes, 'block item');
                        $export[get_class($blockItem)][] = $blockItem->attributes;

                    }
                    $navItemRedirect = NavItemRedirect::findOne($navItem->nav_item_type_id);
                    $navItemModule = NavItemModule::findOne($navItem->nav_item_type_id);
//
                    $export[get_class($navItemRedirect)] = $navItemRedirect->attributes;
                    $export[get_class($navItemModule)] = $navItemModule->attributes;

                }
            }
        }
        $encodedExport = serialize($export);
        $name = "uploadfiles/export_page_$nav_id.txt";
        $myfile = fopen($name, "w+") or die("Unable to open file!");
        fwrite($myfile, $encodedExport);
        fclose($myfile);


        $filePath = \Yii::getAlias('@app') . '/web/' . $name;
        pr($filePath);
        die;
        if (file_exists($filePath)) {
            return \Yii::$app->response->sendFile($filePath);
        } else {
            pr('non esisite');
        }
        die;
//        $navItemsChildren = $this->findChildren($nav_id, true);
//        foreach ($navItemsChildren as $navItem){
//            pr($navItem->attributes);
//        }
//

        pr('action');
        die;
    }


    /**
     * @param $nav_id
     * @param bool $first
     * @return array|Nav|null|\yii\db\ActiveRecord[]
     */
    public function findChildren($nav_id, $first = false)
    {
        $navItems = Nav::find()->andWhere(['parent_nav_id' => $nav_id])->all();
        if (empty($navItems)) {
            if (!$first) {
                return Nav::findOne($nav_id);
            }
        }
        foreach ($navItems as $navItem) {
            $navItems[] = $this->findChildren($navItem->id);
        }
        return $navItems;
    }


    public function actionImport($filePath, $container_id = 1)
    {
        if (file_exists($filePath)) {

            $string = file_get_contents($filePath);
            $import = unserialize($string);
            $navClass = 'luya\cms\models\Nav';
            $navItemClass = 'luya\cms\models\NavItem';
            $navItemPageClass = 'luya\cms\models\NavItemPage';
            pr($import[$navClass]);

            $nav = new Nav();
//
//            $nav->load(['Nav' => ['parent_nav_id = 2']]);
//            $nav->nav_container_id = $container_id;
//            pr($nav->attributes);
        }
        die;
    }


}