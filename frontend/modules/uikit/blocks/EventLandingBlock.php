<?php


namespace app\modules\uikit\blocks;


use app\modules\cmsapi\frontend\utility\CmsBlocksBuilder;
use app\modules\uikit\Module;
use open20\amos\events\models\Event;
use luya\cms\frontend\blockgroups\MediaGroup;
use luya\cms\frontend\blockgroups\TextGroup;
use app\modules\uikit\Uikit;
use yii\helpers\Json;
use app\modules\cmsapi\frontend\models\PostCmsCreatePage;
use \Yii;

class EventLandingBlock extends \app\modules\uikit\BaseUikitBlock
{

    public $cacheEnabled = false;
    /**
     * @inheritdoc
     */
    protected $component = "eventlanding";

    public function init()
    {
        parent::init();
        $this->cacheEnabled = false;
    }

    /**
     * @inheritdoc
     */
    public function blockGroup()
    {
        return MediaGroup::class;
    }

    /**
     * @inheritdoc
     */
    public function name()
    {
        return Module::t('Contenuti landing eventi');
    }


    /**
     * @inheritdoc
     */
    public function icon()
    {
        return 'view_module';
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function frontend(array $params = array())
    {
        $data = [];
        $event_id = null;
        if (!Uikit::element('data', $params, '')) {
            $configs = $this->getValues();
            $configs["id"] = Uikit::unique($this->component);
            $params['data'] = Uikit::configs($configs);
            $params['debug'] = $this->config();
            $params['filters'] = $this->filters;
            $data = $params['data'];
        }
        if (!empty($data['event_id'])) {
            $event_id = $data['event_id'];
        }

        $model = Event::findOne($event_id);
        if (!empty($model)) {
            $enabledCache = $model->getIsCacheableNavItem();
            $data['eventLanding'] = $model->eventLanding;
            $data['model'] = $model;
            $data['configsCache'] = self::configsCache($model,$enabledCache);
            $data['enabledCache'] = $enabledCache;
        }

        return $this->view->render($this->getViewFileName('php'), $data, $this);
    }


    /**
     * @inheritdoc
     */
    public function admin()
    {
        $sections = Event::getConfigLandingSections();
        $text = '';
        foreach ($sections as $section => $config){
            $text.= "<strong>".$section."</strong> => ". "Ajax: <i>".\Yii::$app->formatter->asBoolean($config['ajax']).'</i>, cache: <i>'.\Yii::$app->formatter->asBoolean($config['cache'])."</i>, view: <i>".$config['view']."</i><br>";
        }
        return '<h4>Sezioni eventi</h4>'.$text;
    }

    /**
     * @param $model
     * @return array
     */
    public static function configsCache($model, $enabledCache = false)
    {
        return [
            'cache' => 'landingCache',
            'duration' => Event::CACHE_DURATION,
            'variations' => [
                Yii::$app->request->url,
                !empty($_COOKIE['cookie_privacy']) ? $_COOKIE['cookie_privacy'] : 'no_cookie',
                Yii::$app->language,
                Yii::$app->request->get(),
                Yii::$app->request->post(),
                Yii::$app->user->isGuest ? 'guest' : 'loggato'
            ],
            'dependency' => [
                'class' => 'yii\caching\DbDependency',
                'sql' => Event::CACHE_SQL_DEPENDENCY,
            ],
            'enabled' => $enabledCache
        ];
    }


}