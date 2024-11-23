<?php

namespace app\modules\cms\base;


use open20\amos\events\models\Event;
use luya\admin\filters\LargeThumbnail;
use luya\cms\frontend\events\BeforeRenderEvent;
use luya\cms\models\NavItem;
use luya\Exception;
use luya\TagParser;
use luya\web\filters\ResponseCache;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use Lullabot\AMP\AMP;
use Lullabot\AMP\Validate\Scope;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;
use yii\web\View;

class DefaultController extends \luya\cms\frontend\controllers\DefaultController
{

    private $queryVars = [
        'amp',
        'path'
    ];


    /**
     *
     * @return boolean
     */
    private function isAmp()
    {
        $is = false;

        foreach ($this->queryVars as $var) {
            $value = \Yii::$app->request->getQueryParam($var);
            if (!empty($value)) {
                if ($value == 'amp') {
                    $is = true;
                }
                break;
            }
        }
        return $is;
    }

    /**
     *
     * @param type $view
     * @param type $params
     * @return type
     */
    public function render($view, $params = array())
    {

        $html = null;

        if ($this->isAmp()) {
            $this->layout = "@app/views/layouts/main_amp";
        }

        $html = parent::render($view, $params);
        return $html;
    }

    public function renderContent($view)
    {

        $html = null;

        if ($this->isAmp()) {
            $this->layout = "@app/views/layouts/main_amp";
        }

        $html = parent::renderContent($view);
        return $html;
    }

    /**
     * @return null|string|Response
     * @throws NotFoundHttpException
     * @throws \yii\web\MethodNotAllowedHttpException
     */
    public function actionIndex()
    {
        try {
            $current = \Yii::$app->menu->current;
        } catch (Exception $e) {
            // An exception while resolving, check for internal redirect otherwise throw not found exception.
            if (($redirect = $this->findInternalRedirect())) {
                return $redirect;
            }

            throw new NotFoundHttpException($e->getMessage());
        }

        // the current resolved item seems to be the 404 page
        if ($current->is404Page) {
            // find redirects
            if (($redirect = $this->findInternalRedirect())) {
                return $redirect;
            }

            // set status 404 and render the item
            \Yii::$app->response->statusCode = 404;
        }

        $content = $this->renderItem($current->id, Yii::$app->menu->currentAppendix);

        // It seems to be a json response. Yii::$app->response->format should be FORMAT_JSON or FORMAT_JSONP
        if (is_array($content)) {
            return $this->asJson($content);
        }

        // Default format is FORMAT_HTML, if RAW is used we render the content without layout.
        if (\Yii::$app->response->format == Response::FORMAT_RAW) {
            return $content;
        }

        return $this->renderContent($content);
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
//        $model = NavItem::find()->where(['id' => $navItemId])->with(['nav'])->one();
        \Yii::$app->language = 'it-IT';
        if (\Yii::$app->request->get('language')) {
            \Yii::$app->language = \Yii::$app->request->get('language');
        }
        $model = \app\modules\cms\models\NavItem::find()->where(['id' => $navItemId])->with(['nav'])->one();

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
        $modelContent = \open20\amos\seo\AmosSeo::getModelFromPrettyUrl($model->alias);
        $ogTitle = $this->view->title;
        $ogDescription = '';
        $ogImage = '';

        if (!empty($model->image_id)) {
            $image = Yii::$app->storage->getImage($model->image_id);
            if ($image) {
                $ogImage = $image->applyFilter(LargeThumbnail::identifier())->sourceAbsolute;
            }
        }

        $isEventDetail = !empty($modelContent) && get_class($modelContent) == 'open20\amos\events\models\Event';

        if (!$isEventDetail) {
            $this->view->registerMetaTag(['property' => 'og:type', 'content' => 'website'], self::META_OG_TYPE);
            $this->view->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'], self::META_TWITTER_CARD);

            $this->view->registerMetaTag(['property' => 'og:title', 'content' => $ogTitle], self::META_OG_TITLE);
            $this->view->registerMetaTag(['name' => 'twitter:title', 'content' => $ogTitle], self::META_TWITTER_TITLE);

            $this->view->registerMetaTag(['property' => 'og:url', 'content' => Yii::$app->request->absoluteUrl], self::META_OG_URL);
            $this->view->registerMetaTag(['name' => 'twitter:url', 'content' => Yii::$app->request->absoluteUrl], self::META_TWITTER_URL);

            if (!empty($ogDescription)) {
                $this->view->registerMetaTag(['name' => 'description', 'content' => $ogDescription], self::META_DESCRIPTION);
                $this->view->registerMetaTag(['property' => 'og:description', 'content' => $ogDescription], self::META_OG_DESCRIPTION);
                $this->view->registerMetaTag(['name' => 'twitter:description', 'content' => $ogDescription], self::META_TWITTER_DESCRIPTION);
            }

            if (!empty($model->keywords)) {
                $this->view->registerMetaTag(['name' => 'keywords', 'content' => implode(", ", $currentMenu->keywords)], self::META_KEYWORDS);
            }

            if ($ogImage) {
                $this->view->registerMetaTag(['property' => 'og:image', 'content' => $ogImage], self::META_OG_IMAGE);
                $this->view->registerMetaTag(['name' => 'twitter:image', 'content' => $ogImage], self::META_TWITTER_IMAGE);
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

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $disableCache = false;
        if (!empty(\Yii::$app->params['disablePageCache']) || !empty($_GET['disablePageCache'])) {
            $disableCache = true;
        }
        $profileUpdated = 'guest';
        if (!\Yii::$app->user->isGuest) {
            if(!empty($_COOKIE['updated_profile'])){
                $profileUpdated = $_COOKIE['updated_profile'];
            }


//            $query = new Query();
//            $count = $query->from('event_invitation')
//                ->select(new Expression('event_invitation.*'))
//                ->innerJoin('event_landing', 'event_landing.event_id = event_invitation.event_id')
//                ->andWhere(['event_invitation.user_id' => Yii::$app->user->id])
//                ->andWhere(['event_landing.url' => Yii::$app->menu->current->alias])
//                ->andWhere(['event_invitation.deleted_at' => null])
//                ->count();
//            if ($count > 0) {
////                foreach ( $count as $a){
////                    pr($a);
////                }
//                $isRegistered = '1';
//            }
        }

        // enable full page cache behavior if supported by page and enabled in module.
        $behaviors['pageCache'] = [
            'class' => ResponseCache::class,
            'variations' => [
                Yii::$app->request->url,
                !empty($_COOKIE['cookie_privacy']) ? $_COOKIE['cookie_privacy'] : 'no_cookie',
                Yii::$app->language,
                Yii::$app->request->get(),
                Yii::$app->request->post(),
                \Yii::$app->session->get('IDM'),
                Yii::$app->user->isGuest ? 'guest' : \Yii::$app->user->id,
                Yii::$app->user->isGuest ? 'guest' : $profileUpdated
            ],
            'cache' => 'landingCache',
            'duration' => Event::CACHE_DURATION,
            'dependency' => [
                'class' => 'yii\caching\DbDependency',
                'sql' => Event::CACHE_SQL_DEPENDENCY,
            ],
            'enabled' => !$disableCache && $this->isFullPageCacheEnabledCms(),
        ];

        //        pr($this->module->fullPageCache, 'fullpageCache');
//        pr(Yii::$app->request->isGet, 'isget');
//        pr(Yii::$app->menu->current, 'current');
//        pr(Yii::$app->menu->current->type == \app\modules\cms\models\NavItem::TYPE_PAGE, 'current');
//        pr(!Yii::$app->menu->current->is404Page, '404');
//        pr($this->isAdminLoggedInCms(), 'isadminloggedincms');
//        pr((int) NavItem::find()->where(['nav_id' => Yii::$app->menu->current->navId, 'lang_id' => Yii::$app->adminLanguage->activeId])->select(['is_cacheable'])->scalar(), 'query');
//die;
        return $behaviors;
    }

    /**
     * Determines whether the full page cache is enabled or not.
     *
     * @return boolean
     * @since 2.1.0
     */
    protected function isFullPageCacheEnabledCms()
    {
        // if the page could not be found, caching is disable otherwise the behaviors method would
        // throw an exception which then would stop execute the "find redirects" task.
        try {
            return $this->module->fullPageCache && Yii::$app->request->isGet && Yii::$app->menu->current && Yii::$app->menu->current->type == \app\modules\cms\models\NavItem::TYPE_PAGE && !Yii::$app->menu->current->is404Page && $this->isAdminLoggedInCms() && (int)NavItem::find()->where(['nav_id' => Yii::$app->menu->current->navId, 'lang_id' => Yii::$app->adminLanguage->activeId])->select(['is_cacheable'])->scalar();
        } catch (NotFoundHttpException $notFound) {
            return false;
        }
    }

    /**
     * Returns whether admin user is working in frontend context.
     *
     * @return boolean Whether caching should be enabled or not.
     * @since 3.5.0
     */
    private function isAdminLoggedInCms()
    {
        return Yii::$app->has('adminuser') ? Yii::$app->adminuser->isGuest : true;
    }


}
