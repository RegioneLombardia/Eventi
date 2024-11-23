<?php

namespace app\modules\cmsapi\frontend\controllers;

use open20\amos\events\models\EventLanding;
use open20\amos\events\models\Event;
use luya\admin\filters\LargeThumbnail;
use luya\cms\frontend\events\BeforeRenderEvent;
use luya\TagParser;
use Yii;
use yii\helpers\StringHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\modules\cms\models\NavItem;
use luya\cms\menu\InjectItem;
use luya\cms\frontend\base\Controller;
use yii\web\View;

class PreviewController extends Controller
{

    /**
     * Renders the preview action.
     *
     * @param integer $itemId The nav item to render.
     * @param integer $version The version to display.
     * @param integer $date The date from the preview frame, is false when not using the preview frame from the cms.
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @return \yii\web\Response|string
     */
    public function actionIndex($itemId, $version = false, $date = false)
    {
        \Yii::$app->language = 'it-IT';
        if(\Yii::$app->request->get('language')){
            \Yii::$app->language = \Yii::$app->request->get('language');
        }

        $found = EventLanding::find()
            ->innerJoinWith('event')
            ->innerJoin('event_group_referent','event.event_group_referent_id = event_group_referent.id')
            ->innerJoin('event_group_referent_mm','event_group_referent_mm.event_group_referent_id = event_group_referent.id')
            ->andWhere(['event_group_referent_mm.exclude_from_query' => true ])
            ->andWhere(['event_group_referent_mm.user_id' => \Yii::$app->user->id ])
            ->andWhere(['event_landing.luya_page_id' =>$itemId])
            ->count();
        if ($found == 0) {
                throw new ForbiddenHttpException('Unable to see the preview page, session expired or not logged in.');
        }
        $navItem = NavItem::find()->andWhere(['nav_id' => $itemId])->one();
//        pr($navItem->attributes);

        if (!$navItem) {
            throw new NotFoundHttpException("The requested nav item with id {$itemId} does not exist.");
        }

        $langShortCode = $navItem->lang->short_code;

        Yii::$app->composition['langShortCode'] = $langShortCode;

        $item = Yii::$app->menu->find()->where(['nav_id' => $itemId])->with('hidden')->lang($langShortCode)->one();

        if ($item && !$date && $navItem->nav_item_type_id == $version) {
            return $this->redirect($item->link);
        }

        // this item is still offline so we have to inject and fake it with the inject api
        if (!$item) {
            $navItemId = $navItem->id;
//            pr($navItem->id);
            // create new item to inject
            $inject = new InjectItem([
                'id' => $navItemId,
                'navId' => $navItem->nav->id,
                'childOf' => Yii::$app->menu->home->id,
                'title' => $navItem->title,
                'alias' => $navItem->alias,
                'isHidden' => true,
            ]);
//            pr($inject,'inject');
            // inject item into menu component
            Yii::$app->menu->injectItem($inject);
            // find the inject menu item
            $item = Yii::$app->menu->find()->where(['id' => $inject->id])->with('hidden')->lang($langShortCode)->one();
//            pr($item,'ffff');die;
            // something really went wrong while finding injected item
            if (!$item) {
                throw new NotFoundHttpException("Unable to find the preview for this ID, maybe the page is still Offline?");
            }
        }

        // set the current item, as it would be resolved wrong from the url manager / request path
        Yii::$app->menu->current = $item;

        return $this->renderContent($this->renderItem($navItemId, null, $version));
    }

    /**
     * Render the NavItem content and set several view specific data.
     *
     * @param integer $navItemId
     * @param string $appendix
     * @param boolean|integer $setNavItemTypeId To get the content of a version this parameter will change the database value from the nav item Model
     * to this provided value
     *
     * @return string
     * @throws NotFoundHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function renderItem($navItemId, $appendix = null, $setNavItemTypeId = false)
    {
        \Yii::$app->language = 'it-IT';
        if(\Yii::$app->request->get('language')){
            \Yii::$app->language = \Yii::$app->request->get('language');
        }
        $model = NavItem::find()->where(['id' => $navItemId])->with(['nav'])->one();

        if (!$model) {
            throw new NotFoundHttpException('The requested nav item could not found.');
        }

        Yii::$app->urlManager->contextNavItemId = $navItemId;

        $currentMenu = Yii::$app->menu->current;

        $event = new BeforeRenderEvent();
        $event->menu = $currentMenu;
        foreach ($model->nav->properties as $property) {
            $object = $property->getObject();

            $object->trigger($object::EVENT_BEFORE_RENDER, $event);
            if (!$event->isValid) {
                throw new MethodNotAllowedHttpException('Your are not allowed to see this page.');
                return Yii::$app->end();
            }
        }

        if ($setNavItemTypeId !== false && !empty($setNavItemTypeId)) {
            $model->nav_item_type_id = $setNavItemTypeId;
        }

        $typeModel = $model->getType();

        if (!$typeModel) {
            throw new NotFoundHttpException("The requestd nav item could not be found with the paired type, maybe this version does not exists for this Type.");
        }

        $typeModel->setOptions([
            'navItemId' => $navItemId,
            'restString' => $appendix,
        ]);

        $content = $typeModel->getContent();

        if ($content instanceof Response) {
            return Yii::$app->end(0, $content);
        }

        // it seems to be a json response as it is an array
        if (is_array($content)) {
            return $content;
        }

        // https://github.com/luyadev/luya/issues/863 - if context controller is not false and the layout variable is not empty, the layout file will be displayed
        // as its already renderd by the module controller itself.
        if ($typeModel->controller !== false && !empty($typeModel->controller->layout)) {
            $this->layout = false;
        }

        // If the user has defined a layout file, this will be ensured and set as layout file.
        $layoutFile = $model->nav->layout_file;
        if (!empty($layoutFile)) {
            $this->layout = StringHelper::startsWith($layoutFile, '@') ? $layoutFile : '/' . ltrim($layoutFile, '/');
        }

        if ($this->view->title === null) {
            if (empty($model->title_tag)) {
                $this->view->title = $model->title;
            } else {
                $this->view->title = $model->title_tag;
            }
        }

        $this->view->registerMetaTag(['property' => 'og:type', 'content' => 'website'], self::META_OG_TYPE);
        $this->view->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'], self::META_TWITTER_CARD);

        $this->view->registerMetaTag(['property' => 'og:title', 'content' => $this->view->title], self::META_OG_TITLE);
        $this->view->registerMetaTag(['name' => 'twitter:title', 'content' => $this->view->title], self::META_TWITTER_TITLE);

        $this->view->registerMetaTag(['property' => 'og:url', 'content' => Yii::$app->request->absoluteUrl], self::META_OG_URL);
        $this->view->registerMetaTag(['name' => 'twitter:url', 'content' => Yii::$app->request->absoluteUrl], self::META_TWITTER_URL);

        if (!empty($model->description)) {
            $this->view->registerMetaTag(['name' => 'description', 'content' => $model->description], self::META_DESCRIPTION);
            $this->view->registerMetaTag(['property' => 'og:description', 'content' => $model->description], self::META_OG_DESCRIPTION);
            $this->view->registerMetaTag(['name' => 'twitter:description', 'content' => $model->description], self::META_TWITTER_DESCRIPTION);
        }

        if (!empty($model->keywords)) {
            $this->view->registerMetaTag(['name' => 'keywords', 'content' => implode(", ", $currentMenu->keywords)], self::META_KEYWORDS);
        }

        if (!empty($model->image_id)) {
            $image = Yii::$app->storage->getImage($model->image_id);
            if ($image) {
                $this->view->registerMetaTag(['property' => 'og:image', 'content' => $image->applyFilter(LargeThumbnail::identifier())->sourceAbsolute], self::META_OG_IMAGE);
                $this->view->registerMetaTag(['name' => 'twitter:image', 'content' => $image->applyFilter(LargeThumbnail::identifier())->sourceAbsolute], self::META_TWITTER_IMAGE);
            }
        }

        if ($this->module->enableTagParsing) {
            $content = TagParser::convert($content);
        }

        if (Yii::$app->has('adminuser') && !Yii::$app->request->isAjax && !Yii::$app->adminuser->isGuest && $this->module->overlayToolbar === true) {
            $this->view->registerCssFile('//fonts.googleapis.com/icon?family=Material+Icons');
            $this->view->on(View::EVENT_BEGIN_BODY, [$this, 'renderToolbar'], ['content' => $content]);
        }

        return $content;
    }

    public function preview($itemId, $version = false, $date = false)
    {
        if (Yii::$app->adminuser->isGuest) {
            throw new ForbiddenHttpException('Unable to see the preview page, session expired or not logged in.');
        }

        $navItem = NavItem::findOne($itemId);

        if (!$navItem) {
            throw new NotFoundHttpException("The requested nav item with id {$itemId} does not exist.");
        }

        $langShortCode = $navItem->lang->short_code;

        Yii::$app->composition['langShortCode'] = $langShortCode;

        $item = Yii::$app->menu->find()->where(['id' => $itemId])->with('hidden')->lang($langShortCode)->one();

        if ($item && !$date && $navItem->nav_item_type_id == $version) {
            return $this->redirect($item->link);
        }

        // this item is still offline so we have to inject and fake it with the inject api
        if (!$item) {
            // create new item to inject
            $inject = new InjectItem([
                'id' => $itemId,
                'navId' => $navItem->nav->id,
                'childOf' => Yii::$app->menu->home->id,
                'title' => $navItem->title,
                'alias' => $navItem->alias,
                'isHidden' => true,
            ]);
            // inject item into menu component
            Yii::$app->menu->injectItem($inject);
            // find the inject menu item
            $item = Yii::$app->menu->find()->where(['id' => $inject->id])->with('hidden')->lang($langShortCode)->one();
            // something really went wrong while finding injected item
            if (!$item) {
                throw new NotFoundHttpException("Unable to find the preview for this ID, maybe the page is still Offline?");
            }
        }

        // set the current item, as it would be resolved wrong from the url manager / request path
        //Yii::$app->menu->current = $item;

        return $this->renderContent($this->renderItem($itemId, null, $version));
    }
}