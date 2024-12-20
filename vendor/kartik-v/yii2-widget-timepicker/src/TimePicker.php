<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-timepicker
 * @version 1.0.5
 */

namespace kartik\time;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\base\InputWidget;

/**
 * The TimePicker widget  allows you to easily select a time for a text input using your mouse or keyboards arrow keys.
 * Thus widget is a wrapper enhancement over the TimePicker JQuery plugin by rendom forked from the plugin by jdewit.
 * Additional enhancements have been done to this input widget and plugin by Krajee to fix various bugs, and also
 * provide compatibility with Bootstrap 3.
 *
 * @since 1.0
 */
class TimePicker extends InputWidget
{
    /**
     * @var string the size of the input - 'lg', 'md', 'sm', 'xs'
     */
    public $size;

    /**
     * @var string|boolean the addon content
     */
    public $addon;

    /**
     * @var array HTML attributes for the addon container. The following special options are recognized:
     * - `asButton`: _boolean_, if the addon is to be displayed as a button.
     * - `buttonOptions`: _array_, HTML attributes if the addon is to be displayed like a button. If [[asButton]] is
     *   `true`, this will default to :
     *    - `['class' => 'btn btn-default']` for [[bsVersion]] = '3.x' or .
     *    - `['class' => 'btn btn-secondary']` for [[bsVersion]] = '4.x' and '5.x'
     */
    public $addonOptions = [];

    /**
     * @var array HTML attributes for the input group container
     */
    public $containerOptions = [];

    /**
     * @inheritdoc
     */
    public $pluginName = 'timepicker';

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function run()
    {
        $this->initIcon('up');
        $this->initIcon('down');
        $this->registerAssets();
        echo Html::tag('div', $this->renderInput(), $this->containerOptions);
    }

    /**
     * Initializes icon for time units up and down buttons
     * @param string $type whether 'up' or 'down'
     * @throws InvalidConfigException|\Exception
     */
    protected function initIcon($type)
    {
        $prop = $type . 'ArrowStyle';
        if (!isset($this->pluginOptions[$prop])) {
            $prefix = !$this->isBs(3) ? 'fas fa-' : 'glyphicon glyphicon-';
            $this->pluginOptions[$prop] = $prefix . 'chevron-' . $type;
        }
    }

    /**
     * Renders the input
     *
     * @return string
     * @throws InvalidConfigException|\Exception
     */
    protected function renderInput()
    {
        $notBs3 = !$this->isBs(3);
        $isBs5 = $this->isBs(5);
        if (!isset($this->addon)) {
            $this->addon = $notBs3 ? '<i class="far fa-clock"></i>' : '<i class="glyphicon glyphicon-time"></i>';
        }
        Html::addCssClass($this->options, 'form-control');
        if (!empty($this->options['disabled'])) {
            Html::addCssClass($this->addonOptions, 'disabled-addon');
        }
        if (ArrayHelper::getValue($this->pluginOptions, 'template', true) === false) {
            $css = $notBs3 ? 'bootstrap-timepicker4' : 'bootstrap-timepicker3';
            Html::addCssClass($this->containerOptions, ['bootstrap-timepicker', $css]);
            if (isset($this->size)) {
                Html::addCssClass($this->options, ($notBs3 ? 'form-control-' : 'input-') . $this->size);
                Html::addCssClass($this->addonOptions, 'inline-addon inline-addon-' . $this->size);
            } else {
                Html::addCssClass($this->addonOptions, 'inline-addon');
            }
            return $this->getInput('textInput') . Html::tag('span', $this->addon, $this->addonOptions);
        }
        Html::addCssClass($this->containerOptions, 'bootstrap-timepicker input-group');
        $asButton = ArrayHelper::remove($this->addonOptions, 'asButton', false);
        $buttonOptions = ArrayHelper::remove($this->addonOptions, 'buttonOptions', []);

        if ($asButton) {
            $css = $notBs3 ? 'input-group-append' : 'input-group-btn';
            $tag = $notBs3 ? 'div' : 'span';
            Html::addCssClass($this->addonOptions, [$css, 'picker']);
            $buttonOptions['type'] = 'button';
            if (empty($buttonOptions['class'])) {
                Html::addCssClass($buttonOptions, 'btn btn-default');
            }
            $button = Html::button($this->addon, $buttonOptions);
            $addon = $isBs5 ? $button : Html::tag($tag, $button, $this->addonOptions);
        } else {
            $css = $notBs3 ? 'input-group-text' : 'input-group-addon';
            Html::addCssClass($this->addonOptions, [$css, 'picker']);
            $addon = Html::tag('span', $this->addon, $this->addonOptions);
            if ($notBs3 && !$isBs5) {
                $addon = Html::tag('div', $addon, ['class' => 'input-group-append']);
            }
        }
        if (isset($this->size)) {
            Html::addCssClass($this->containerOptions, 'input-group-' . $this->size);
        }
        return $this->getInput('textInput') . $addon;
    }

    /**
     * Registers the client assets for [[Timepicker]] widget
     */
    public function registerAssets()
    {
        $view = $this->getView();
        TimePickerAsset::register($view);
        $this->registerPlugin($this->pluginName);
    }
}
