<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\dashboard\widgets
 * @category   CategoryName
 */

namespace open20\amos\dashboard\widgets;

use Yii;
use yii\base\Widget;
use open20\amos\dashboard\AmosDashboard;
use open20\amos\dashboard\models\search\AmosWidgetsSearch;
use open20\amos\dashboard\models\AmosWidgets;
use open20\amos\dashboard\assets\SubDashboardAsset;
use yii\db\ActiveQuery;

/**
 * Class SubDashboardWidget
 * @package open20\amos\dashboard\widgets
 */
class SubDashboardWidget extends Widget
{
    public $model;
    public $module;
    public $sub_dashboard     = 1;
    public $widgets_type      = 'ICON';
    public $classDivGraphic   = 'grid-item';
    public $graphicCustomSize = true;

    /**
     * widget initialization
     */
    public function init()
    {
        parent::init();

        if (empty($this->model)) {
            throw new \Exception(AmosDashboard::t('amosdashboard', 'Missing model'));
        }
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $widgets = '';
        if (empty($this->module)) {
            $this->module = $this->getModule();
            if (!empty($this->module)) {
                if ($this->widgets_type == AmosWidgets::TYPE_ICON) {
                    $widgets = AmosWidgetsSearch::selectableIcon($this->sub_dashboard, $this->module, true);
                    if ($widgets->count()) {
                        foreach ($widgets->all() as $value) {
                            if ($value->classname != 'open20\amos\admin\widgets\icons\WidgetIconUserProfile' &&
                                $value->classname != 'open20\amos\community\widgets\icons\WidgetIconCommunityDashboard'
                                &&
                                (
                                empty(\Yii::$app->params['isPoi']) || (!empty(\Yii::$app->params['isPoi']) && \Yii::$app->params['isPoi']
                                == false) || $this->model->className() != '\open20\amos\community\models\Community'
                                || ($value->classname != '\open20\amos\projectmanagement\widgets\icons\WidgetIconprojects'
                                /* && $value->classname !=  '\open20\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard' */)
                                || ( $value->classname == '\open20\amos\projectmanagement\widgets\icons\WidgetIconprojects'
                                && ($this->model->id != 2751 && $this->model->id != 2754 && $this->model->id != 2750))
                                /* || ($value->classname ==  '\open20\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard'
                                  && ($this->model->id != 2751)) */
                                )) {
                                $widget = Yii::createObject($value->classname);
                                echo $widget::widget();
                            }
                        }
                    }
                } else if ($this->widgets_type == AmosWidgets::TYPE_GRAPHIC) {
                    $widgets = AmosWidgetsSearch::selectableGraphic($this->sub_dashboard, $this->module, true);
                    $this->runHtml($widgets);
                }
            }
        }
    }

    /**
     * This method render the widget graphics
     * @param ActiveQuery $widgets
     * @throws \yii\base\InvalidConfigException
     */
    protected function runHtml($widgets)
    {
        $moduleL         = \Yii::$app->getModule('layout');
        $layoutModuleSet = isset($moduleL);
        $showWidgets     = [];

        if ($widgets->count()) {
            if ($this->graphicCustomSize) {
                foreach ($widgets->all() as $value) {
                    if ($this->model->className() != '\open20\amos\community\models\Community' || ($value->classname
                        != '\open20\amos\documenti\widgets\graphics\WidgetGraphicsUltimiDocumenti') || ($value->classname
                        == '\open20\amos\documenti\widgets\graphics\WidgetGraphicsUltimiDocumenti' && ($this->model->id
                        != 2751))) {

                        $widgetGraphic = Yii::createObject($value->classname);
                        if (!empty($widgetGraphic->classFullSize)) {
                            array_unshift($showWidgets, $widgetGraphic);
                        } else {
                            $showWidgets[] = $widgetGraphic;
                        }
                    }
                }
            }
            SubDashboardAsset::register(\Yii::$app->getView());
            echo '<div id="bk-pluginGrafici" class="sub-dashboard-graphics">'.
            '<div class="graphics-dashboard-container">'.
            '<div id="widgets-graphic" class="widgets-graphic-sortable">'.
            '<div class="grid">'.
            '<div class="grid-sizer"></div>';
            foreach ($showWidgets as $widget) {
                echo '<div '.(($layoutModuleSet) ? '' : 'class="'.$this->classDivGraphic.'');
                if ($this->graphicCustomSize === true) {
                    if (!empty($widget->classFullSize)) {
                        echo ' '.$widget->classFullSize;
                    }
                }
                echo (($layoutModuleSet) ? '' : '" ').
                ' data-code="'.$widget::classname().'" data-module-name="'.$widget->moduleName.'">'.$widget::widget().'</div>';
            }
            echo '</div>'.
            '</div>'.
            '</div>'.
            '</div>';
        }
    }

    /**
     * @return string|null
     */
    protected function getModule()
    {
        $module = null;
        if (!empty($this->model->context)) {
            try {
                $modelContext = \Yii::createObject($this->model->context);
                if (method_exists($modelContext, 'getPluginModule')) {
                    $module = $modelContext->getPluginModule();
                } else {
                    $modelArray = explode('\\', $this->model->context);
                    if (!empty(\Yii::$app->getModule('dashboard')->modulesSubdashboard)) {
                        $modules = \Yii::$app->getModule('dashboard')->modulesSubdashboard;
                        $module  = $this->getModuleByModel($modelArray, $modules);
                    } else {
                        $module = $this->getModuleByApp($modelArray);
                    }
                }
            } catch (\Exception $e) {
                $modelArray = explode('\\', $this->model->context);
                $module     = $this->getModuleByApp($modelArray);
            }
        }
        return $module;
    }

    /**
     * @param array $arr_model
     * @param array $modules
     * @return string|null
     */
    protected function getModuleByModel($arr_model, $modules)
    {
        $module = null;
        foreach ($modules as $k => $v) {
            if (in_array($v, $arr_model)) {
                $module = $v;
                continue;
            }
        }
        return $module;
    }

    /**
     * @param array $arr_model
     * @return string|null
     */
    protected function getModuleByApp($arr_model)
    {
        $module  = null;
        $modules = \Yii::$app->getModules(false);
        foreach ($modules as $k => $v) {
            if (in_array($k, $arr_model)) {
                $module = $k;
                continue;
            }
        }
        return $module;
    }
}