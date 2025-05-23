<?php

/**
 * @package   yii2-krajee-base
 * @version   3.0.5
 */

namespace kartik\base;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Html5Input widget is a widget encapsulating the HTML 5 inputs.
 *
 */
class Html5Input extends InputWidget
{
    use AddonTrait;

    /**
     * @var string the HTML 5 input type
     */
    public $type;

    /**
     * @var array the HTML attributes for the widget container
     */
    public $containerOptions = [];

    /**
     * @var array the HTML attributes for the HTML-5 input.
     */
    public $html5Options = [];

    /**
     * @var array the HTML attributes for the HTML-5 input container
     */
    public $html5Container = [];

    /**
     * @var string|boolean the message shown for unsupported browser. If set to false
     * will not be displayed
     */
    public $noSupport;

    /**
     * @var array the HTML attributes for container displaying unsupported browser message
     */
    public $noSupportOptions = [];

    /**
     * @var string one of the SIZE modifiers.
     * - For BS 3.x: 'lg', 'md', 'sm', 'xs'
     * - For BS 4.x: 'lg', 'sm'
     */
    public $size;

    /**
     * @var string the width in 'px' or '%' of the HTML5 input container. This property is DEPRECATED since
     * v1.9.4 and will not cause any change to behaviors. One can directly set the width and other CSS styles
     * via the [[html5Container]] property.
     */
    public $width;

    /**
     * @var array the list of allowed HTML input types.
     */
    private static $_allowedInputTypes = [
        'color',
        'range',
        'text',
        'hidden',
    ];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->initInput();
    }

    /**
     * Initializes the input.
     */
    protected function initInput()
    {
        $this->initDisability($this->html5Options);
        if (in_array($this->type, self::$_allowedInputTypes)) {
            $this->html5Options['id'] = $this->options['id'] . '-source';
            $this->registerAssets();
            echo $this->renderInput();
        } else {
            ArrayHelper::merge($this->options, $this->html5Options);
            if (isset($this->size)) {
                Html::addCssClass($this->options, ['class' => 'input-' . $this->size]);
            }
            echo $this->getHtml5Input();
        }
    }

    /**
     * Registers the needed assets for [[Html5Input]] widget.
     */
    public function registerAssets()
    {
        $view = $this->getView();
        Html5InputAsset::register($view);
        $idCap = '#' . $this->options['id'];
        $idInp = '#' . $this->html5Options['id'];
        $this->registerWidgetJs("kvInitHtml5('{$idCap}','{$idInp}');");
    }

    /**
     * Renders the special HTML5 input. Mainly useful for the color and range inputs
     */
    protected function renderInput()
    {
        Html::addCssClass($this->options, 'form-control');
        $isBs3 = $this->isBs(3);
        $n = $isBs3 ? 3 : 4;
        Html::addCssClass($this->containerOptions,
            ['input-group', 'input-group-html5', "kv-type-{$this->type}", "is-bs{$n}"]);
        if (!empty($this->size)) {
            Html::addCssClass($this->containerOptions, "input-group-{$this->size}");
        }
        $prepend = $this->getAddonContent('prepend');
        $preCaption = $this->getAddonContent('preCaption');
        $append = $this->getAddonContent('append');
        $caption = $this->getInput('textInput');
        $value = $this->hasModel() ? Html::getAttributeValue($this->model, $this->attribute) : $this->value;
        Html::addCssClass($this->html5Options, 'form-control-' . $this->type);
        $input = Html::input($this->type, $this->html5Options['id'], $value, $this->html5Options);
        if ($isBs3) {
            Html::addCssClass($this->html5Container, ['input-group-addon']);
            $prepend .= Html::tag('span', $input, $this->html5Container);
        } else {
            Html::addCssClass($this->html5Container, 'input-group-text');
            $out = Html::tag('span', $input, $this->html5Container);
            $prepend .= $this->isBs(4) ? Html::tag('span', $out, ['class' => 'input-group-prepend']) : $out;
        }
        $content = Html::tag('div', $prepend . $preCaption . $caption . $append, $this->containerOptions);
        Html::addCssClass($this->noSupportOptions, 'alert alert-warning');
        if ($this->noSupport === false) {
            $message = '';
        } else {
            $noSupport = !empty($this->noSupport) ? $this->noSupport :
                Yii::t(
                    'kvbase',
                    'It is recommended you use an upgraded browser to display the {type} control properly.',
                    ['type' => $this->type]
                );
            $message = "\n<br>" . Html::tag('div', $noSupport, $this->noSupportOptions);
        }
        return "<!--[if lt IE 10]>\n{$caption}{$message}\n<![endif]--><![if gt IE 9]>\n{$content}\n<![endif]>";
    }

    /**
     * Gets the HTML5 input.
     *
     * @return string
     */
    protected function getHtml5Input()
    {
        if ($this->hasModel()) {
            return Html::activeInput($this->type, $this->model, $this->attribute, $this->options);
        }
        return Html::input($this->type, $this->name, $this->value, $this->options);
    }
}
