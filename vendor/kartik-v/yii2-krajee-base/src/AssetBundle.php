<?php

/**
 * @package   yii2-krajee-base
 * @version   3.0.5
 */

namespace kartik\base;

use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * Asset bundle used for all Krajee extensions with bootstrap and jquery dependency.
 *
 */
class AssetBundle extends BaseAssetBundle implements BootstrapInterface
{
    use BootstrapTrait;

    /**
     * @var bool whether to enable the dependency with yii2 bootstrap asset bundle (depending on [[bsVersion]])
     */
    public $bsDependencyEnabled;

    /**
     * @var bool whether the bootstrap JS plugins are to be loaded and enabled
     */
    public $bsPluginEnabled = false;

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\YiiAsset',
    ];

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function init()
    {
        if (!isset($this->bsDependencyEnabled)) {
            $this->bsDependencyEnabled = ArrayHelper::getValue(Yii::$app->params, 'bsDependencyEnabled', true);
        }
        if ($this->bsDependencyEnabled) {
            $this->initBsAssets();
        }
        parent::init();
    }

    /**
     * Adds asset bundle dependency
     * @param  string  $lib
     */
    protected function addDependency($lib)
    {
        $index = array_search($lib, $this->depends);
        if ($index !== 0 && empty($index)) {
            $this->depends[] = $lib;
        }
    }

    /**
     * Initialize bootstrap assets dependencies
     */
    protected function initBsAssets()
    {
        $lib = $this->getBsExtBasename();
        $this->addDependency("yii\\{$lib}\\BootstrapAsset");
        if ($this->bsPluginEnabled) {
            $this->addDependency("yii\\{$lib}\\BootstrapPluginAsset");
        }
    }

    /**
     * Registers this asset bundle with a view after validating the bootstrap version
     * @param  View  $view  the view to be registered with
     * @param  string  $bsVer  the bootstrap version
     * @return static the registered asset bundle instance
     * @throws Exception
     */
    public static function registerBundle($view, $bsVer = null)
    {
        $currVer = ArrayHelper::getValue(Yii::$app->params, 'bsVersion', null);
        if (empty($bsVer) || static::isSameVersion($currVer, $bsVer)) {
            return static::register($view);
        }
        Yii::$app->params['bsVersion'] = $bsVer;
        $out = static::register($view);
        if (!empty($currVer)) {
            Yii::$app->params['bsVersion'] = $currVer;
        }

        return $out;
    }
}
